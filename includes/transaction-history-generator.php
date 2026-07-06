<?php

class TransactionHistoryGenerator
{
    public const DENSITY_COUNTS = [
        'light' => 25,
        'normal' => 70,
        'heavy' => 150,
    ];

    private Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    public function getDefaultTemplate(): ?array
    {
        $stmt = $this->db->query(
            "SELECT * FROM transaction_templates WHERE slug = 'default_checking' AND is_active = 1 LIMIT 1"
        );
        return $stmt ? $stmt->fetch() : null;
    }

    public function loadTemplateItems(int $templateId): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM transaction_template_items WHERE template_id = ? ORDER BY sort_order ASC",
            [$templateId]
        );
        return $stmt ? $stmt->fetchAll() : [];
    }

    public function computeParamsHash(array $params): string
    {
        $payload = [
            'account_id' => (int)($params['account_id'] ?? 0),
            'start_date' => $params['start_date'] ?? '',
            'end_date' => $params['end_date'] ?? '',
            'density' => $params['density'] ?? 'normal',
            'history_impact' => round((float)($params['history_impact'] ?? 0), 2),
            'template_id' => (int)($params['template_id'] ?? 0),
        ];

        return hash('sha256', json_encode($payload, JSON_UNESCAPED_SLASHES));
    }

    public function findDuplicateBatch(int $accountId, string $paramsHash): ?array
    {
        $stmt = $this->db->query(
            "SELECT batch_id, created_at, transaction_count
             FROM transaction_generation_batches
             WHERE account_id = ? AND params_hash = ? AND status = 'completed'
             ORDER BY id DESC LIMIT 1",
            [$accountId, $paramsHash]
        );
        $row = $stmt ? $stmt->fetch() : false;
        return $row ?: null;
    }

    public function loadExistingTransactions(int $accountId, ?string $startDate = null, ?string $endDate = null): array
    {
        $sql = "SELECT id, created_at, transaction_ref, amount, transaction_type, status
                FROM transactions WHERE account_id = ?";
        $params = [$accountId];

        if ($startDate && $endDate) {
            $sql .= " AND DATE(created_at) BETWEEN ? AND ?";
            $params[] = $startDate;
            $params[] = $endDate;
        }

        $sql .= " ORDER BY created_at ASC";
        $stmt = $this->db->query($sql, $params);
        return $stmt ? $stmt->fetchAll() : [];
    }

    public function sumCompletedBatchImpact(int $accountId): float
    {
        $stmt = $this->db->query(
            "SELECT COALESCE(SUM(history_impact), 0) AS total
             FROM transaction_generation_batches
             WHERE account_id = ? AND status = 'completed'",
            [$accountId]
        );
        return round((float)(($stmt ? $stmt->fetch() : [])['total'] ?? 0), 2);
    }

    public function resolveAnchorBalances(float $previousBalance, float $historyImpact, bool $replacePrevious, int $accountId): array
    {
        $replacedImpactSum = $replacePrevious ? $this->sumCompletedBatchImpact($accountId) : 0.0;
        $anchorAfter = round($previousBalance - $replacedImpactSum, 2);
        $targetFinal = round($anchorAfter + $historyImpact, 2);
        $genOpening = round($anchorAfter - $historyImpact, 2);

        return [
            'anchor_after' => $anchorAfter,
            'target_final_balance' => $targetFinal,
            'gen_opening_balance' => $genOpening,
            'replaced_impact_sum' => $replacedImpactSum,
        ];
    }

    public function buildWarnings(int $accountId, string $startDate, string $endDate): array
    {
        $warnings = [];

        $afterStmt = $this->db->query(
            "SELECT COUNT(*) AS cnt FROM transactions
             WHERE account_id = ? AND DATE(created_at) > ?",
            [$accountId, $endDate]
        );
        $afterCount = (int)(($afterStmt ? $afterStmt->fetch() : [])['cnt'] ?? 0);
        if ($afterCount > 0) {
            $warnings[] = sprintf(
                '%d existing transaction(s) after end date will remain as most recent',
                $afterCount
            );
        }

        $overlapStmt = $this->db->query(
            "SELECT COUNT(*) AS cnt FROM transactions
             WHERE account_id = ? AND DATE(created_at) BETWEEN ? AND ?
               AND transaction_ref NOT LIKE 'GEN-ACC%'",
            [$accountId, $startDate, $endDate]
        );
        $overlapCount = (int)(($overlapStmt ? $overlapStmt->fetch() : [])['cnt'] ?? 0);
        if ($overlapCount > 0) {
            $warnings[] = sprintf(
                '%d existing non-generated transaction(s) overlap the selected date range',
                $overlapCount
            );
        }

        return $warnings;
    }

    public function sampleAndSchedule(array $templateItems, string $density, string $startDate, string $endDate, string $seed): array
    {
        $count = self::DENSITY_COUNTS[$density] ?? self::DENSITY_COUNTS['normal'];
        if ($count <= 0 || empty($templateItems)) {
            return [];
        }

        $rng = $this->seedRandom($seed);
        $picked = [];

        for ($i = 0; $i < $count; $i++) {
            $picked[] = $this->weightedPick($templateItems, $rng);
        }

        $dates = $this->scheduleDates($count, $startDate, $endDate, $rng);
        foreach ($picked as $idx => &$item) {
            $item = $this->cloneTemplateItem($item);
            $item['scheduled_at'] = $dates[$idx];
        }
        unset($item);

        usort($picked, function ($a, $b) {
            return strcmp($a['scheduled_at'], $b['scheduled_at']);
        });

        return $picked;
    }

    public function computeNetMovement(array $items): float
    {
        $net = 0.0;
        foreach ($items as $item) {
            if (($item['status'] ?? 'completed') !== 'completed') {
                continue;
            }
            $amount = round((float)$item['amount'], 2);
            $fee = round((float)($item['fee'] ?? 0), 2);
            if (($item['transaction_type'] ?? '') === 'credit') {
                $net += $amount;
            } else {
                $net -= ($amount + $fee);
            }
        }
        return round($net, 2);
    }

    public function scaleToHistoryImpact(array $items, float $historyImpact): array
    {
        $templateNet = $this->computeNetMovement($items);
        if (abs($templateNet) < 0.00001) {
            throw new InvalidArgumentException('Template sample net is zero; cannot scale to history impact.');
        }

        $factor = $historyImpact / $templateNet;
        $scaled = [];
        foreach ($items as $item) {
            $row = $item;
            $row['amount'] = round((float)$item['amount'] * $factor, 2);
            $row['fee'] = round((float)($item['fee'] ?? 0) * abs($factor), 2);
            $scaled[] = $row;
        }

        $actualNet = $this->computeNetMovement($scaled);
        $diff = round($historyImpact - $actualNet, 2);
        if (abs($diff) >= 0.01) {
            $scaled = $this->adjustNetDifference($scaled, $diff);
        }

        return $scaled;
    }

    public function buildBalanceChain(array $items, float $genOpening): array
    {
        $balance = round($genOpening, 2);
        $result = [];

        foreach ($items as $item) {
            $before = $balance;
            $after = $before;

            if (($item['status'] ?? 'completed') === 'completed') {
                $amount = round((float)$item['amount'], 2);
                $fee = round((float)($item['fee'] ?? 0), 2);
                if (($item['transaction_type'] ?? '') === 'credit') {
                    $after = round($before + $amount, 2);
                } else {
                    $after = round($before - $amount - $fee, 2);
                }
                $balance = $after;
            }

            $item['balance_before'] = $before;
            $item['balance_after'] = $after;
            $result[] = $item;
        }

        return $result;
    }

    public function preview(array $params): array
    {
        $this->validateParams($params, false);

        $account = $this->getAccount((int)$params['account_id'], (int)$params['user_id']);
        $previousBalance = round((float)$account['balance'], 2);
        $historyImpact = round((float)$params['history_impact'], 2);
        $replacePrevious = !empty($params['replace_previous']);
        $anchors = $this->resolveAnchorBalances($previousBalance, $historyImpact, $replacePrevious, (int)$params['account_id']);
        $targetFinal = $anchors['target_final_balance'];
        $genOpening = $anchors['gen_opening_balance'];

        $templateId = (int)($params['template_id'] ?? 0);
        if (!$templateId) {
            $template = $this->getDefaultTemplate();
            $templateId = $template ? (int)$template['id'] : 0;
        }
        if (!$templateId) {
            throw new RuntimeException('No active transaction template found.');
        }

        $templateItems = $this->loadTemplateItems($templateId);
        $seed = (string)($params['preview_seed'] ?? $params['idempotency_key'] ?? uniqid('preview_', true));
        $sampled = $this->sampleAndSchedule(
            $templateItems,
            $params['density'],
            $params['start_date'],
            $params['end_date'],
            $seed
        );

        foreach ($sampled as &$row) {
            $row['amount'] = round((float)$row['base_amount'], 2);
        }
        unset($row);

        $scaled = $this->scaleToHistoryImpact($sampled, $historyImpact);
        $chained = $this->buildBalanceChain($scaled, $genOpening);
        $paramsHash = $this->computeParamsHash(array_merge($params, [
            'template_id' => $templateId,
            'history_impact' => $historyImpact,
        ]));
        $duplicate = $this->findDuplicateBatch((int)$params['account_id'], $paramsHash);

        $warnings = $this->buildWarnings((int)$params['account_id'], $params['start_date'], $params['end_date']);
        if ($replacePrevious && abs($anchors['replaced_impact_sum']) >= 0.01) {
            $warnings[] = sprintf(
                'Replace mode: %s of prior generated impact will be reversed before applying new history.',
                formatCurrency(
                    $anchors['replaced_impact_sum'],
                    $account['currency'] ?? DEFAULT_CURRENCY,
                    $account['currency'] ?? DEFAULT_CURRENCY
                )
            );
        }

        return [
            'success' => true,
            'transaction_count' => count($chained),
            'previous_balance' => $previousBalance,
            'history_impact' => $historyImpact,
            'new_account_balance' => $targetFinal,
            'gen_opening_balance' => $genOpening,
            'anchor_after' => $anchors['anchor_after'],
            'replaced_impact_sum' => $anchors['replaced_impact_sum'],
            'sample_transactions' => $this->formatSampleTransactions(array_slice($chained, 0, 5), $account['currency'] ?? DEFAULT_CURRENCY),
            'warnings' => $warnings,
            'duplicate_batch_warning' => $duplicate ? [
                'batch_id' => $duplicate['batch_id'],
                'message' => 'An identical generation already exists for this account.',
            ] : null,
            'preview_seed' => $seed,
            'params_hash' => $paramsHash,
            'template_id' => $templateId,
        ];
    }

    public function generate(array $params): array
    {
        $this->validateParams($params, true);

        $idempotencyKey = trim((string)$params['idempotency_key']);
        $existing = $this->findBatchByIdempotency($idempotencyKey);
        if ($existing) {
            return [
                'success' => true,
                'message' => 'Generation already completed for this idempotency key.',
                'batch_id' => $existing['batch_id'],
                'duplicate' => true,
            ];
        }

        $preview = $this->preview($params);
        $accountId = (int)$params['account_id'];
        $userId = (int)$params['user_id'];
        $adminId = (int)$params['admin_id'];
        $historyImpact = (float)$preview['history_impact'];
        $previousBalance = (float)$preview['previous_balance'];
        $templateId = (int)$preview['template_id'];
        $seed = (string)($params['preview_seed'] ?? $params['idempotency_key']);
        $replacePrevious = !empty($params['replace_previous']);

        $templateItems = $this->loadTemplateItems($templateId);
        $sampled = $this->sampleAndSchedule(
            $templateItems,
            $params['density'],
            $params['start_date'],
            $params['end_date'],
            $seed
        );
        foreach ($sampled as &$row) {
            $row['amount'] = round((float)$row['base_amount'], 2);
        }
        unset($row);

        $scaled = $this->scaleToHistoryImpact($sampled, $historyImpact);

        $batchId = $this->makeBatchId($accountId);

        $this->db->beginTransaction();
        try {
            $acctStmt = $this->db->query(
                "SELECT id, user_id, balance, available_balance, currency FROM accounts WHERE id = ? AND user_id = ? FOR UPDATE",
                [$accountId, $userId]
            );
            $account = $acctStmt ? $acctStmt->fetch() : false;
            if (!$account) {
                throw new RuntimeException('Account not found for user.');
            }

            $lockedPrevious = round((float)$account['balance'], 2);
            if (abs($lockedPrevious - $previousBalance) > 0.01) {
                throw new RuntimeException('Account balance changed during generation. Preview again.');
            }

            $replacedPrevious = 0;
            $replacedImpactSum = 0.0;
            if ($replacePrevious) {
                $replacedImpactSum = $this->sumCompletedBatchImpact($accountId);
                $delStmt = $this->db->query(
                    "DELETE FROM transactions WHERE account_id = ? AND transaction_ref LIKE ?",
                    [$accountId, 'GEN-ACC' . $accountId . '-%']
                );
                if ($delStmt) {
                    $replacedPrevious = $delStmt->rowCount();
                }
                $this->db->query(
                    "UPDATE transaction_generation_batches SET status = 'undone', updated_at = NOW()
                     WHERE account_id = ? AND status = 'completed'",
                    [$accountId]
                );
                if (abs($replacedImpactSum) >= 0.01) {
                    $this->db->query(
                        "UPDATE accounts SET balance = balance - ?, available_balance = available_balance - ?, updated_at = NOW() WHERE id = ?",
                        [$replacedImpactSum, $replacedImpactSum, $accountId]
                    );
                    $lockedPrevious = round($lockedPrevious - $replacedImpactSum, 2);
                }
            }

            $anchors = $this->resolveAnchorBalances($lockedPrevious, $historyImpact, false, $accountId);
            $targetFinal = $anchors['target_final_balance'];
            $genOpening = $anchors['gen_opening_balance'];
            $chained = $this->buildBalanceChain($scaled, $genOpening);

            $currency = $account['currency'] ?? DEFAULT_CURRENCY;
            $seq = 1;
            foreach ($chained as $row) {
                $ref = sprintf('GEN-ACC%d-%s-%03d', $accountId, $batchId, $seq);
                $metadata = json_encode([
                    'generator' => true,
                    'generator_batch_id' => $batchId,
                    'admin_id' => $adminId,
                    'template_id' => $templateId,
                ], JSON_UNESCAPED_SLASHES);

                $completedAt = ($row['status'] ?? 'completed') === 'completed' ? $row['scheduled_at'] : null;
                $this->db->query(
                    "INSERT INTO transactions (
                        transaction_ref, user_id, account_id, transaction_type, category, expense_category,
                        amount, currency, balance_before, balance_after, description,
                        recipient_account, recipient_name, recipient_bank,
                        status, payment_method, fee, exchange_rate, metadata, ip_address, created_at, completed_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, ?, NULL, ?, ?, ?, ?)",
                    [
                        $ref,
                        $userId,
                        $accountId,
                        $row['transaction_type'],
                        $row['category'],
                        $row['expense_category'] ?? null,
                        $row['amount'],
                        $currency,
                        $row['balance_before'],
                        $row['balance_after'],
                        $row['description'],
                        $row['recipient_account'] ?? null,
                        $row['recipient_name'] ?? null,
                        $row['recipient_bank'] ?? null,
                        $row['status'] ?? 'completed',
                        $row['fee'] ?? 0,
                        $metadata,
                        $_SERVER['REMOTE_ADDR'] ?? null,
                        $row['scheduled_at'],
                        $completedAt,
                    ]
                );
                $seq++;
            }

            $this->db->query(
                "UPDATE accounts SET balance = ?, available_balance = ?, updated_at = NOW() WHERE id = ?",
                [$targetFinal, $targetFinal, $accountId]
            );

            $this->db->query(
                "INSERT INTO transaction_generation_batches (
                    batch_id, idempotency_key, params_hash, admin_id, user_id, account_id,
                    template_id, density, start_date, end_date, previous_balance, history_impact,
                    target_final_balance, opening_balance, transaction_count, replaced_previous, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'completed')",
                [
                    $batchId,
                    $idempotencyKey,
                    $preview['params_hash'],
                    $adminId,
                    $userId,
                    $accountId,
                    $templateId,
                    $params['density'],
                    $params['start_date'],
                    $params['end_date'],
                    $previousBalance,
                    $historyImpact,
                    $targetFinal,
                    $genOpening,
                    count($chained),
                    $replacedPrevious ? 1 : 0,
                ]
            );

            $this->db->query(
                "INSERT INTO admin_logs (admin_id, user_id, action, description, created_at)
                 VALUES (?, ?, 'transaction_history_generate', ?, NOW())",
                [
                    $adminId,
                    $userId,
                    sprintf(
                        'Generated %d transactions (batch %s) for account %d; history impact %s; balance %s -> %s',
                        count($chained),
                        $batchId,
                        $accountId,
                        $historyImpact,
                        $previousBalance,
                        $targetFinal
                    ),
                ]
            );

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Transaction history generated successfully.',
                'batch_id' => $batchId,
                'transaction_count' => count($chained),
                'previous_balance' => $previousBalance,
                'new_account_balance' => $targetFinal,
                'history_impact' => $historyImpact,
            ];
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }
            throw $e;
        }
    }

    public function countPostBatchActivity(int $accountId, string $batchCreatedAt, string $batchId): int
    {
        $stmt = $this->db->query(
            "SELECT COUNT(*) AS cnt FROM transactions
             WHERE account_id = ?
               AND created_at > ?
               AND (
                 transaction_ref NOT LIKE ?
                 OR JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.generator_batch_id')) <> ?
               )",
            [
                $accountId,
                $batchCreatedAt,
                'GEN-ACC' . $accountId . '-%',
                $batchId,
            ]
        );
        return (int)(($stmt ? $stmt->fetch() : [])['cnt'] ?? 0);
    }

    public function undoBatch(string $batchId, bool $confirmWithActivity = false): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM transaction_generation_batches WHERE batch_id = ? AND status = 'completed' LIMIT 1",
            [$batchId]
        );
        $batch = $stmt ? $stmt->fetch() : false;
        if (!$batch) {
            throw new RuntimeException('Batch not found or already undone.');
        }

        $accountId = (int)$batch['account_id'];
        $activityCount = $this->countPostBatchActivity(
            $accountId,
            $batch['created_at'],
            $batchId
        );

        if ($activityCount > 0 && !$confirmWithActivity) {
            return [
                'success' => false,
                'blocked' => true,
                'http_status' => 409,
                'message' => 'Additional account activity exists after this generation. Undo cannot safely restore the original balance.',
                'activity_count' => $activityCount,
                'requires_confirmation' => true,
                'current_balance' => $this->getAccountBalance($accountId),
                'history_impact' => (float)$batch['history_impact'],
                'previous_balance' => (float)$batch['previous_balance'],
            ];
        }

        $this->db->beginTransaction();
        try {
            $acctStmt = $this->db->query(
                "SELECT balance, available_balance FROM accounts WHERE id = ? FOR UPDATE",
                [$accountId]
            );
            $account = $acctStmt ? $acctStmt->fetch() : false;
            if (!$account) {
                throw new RuntimeException('Account not found.');
            }

            $currentBalance = round((float)$account['balance'], 2);
            $historyImpact = round((float)$batch['history_impact'], 2);
            $previousBalance = round((float)$batch['previous_balance'], 2);

            if ($activityCount > 0) {
                $newBalance = round($currentBalance - $historyImpact, 2);
            } else {
                $newBalance = $previousBalance;
            }

            $this->db->query(
                "DELETE FROM transactions
                 WHERE account_id = ?
                   AND JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.generator_batch_id')) = ?",
                [$accountId, $batchId]
            );

            $this->db->query(
                "UPDATE accounts SET balance = ?, available_balance = ?, updated_at = NOW() WHERE id = ?",
                [$newBalance, $newBalance, $accountId]
            );

            $this->db->query(
                "UPDATE transaction_generation_batches SET status = 'undone', updated_at = NOW() WHERE batch_id = ?",
                [$batchId]
            );

            $this->db->query(
                "INSERT INTO admin_logs (admin_id, user_id, action, description, created_at)
                 VALUES (?, ?, 'transaction_history_undo', ?, NOW())",
                [
                    (int)($_SESSION['user_id'] ?? 0),
                    (int)$batch['user_id'],
                    sprintf(
                        'Undid batch %s for account %d; balance %s -> %s%s',
                        $batchId,
                        $accountId,
                        $currentBalance,
                        $newBalance,
                        $activityCount > 0 ? ' (forced; post-batch activity preserved)' : ''
                    ),
                ]
            );

            $this->db->commit();

            return [
                'success' => true,
                'message' => $activityCount > 0
                    ? 'Generated history removed. Balance adjusted by reversing history impact.'
                    : 'Generated history removed and balance restored.',
                'batch_id' => $batchId,
                'previous_balance_before_undo' => $currentBalance,
                'new_balance' => $newBalance,
                'forced' => $activityCount > 0,
            ];
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }
            throw $e;
        }
    }

    public function listBatches(?int $accountId = null, int $limit = 50): array
    {
        $sql = "SELECT b.*, u.full_name, u.email, a.account_number
                FROM transaction_generation_batches b
                JOIN users u ON b.user_id = u.id
                JOIN accounts a ON b.account_id = a.id
                WHERE 1=1";
        $params = [];

        if ($accountId) {
            $sql .= " AND b.account_id = ?";
            $params[] = $accountId;
        }

        $sql .= " ORDER BY b.created_at DESC LIMIT " . max(1, min(200, $limit));
        $stmt = $this->db->query($sql, $params);
        return $stmt ? $stmt->fetchAll() : [];
    }

    private function validateParams(array $params, bool $requireIdempotency): void
    {
        $required = ['user_id', 'account_id', 'start_date', 'end_date', 'density', 'history_impact'];
        if ($requireIdempotency) {
            $required[] = 'idempotency_key';
        }

        foreach ($required as $field) {
            if (!isset($params[$field]) || $params[$field] === '') {
                throw new InvalidArgumentException("Missing required field: {$field}");
            }
        }

        if (!in_array($params['density'], array_keys(self::DENSITY_COUNTS), true)) {
            throw new InvalidArgumentException('Invalid density.');
        }

        $historyImpact = round((float)$params['history_impact'], 2);
        if (abs($historyImpact) < 0.01) {
            throw new InvalidArgumentException('History impact must be non-zero.');
        }

        if (strtotime($params['start_date']) === false || strtotime($params['end_date']) === false) {
            throw new InvalidArgumentException('Invalid date range.');
        }

        if ($params['start_date'] > $params['end_date']) {
            throw new InvalidArgumentException('Start date must be on or before end date.');
        }
    }

    private function getAccount(int $accountId, int $userId): array
    {
        $stmt = $this->db->query(
            "SELECT id, user_id, balance, available_balance, currency, account_number, account_type
             FROM accounts WHERE id = ? AND user_id = ? LIMIT 1",
            [$accountId, $userId]
        );
        $account = $stmt ? $stmt->fetch() : false;
        if (!$account) {
            throw new RuntimeException('Account not found for user.');
        }
        return $account;
    }

    private function getAccountBalance(int $accountId): float
    {
        $stmt = $this->db->query("SELECT balance FROM accounts WHERE id = ? LIMIT 1", [$accountId]);
        $row = $stmt ? $stmt->fetch() : false;
        return $row ? round((float)$row['balance'], 2) : 0.0;
    }

    private function findBatchByIdempotency(string $key): ?array
    {
        $stmt = $this->db->query(
            "SELECT batch_id FROM transaction_generation_batches WHERE idempotency_key = ? LIMIT 1",
            [$key]
        );
        $row = $stmt ? $stmt->fetch() : false;
        return $row ?: null;
    }

    private function makeBatchId(int $accountId): string
    {
        return date('YmdHis') . substr(md5(uniqid((string)$accountId, true)), 0, 8);
    }

    private function seedRandom(string $seed): callable
    {
        $state = crc32($seed);
        return function () use (&$state) {
            $state = ($state * 1103515245 + 12345) & 0x7fffffff;
            return $state / 0x7fffffff;
        };
    }

    private function weightedPick(array $items, callable $rng): array
    {
        $totalWeight = 0;
        foreach ($items as $item) {
            $totalWeight += max(1, (int)($item['weight'] ?? 1));
        }
        $pick = $rng() * $totalWeight;
        $running = 0.0;
        foreach ($items as $item) {
            $running += max(1, (int)($item['weight'] ?? 1));
            if ($pick <= $running) {
                return $item;
            }
        }
        return $items[array_key_last($items)];
    }

    private function cloneTemplateItem(array $item): array
    {
        return [
            'transaction_type' => $item['transaction_type'],
            'category' => $item['category'],
            'expense_category' => $item['expense_category'] ?? null,
            'base_amount' => (float)$item['base_amount'],
            'amount' => (float)$item['base_amount'],
            'description' => $item['description'],
            'recipient_account' => $item['recipient_account'] ?? null,
            'recipient_name' => $item['recipient_name'] ?? null,
            'recipient_bank' => $item['recipient_bank'] ?? null,
            'status' => $item['status'] ?? 'completed',
            'fee' => (float)($item['fee'] ?? 0),
        ];
    }

    private function scheduleDates(int $count, string $startDate, string $endDate, callable $rng): array
    {
        $start = new DateTimeImmutable($startDate . ' 00:00:00');
        $end = new DateTimeImmutable($endDate . ' 23:59:59');
        $days = max(1, (int)$start->diff($end)->days + 1);
        $dates = [];

        for ($i = 0; $i < $count; $i++) {
            $attempts = 0;
            do {
                $offset = (int)floor($rng() * $days);
                $candidate = $start->modify('+' . $offset . ' days');
                $dow = (int)$candidate->format('N');
                $isWeekend = $dow >= 6;
                $accept = !$isWeekend || $rng() < 0.35;
                $attempts++;
            } while (!$accept && $attempts < 12);

            $hour = 8 + (int)floor($rng() * 10);
            $minute = (int)floor($rng() * 60);
            $second = (int)floor($rng() * 60);
            $dates[] = $candidate->setTime($hour, $minute, $second)->format('Y-m-d H:i:s');
        }

        sort($dates);
        return $dates;
    }

    private function adjustNetDifference(array $items, float $diff): array
    {
        for ($i = count($items) - 1; $i >= 0; $i--) {
            if (($items[$i]['status'] ?? 'completed') !== 'completed') {
                continue;
            }
            if (($items[$i]['transaction_type'] ?? '') === 'credit' && $diff > 0) {
                $items[$i]['amount'] = round((float)$items[$i]['amount'] + $diff, 2);
                return $items;
            }
            if (($items[$i]['transaction_type'] ?? '') === 'debit' && $diff < 0) {
                $items[$i]['amount'] = round((float)$items[$i]['amount'] + abs($diff), 2);
                return $items;
            }
        }

        if (!empty($items)) {
            $idx = count($items) - 1;
            if (($items[$idx]['transaction_type'] ?? '') === 'credit') {
                $items[$idx]['amount'] = round((float)$items[$idx]['amount'] + $diff, 2);
            } else {
                $items[$idx]['amount'] = round(max(0.01, (float)$items[$idx]['amount'] - $diff), 2);
            }
        }

        return $items;
    }

    private function formatSampleTransactions(array $rows, string $currency): array
    {
        $samples = [];
        foreach ($rows as $row) {
            $isCredit = ($row['transaction_type'] ?? '') === 'credit';
            $amount = (float)$row['amount'];
            $sign = $isCredit ? '+' : '-';
            $samples[] = [
                'date' => $row['scheduled_at'] ?? '',
                'description' => $row['description'] ?? '',
                'amount' => round($amount, 2),
                'type' => $row['transaction_type'] ?? '',
                'status' => $row['status'] ?? 'completed',
                'signed_display' => $sign . formatCurrency($amount, $currency, $currency),
            ];
        }
        return $samples;
    }
}
