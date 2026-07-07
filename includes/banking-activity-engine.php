<?php

require_once __DIR__ . '/transfer-rails.php';
require_once __DIR__ . '/transaction-categories.php';
require_once __DIR__ . '/generator-data/generator-helpers.php';
require_once __DIR__ . '/generator-data/personal-names.php';
require_once __DIR__ . '/generator-data/merchant-selector.php';

if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return $needle === '' || strpos($haystack, $needle) !== false;
    }
}

class BankingActivityEngine
{
    public const VOLUME_TX_RANGE = [
        'low' => [25, 45],
        'medium' => [60, 100],
        'high' => [120, 180],
    ];

    private Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    public function buildPlan(array $params): array
    {
        $seed = (string)($params['preview_seed'] ?? uniqid('plan_', true));
        $rng = $this->seedRandom($seed);

        $operatingCountry = $this->getOperatingCountry();
        $countryIso = generatorCountryIsoFromOperating($operatingCountry);

        $persona = getGeneratorPersonaById($params['persona_id'] ?? null);
        $style = $params['account_style'] ?? ($persona['account_style'] ?? 'personal');
        $behaviour = $params['financial_behaviour'] ?? ($persona['financial_behaviour'] ?? 'average');
        $volume = $params['volume'] ?? ($persona['volume'] ?? 'medium');
        if (!in_array($volume, ['low', 'medium', 'high'], true)) {
            $volume = $this->legacyDensityToVolume($params['density'] ?? 'normal');
        }

        $merchantTags = $persona['merchant_tags'] ?? [];
        $incomePattern = $this->resolveIncomePattern($persona, $style);
        $incomeRange = resolvePersonaSalaryRange($persona, $style, $operatingCountry);
        $behaviourMult = $this->behaviourAmountMultiplier($behaviour);

        $banks = $this->loadBanks($operatingCountry);
        $domesticBanks = $banks['domestic'];
        $intlBanks = $banks['international'];

        $startDate = $params['start_date'];
        $endDate = $params['end_date'];
        $targetCount = $this->resolveTargetCount($volume, $startDate, $endDate, $rng);

        $mix = $this->resolveMixWeights($style, $behaviour, $volume);
        $counts = $this->allocateEventCounts($targetCount, $mix, $behaviour);
        $events = [];

        for ($i = 0; $i < $counts['domestic_out']; $i++) {
            $dt = $this->randomDateTime($startDate, $endDate, $rng, true);
            $name = $style === 'business' ? pickGeneratorName('business', $rng) : pickGeneratorName('personal', $rng);
            $bank = $this->pickBank($domesticBanks, $rng);
            $acct = generateDomesticAccountNumber($countryIso, $rng);
            $events[] = $this->makeEvent('domestic_transfer', $dt, 1.0, [
                'recipient_name' => $name,
                'recipient_bank' => $bank['name'] ?? '',
                'recipient_account' => $acct,
                'fee' => 0,
                'operating_country' => $operatingCountry,
                'country_iso' => $countryIso,
            ]);
        }

        for ($i = 0; $i < $counts['intl_out']; $i++) {
            if (empty($intlBanks)) {
                break;
            }
            $dt = $this->randomDateTime($startDate, $endDate, $rng, true);
            $bank = $intlBanks[(int) floor($rng() * count($intlBanks))];
            $name = pickGeneratorName('personal', $rng);
            $events[] = $this->makeEvent('international_transfer', $dt, 1.0, [
                'recipient_name' => $name,
                'recipient_bank' => $bank['bank_name'] ?? $bank['name'] ?? '',
                'recipient_account' => $this->generateIntlAccount($bank, $rng),
                'fee' => 0,
                'region' => $bank['region'] ?? '',
                'country' => $bank['country'] ?? '',
                'swift' => $bank['swift_code'] ?? '',
            ]);
        }

        $incomingEvents = $this->buildIncomingCreditEvents(
            $counts['incoming'],
            $incomePattern,
            $style,
            $startDate,
            $endDate,
            $rng,
            $domesticBanks,
            $intlBanks,
            $countryIso,
            $incomeRange,
            $behaviourMult
        );
        $events = array_merge($events, $incomingEvents);

        $channel = ($behaviour === 'digital_first') ? 'online' : (($behaviour === 'cash_heavy') ? 'pos' : null);
        for ($i = 0; $i < $counts['card']; $i++) {
            $dt = $this->randomDateTime($startDate, $endDate, $rng, false);
            $merchant = selectGeneratorMerchant($operatingCountry, $merchantTags, $channel, $rng);
            if (!$merchant) {
                continue;
            }
            $events[] = $this->makeEvent('card_payment', $dt, merchantAmount($merchant, $rng, $behaviourMult), [
                'merchant' => $merchant,
                'description' => 'Card payment — ' . ($merchant['name'] ?? 'Purchase'),
            ]);
        }

        $billTags = $merchantTags;
        if ($style === 'student' || $style === 'personal') {
            $billTags = array_values(array_unique(array_merge($billTags, ['utilities', 'subscription'])));
        }
        for ($i = 0; $i < $counts['bills']; $i++) {
            $dt = $this->randomDateTime($startDate, $endDate, $rng, false);
            $merchant = selectGeneratorMerchant($operatingCountry, $billTags, 'online', $rng);
            if (!$merchant) {
                continue;
            }
            $events[] = $this->makeEvent('bill_payment', $dt, merchantAmount($merchant, $rng, $behaviourMult), [
                'merchant' => $merchant,
                'description' => ($merchant['name'] ?? 'Bill') . ' — payment',
            ]);
        }

        if ($behaviour === 'cash_heavy' && $counts['atm'] > 0) {
            for ($i = 0; $i < $counts['atm']; $i++) {
                $dt = $this->randomDateTime($startDate, $endDate, $rng, false);
                $events[] = $this->makeEvent('atm_withdrawal', $dt, round((40 + $rng() * 300) * $behaviourMult, 2), [
                    'description' => 'ATM Cash Withdrawal',
                ]);
            }
        }

        usort($events, fn($a, $b) => strcmp($a['scheduled_at'], $b['scheduled_at']));

        $historyImpact = round((float)($params['history_impact'] ?? 0), 2);
        $events = $this->applyPlanAmounts($events, $historyImpact, $style, $behaviourMult, $rng);
        $events = $this->balancePlanToImpact($events, $historyImpact, $style, $rng, $domesticBanks, $intlBanks, $countryIso, $operatingCountry);

        $summary = $this->summarizePlan($events, $persona, $style, $behaviour, $volume, $operatingCountry);

        return [
            'events' => $events,
            'summary' => $summary,
            'seed' => $seed,
            'operating_country' => $operatingCountry,
            'persona_label' => $persona['label'] ?? null,
            'engine_params' => [
                'account_style' => $style,
                'financial_behaviour' => $behaviour,
                'volume' => $volume,
                'persona_id' => $params['persona_id'] ?? null,
            ],
        ];
    }

    public function materializeEvents(array $events, string $currency): array
    {
        $rows = [];
        foreach ($events as $event) {
            $rows[] = $this->materializeEvent($event, $currency);
        }
        return $rows;
    }

    private function materializeEvent(array $event, string $currency): array
    {
        $type = $event['event_type'];
        $meta = ['generator' => true];
        $category = getStructuralCategoryForEventType($type);
        $expenseCategory = $event['expense_category'] ?? null;
        $paymentMethod = null;
        $recipientAccount = $event['recipient_account'] ?? null;
        $recipientName = $event['recipient_name'] ?? null;
        $recipientBank = $event['recipient_bank'] ?? null;
        $description = $event['description'] ?? '';
        $fee = (float)($event['fee'] ?? 0);
        $transactionType = $this->isCreditEventType($type) ? 'credit' : 'debit';

        switch ($type) {
            case 'domestic_transfer':
                $description = 'Domestic Transfer to ' . $recipientName . ' at ' . $recipientBank;
                $expenseCategory = normalizeExpenseCategory('transfer');
                $category = 'transfer';
                $meta['transfer_scope'] = 'domestic';
                $meta['country_code'] = $event['country_iso'] ?? '';
                $meta['bank_name'] = $recipientBank;
                $meta['account_number'] = $recipientAccount;
                $paymentMethod = 'local';
                if ($fee <= 0 && $event['amount'] > 0) {
                    $fee = round((float)$event['amount'] * 0.003, 2);
                }
                break;
            case 'international_transfer':
                $description = 'International Wire Transfer to ' . $recipientName . ' at ' . $recipientBank . ', ' . ($event['country'] ?? '');
                $expenseCategory = normalizeExpenseCategory('transfer');
                $category = 'transfer';
                $meta['transfer_scope'] = 'international';
                $meta['region'] = $event['region'] ?? '';
                $meta['country'] = $event['country'] ?? '';
                $meta['bank_name'] = $recipientBank;
                $meta['account_number'] = $recipientAccount;
                if (!empty($event['swift'])) {
                    $meta['swift'] = $event['swift'];
                }
                $paymentMethod = 'swift';
                if ($fee <= 0 && $event['amount'] > 0) {
                    $fee = round(15 + min(45, (float)$event['amount'] * 0.001), 2);
                }
                break;
            case 'incoming_domestic_transfer':
                $transactionType = 'credit';
                $category = 'deposit';
                $expenseCategory = normalizeExpenseCategory($expenseCategory ?? 'other');
                $description = $event['description'] ?? ('Incoming Domestic Transfer from ' . $recipientName . ' at ' . $recipientBank);
                $meta['transfer_scope'] = 'domestic';
                $meta['bank_name'] = $recipientBank;
                $meta['account_number'] = $recipientAccount;
                $paymentMethod = 'local';
                break;
            case 'incoming_international_transfer':
                $transactionType = 'credit';
                $category = 'deposit';
                $expenseCategory = normalizeExpenseCategory($expenseCategory ?? 'other');
                $description = $event['description'] ?? ('Incoming International Wire from ' . $recipientName . ' at ' . $recipientBank);
                $meta['transfer_scope'] = 'international';
                $meta['region'] = $event['region'] ?? '';
                $meta['country'] = $event['country'] ?? '';
                $meta['bank_name'] = $recipientBank;
                $meta['account_number'] = $recipientAccount;
                if (!empty($event['swift'])) {
                    $meta['swift'] = $event['swift'];
                }
                $paymentMethod = 'swift';
                break;
            case 'card_payment':
                $merchant = $event['merchant'] ?? [];
                $recipientName = $merchant['name'] ?? $recipientName;
                $expenseCategory = normalizeExpenseCategory($merchant['category'] ?? 'shopping');
                $category = 'card';
                $meta['channel'] = $merchant['channel'] ?? 'pos';
                $description = $event['description'] ?? ('Card payment — ' . $recipientName);
                break;
            case 'bill_payment':
                $merchant = $event['merchant'] ?? [];
                $recipientName = $merchant['name'] ?? $recipientName;
                $expenseCategory = normalizeExpenseCategory($merchant['category'] ?? 'bills');
                $category = 'payment';
                $description = $event['description'] ?? ($recipientName . ' — payment');
                break;
            case 'salary_credit':
            case 'investment_credit':
            case 'adjustment_credit':
                $transactionType = 'credit';
                $category = 'deposit';
                $expenseCategory = normalizeExpenseCategory($expenseCategory ?? ($type === 'investment_credit' ? 'investment' : ($type === 'salary_credit' ? 'salary' : 'other')));
                if (empty($description)) {
                    $description = $type === 'salary_credit'
                        ? 'Salary deposit from ' . $recipientName . ' at ' . $recipientBank
                        : ($event['description'] ?? 'Credit');
                }
                if ($recipientBank) {
                    $meta['transfer_scope'] = str_contains($type, 'international') ? 'international' : 'domestic';
                    $meta['bank_name'] = $recipientBank;
                    $meta['account_number'] = $recipientAccount;
                }
                break;
            case 'atm_withdrawal':
                $category = 'withdrawal';
                $expenseCategory = normalizeExpenseCategory('withdrawal');
                $description = $event['description'] ?? 'ATM Cash Withdrawal';
                break;
            case 'adjustment_debit':
                $category = 'withdrawal';
                $expenseCategory = normalizeExpenseCategory('other');
                $description = $event['description'] ?? 'Account adjustment';
                break;
        }

        return [
            'transaction_type' => $transactionType,
            'category' => $category,
            'expense_category' => $expenseCategory,
            'amount' => round((float)$event['amount'], 2),
            'fee' => $fee,
            'description' => $description,
            'recipient_account' => $recipientAccount,
            'recipient_name' => $recipientName,
            'recipient_bank' => $recipientBank,
            'status' => $event['status'] ?? 'completed',
            'scheduled_at' => $event['scheduled_at'],
            'payment_method' => $paymentMethod,
            'metadata' => $meta,
            'currency' => $currency,
        ];
    }

    private function balancePlanToImpact(array $events, float $historyImpact, string $style, callable $rng, array $domesticBanks, array $intlBanks, string $countryIso, string $operatingCountry): array
    {
        $diff = round($historyImpact - $this->computePlanNet($events), 2);
        if (abs($diff) < 0.01) {
            return $events;
        }

        $lastDate = end($events)['scheduled_at'] ?? date('Y-m-d H:i:s');

        if ($diff > 0) {
            $splits = min(5, max(1, (int) ceil(log10(max(10, abs($diff))) - 2)));
            $remaining = abs($diff);
            for ($i = 0; $i < $splits && $remaining > 0.01; $i++) {
                $portion = $i === $splits - 1
                    ? $remaining
                    : round($remaining * (0.25 + $rng() * 0.35), 2);
                $remaining = round($remaining - $portion, 2);
                $useIntl = !empty($intlBanks) && ($style === 'investor' || $rng() > 0.45);
                if ($useIntl) {
                    $bank = $intlBanks[(int) floor($rng() * count($intlBanks))];
                    $sender = pickGeneratorName('business', $rng);
                    $events[] = $this->makeEvent('incoming_international_transfer', $lastDate, $portion, [
                        'recipient_name' => $sender,
                        'recipient_bank' => $bank['bank_name'] ?? $bank['name'] ?? '',
                        'recipient_account' => $this->generateIntlAccount($bank, $rng),
                        'region' => $bank['region'] ?? '',
                        'country' => $bank['country'] ?? '',
                        'swift' => $bank['swift_code'] ?? '',
                        'expense_category' => $style === 'investor' ? 'investment' : 'other',
                        'description' => $style === 'investor'
                            ? 'Incoming International Wire — capital distribution from ' . $sender
                            : 'Incoming International Wire from ' . $sender,
                    ]);
                } else {
                    $bank = $this->pickBank($domesticBanks, $rng);
                    $sender = pickGeneratorName('business', $rng);
                    $events[] = $this->makeEvent('incoming_domestic_transfer', $lastDate, $portion, [
                        'recipient_name' => $sender,
                        'recipient_bank' => $bank['name'] ?? '',
                        'recipient_account' => generateDomesticAccountNumber($countryIso, $rng),
                        'expense_category' => $style === 'investor' ? 'investment' : 'other',
                        'description' => 'Incoming Domestic Transfer from ' . $sender . ' at ' . ($bank['name'] ?? 'Bank'),
                    ]);
                }
            }
        } else {
            $splits = min(3, max(1, (int) ceil(log10(max(10, abs($diff))) - 2)));
            $remaining = abs($diff);
            for ($i = 0; $i < $splits && $remaining > 0.01; $i++) {
                $portion = $i === $splits - 1 ? $remaining : round($remaining * (0.3 + $rng() * 0.4), 2);
                $remaining = round($remaining - $portion, 2);
                $bank = $this->pickBank($domesticBanks, $rng);
                $name = pickGeneratorName('personal', $rng);
                $events[] = $this->makeEvent('domestic_transfer', $lastDate, $portion, [
                    'recipient_name' => $name,
                    'recipient_bank' => $bank['name'] ?? '',
                    'recipient_account' => generateDomesticAccountNumber($countryIso, $rng),
                    'fee' => round($portion * 0.003, 2),
                    'country_iso' => $countryIso,
                    'description' => 'Domestic Transfer to ' . $name,
                ]);
            }
        }

        usort($events, fn($a, $b) => strcmp($a['scheduled_at'], $b['scheduled_at']));
        return $events;
    }

    private function summarizePlan(array $events, ?array $persona, string $style, string $behaviour, string $volume, string $operatingCountry = ''): array
    {
        $counts = [
            'domestic_transfers' => 0,
            'international_transfers' => 0,
            'incoming_credits' => 0,
            'card_payments' => 0,
            'bills' => 0,
            'salary_credits' => 0,
            'atm_withdrawals' => 0,
            'other' => 0,
        ];
        foreach ($events as $e) {
            $t = $e['event_type'];
            if ($t === 'domestic_transfer') {
                $counts['domestic_transfers']++;
            } elseif ($t === 'international_transfer') {
                $counts['international_transfers']++;
            } elseif ($t === 'card_payment') {
                $counts['card_payments']++;
            } elseif ($t === 'bill_payment') {
                $counts['bills']++;
            } elseif ($this->isCreditEventType($t)) {
                $counts['incoming_credits']++;
                if ($t === 'salary_credit') {
                    $counts['salary_credits']++;
                }
            } elseif ($t === 'atm_withdrawal') {
                $counts['atm_withdrawals']++;
            } else {
                $counts['other']++;
            }
        }
        $counts['total'] = count($events);
        $counts['account_style'] = $style;
        $counts['financial_behaviour'] = $behaviour;
        $counts['volume'] = $volume;
        $counts['persona_label'] = $persona['label'] ?? null;
        $counts['operating_country'] = $operatingCountry;
        return $counts;
    }

    private function makeEvent(string $type, string $scheduledAt, float $amount, array $extra = []): array
    {
        return array_merge([
            'event_type' => $type,
            'scheduled_at' => $scheduledAt,
            'amount' => round($amount, 2),
            'status' => 'completed',
            'fee' => 0,
        ], $extra);
    }

    private function resolveMixWeights(string $style, string $behaviour, string $volume): array
    {
        $mix = [
            'domestic_out' => 0.36,
            'intl_out' => 0.18,
            'incoming' => 0.18,
            'card' => 0.14,
            'bills' => 0.14,
        ];

        if ($style === 'investor') {
            $mix = ['domestic_out' => 0.28, 'intl_out' => 0.28, 'incoming' => 0.22, 'card' => 0.12, 'bills' => 0.10];
        } elseif ($style === 'business') {
            $mix = ['domestic_out' => 0.38, 'intl_out' => 0.20, 'incoming' => 0.20, 'card' => 0.08, 'bills' => 0.14];
        } elseif ($style === 'student') {
            $mix = ['domestic_out' => 0.22, 'intl_out' => 0.06, 'incoming' => 0.10, 'card' => 0.32, 'bills' => 0.30];
        } elseif ($style === 'personal') {
            $mix = ['domestic_out' => 0.32, 'intl_out' => 0.14, 'incoming' => 0.16, 'card' => 0.18, 'bills' => 0.20];
        }

        if ($behaviour === 'intl_traveller') {
            $mix['intl_out'] += 0.08;
            $mix['domestic_out'] -= 0.04;
            $mix['bills'] -= 0.04;
        } elseif ($behaviour === 'conservative') {
            $mix['bills'] += 0.04;
            $mix['card'] -= 0.04;
        } elseif ($behaviour === 'digital_first') {
            $mix['card'] += 0.06;
            $mix['domestic_out'] -= 0.03;
            $mix['bills'] -= 0.03;
        } elseif ($behaviour === 'cash_heavy') {
            $mix['card'] -= 0.06;
            $mix['domestic_out'] += 0.03;
            $mix['bills'] += 0.03;
        }

        if ($volume === 'low') {
            $mix['bills'] = min($mix['bills'], 0.18);
        } elseif ($volume === 'high') {
            $mix['domestic_out'] += 0.02;
            $mix['intl_out'] += 0.02;
            $mix['bills'] = max(0.06, $mix['bills'] - 0.04);
        }

        $sum = array_sum($mix);
        foreach ($mix as $k => $v) {
            $mix[$k] = max(0.04, $v / $sum);
        }
        $sum = array_sum($mix);
        foreach ($mix as $k => $v) {
            $mix[$k] = $v / $sum;
        }

        return $mix;
    }

    private function allocateEventCounts(int $targetCount, array $mix, string $behaviour): array
    {
        $counts = [
            'domestic_out' => (int) round($targetCount * $mix['domestic_out']),
            'intl_out' => (int) round($targetCount * $mix['intl_out']),
            'incoming' => (int) round($targetCount * $mix['incoming']),
            'card' => (int) round($targetCount * $mix['card']),
            'bills' => (int) round($targetCount * $mix['bills']),
            'atm' => $behaviour === 'cash_heavy' ? max(2, (int) round($targetCount * 0.05)) : 0,
        ];

        $sum = array_sum($counts);
        $delta = $targetCount - $sum;
        if ($delta > 0) {
            $counts['domestic_out'] += $delta;
        } elseif ($delta < 0) {
            foreach (['bills', 'card', 'incoming'] as $bucket) {
                if ($delta >= 0) {
                    break;
                }
                $take = min($counts[$bucket], abs($delta));
                $counts[$bucket] -= $take;
                $delta += $take;
            }
        }

        return $counts;
    }

    private function resolveIncomePattern(?array $persona, string $style): string
    {
        if (!empty($persona['income_pattern'])) {
            return (string)$persona['income_pattern'];
        }
        $map = [
            'student' => 'minimal',
            'investor' => 'wire_inflows',
            'business' => 'business_inflow',
            'personal' => 'occasional',
        ];
        return $map[$style] ?? 'occasional';
    }

    private function buildIncomingCreditEvents(
        int $count,
        string $incomePattern,
        string $style,
        string $startDate,
        string $endDate,
        callable $rng,
        array $domesticBanks,
        array $intlBanks,
        string $countryIso,
        array $incomeRange,
        float $behaviourMult
    ): array {
        if ($count <= 0) {
            return [];
        }

        $events = [];
        $attempts = 0;
        $maxAttempts = max($count * 3, $count + 5);
        while (count($events) < $count && $attempts < $maxAttempts) {
            $attempts++;
            $dt = $this->randomDateTime($startDate, $endDate, $rng, true);
            $event = $this->makeIncomingCreditEvent(
                $incomePattern,
                $style,
                $dt,
                $rng,
                $domesticBanks,
                $intlBanks,
                $countryIso,
                $incomeRange,
                $behaviourMult
            );
            if ($event) {
                $events[] = $event;
            }
        }
        return $events;
    }

    private function makeIncomingCreditEvent(
        string $incomePattern,
        string $style,
        string $scheduledAt,
        callable $rng,
        array $domesticBanks,
        array $intlBanks,
        string $countryIso,
        array $incomeRange,
        float $behaviourMult
    ): ?array {
        $useIntl = !empty($intlBanks) && (
            $incomePattern === 'wire_inflows'
            || ($style === 'investor' && $rng() > 0.35)
            || ($incomePattern !== 'monthly_salary' && $rng() > 0.65)
        );

        if ($incomePattern === 'monthly_salary' && $rng() > 0.55) {
            $bank = $this->pickBank($domesticBanks, $rng);
            $employer = pickGeneratorName('business', $rng);
            return $this->makeEvent('salary_credit', $scheduledAt, 1.0, [
                'recipient_name' => $employer,
                'recipient_bank' => $bank['name'] ?? '',
                'recipient_account' => generateDomesticAccountNumber($countryIso, $rng),
                'expense_category' => 'salary',
                'description' => 'Salary deposit from ' . $employer,
            ]);
        }

        if ($incomePattern === 'minimal' && $rng() > 0.65) {
            return null;
        }

        $sender = pickGeneratorName('business', $rng);
        if ($useIntl) {
            $bank = $intlBanks[(int) floor($rng() * count($intlBanks))];
            $labels = [
                'wire_inflows' => ['Incoming International Wire — dividend from ', 'Incoming International Wire — capital return from '],
                'business_inflow' => ['Client payment (international) from ', 'Trade settlement from '],
                'default' => ['Incoming International Wire from ', 'Transfer from '],
            ];
            $set = $labels[$incomePattern] ?? $labels['default'];
            $prefix = $set[(int) floor($rng() * count($set))];
            return $this->makeEvent('incoming_international_transfer', $scheduledAt, 1.0, [
                'recipient_name' => $sender,
                'recipient_bank' => $bank['bank_name'] ?? $bank['name'] ?? '',
                'recipient_account' => $this->generateIntlAccount($bank, $rng),
                'region' => $bank['region'] ?? '',
                'country' => $bank['country'] ?? '',
                'swift' => $bank['swift_code'] ?? '',
                'expense_category' => $style === 'investor' ? 'investment' : 'other',
                'description' => $prefix . $sender . ' at ' . ($bank['bank_name'] ?? $bank['name'] ?? 'Bank'),
            ]);
        }

        $bank = $this->pickBank($domesticBanks, $rng);
        if ($incomePattern === 'business_inflow') {
            $desc = 'Client payment from ' . $sender . ' at ' . ($bank['name'] ?? 'Bank');
            $category = 'other';
        } elseif ($style === 'investor') {
            $desc = 'Incoming Domestic Transfer — distribution from ' . $sender;
            $category = 'investment';
        } elseif ($incomePattern === 'minimal') {
            $desc = 'Incoming transfer from ' . $sender;
            $category = 'other';
        } else {
            $desc = 'Incoming Domestic Transfer from ' . $sender . ' at ' . ($bank['name'] ?? 'Bank');
            $category = 'other';
        }

        return $this->makeEvent('incoming_domestic_transfer', $scheduledAt, 1.0, [
            'recipient_name' => $sender,
            'recipient_bank' => $bank['name'] ?? '',
            'recipient_account' => generateDomesticAccountNumber($countryIso, $rng),
            'expense_category' => $category,
            'description' => $desc,
        ]);
    }

    private function applyPlanAmounts(array $events, float $historyImpact, string $style, float $behaviourMult, callable $rng): array
    {
        $fixedIndices = [];
        $creditIndices = [];
        $debitIndices = [];

        foreach ($events as $idx => $e) {
            $type = $e['event_type'];
            if (in_array($type, ['bill_payment', 'card_payment', 'atm_withdrawal'], true)) {
                $fixedIndices[] = $idx;
            } elseif ($this->isCreditEventType($type)) {
                $creditIndices[] = $idx;
            } else {
                $debitIndices[] = $idx;
            }
        }

        $fixedTotal = 0.0;
        foreach ($fixedIndices as $idx) {
            $amt = (float)$events[$idx]['amount'];
            $fee = (float)($events[$idx]['fee'] ?? 0);
            $fixedTotal += $amt + $fee;
        }

        $absImpact = abs($historyImpact);
        if ($absImpact < 500) {
            $this->assignDefaultTransferAmounts($events, $debitIndices, $creditIndices, $style, $behaviourMult, $rng);
            return $events;
        }

        $grossCredits = $historyImpact > 0
            ? $absImpact + $fixedTotal + ($absImpact * (0.35 + $rng() * 0.25))
            : $fixedTotal + ($absImpact * (0.5 + $rng() * 0.2));
        $grossDebits = $historyImpact > 0
            ? max($fixedTotal, $grossCredits - $historyImpact)
            : $fixedTotal + $absImpact + ($absImpact * (0.25 + $rng() * 0.15));

        $this->distributeAmountsAcrossIndices($events, $creditIndices, $grossCredits, $style, $behaviourMult, $rng, true);
        $this->distributeAmountsAcrossIndices($events, $debitIndices, max(0, $grossDebits - $fixedTotal), $style, $behaviourMult, $rng, false);

        foreach ($debitIndices as $idx) {
            if (in_array($events[$idx]['event_type'], ['domestic_transfer', 'international_transfer'], true)) {
                $amt = (float)$events[$idx]['amount'];
                if ($events[$idx]['event_type'] === 'domestic_transfer') {
                    $events[$idx]['fee'] = round($amt * (0.002 + $rng() * 0.004), 2);
                } else {
                    $events[$idx]['fee'] = round(15 + min(75, $amt * 0.0008), 2);
                }
            }
        }

        return $events;
    }

    private function assignDefaultTransferAmounts(array &$events, array $debitIndices, array $creditIndices, string $style, float $behaviourMult, callable $rng): void
    {
        foreach ($debitIndices as $idx) {
            $type = $events[$idx]['event_type'];
            $range = $this->transferAmountRange($style, $type);
            $events[$idx]['amount'] = round(($range[0] + $rng() * ($range[1] - $range[0])) * $behaviourMult, 2);
        }
        foreach ($creditIndices as $idx) {
            if ((float)$events[$idx]['amount'] <= 1.0) {
                $range = $this->transferAmountRange($style, 'incoming');
                $events[$idx]['amount'] = round(($range[0] + $rng() * ($range[1] - $range[0])) * $behaviourMult, 2);
            }
        }
    }

    private function distributeAmountsAcrossIndices(array &$events, array $indices, float $total, string $style, float $behaviourMult, callable $rng, bool $isCredit): void
    {
        if ($total <= 0 || empty($indices)) {
            return;
        }

        $weights = [];
        $weightSum = 0.0;
        foreach ($indices as $idx) {
            $type = $events[$idx]['event_type'];
            $base = $this->transferAmountRange($style, $isCredit ? 'incoming' : $type);
            $w = ($base[0] + $rng() * ($base[1] - $base[0])) * $behaviourMult;
            $weights[$idx] = max(1.0, $w);
            $weightSum += $weights[$idx];
        }

        foreach ($indices as $idx) {
            $share = $weights[$idx] / max(0.01, $weightSum);
            $events[$idx]['amount'] = round(max(1.0, $total * $share), 2);
        }
    }

    private function transferAmountRange(string $style, string $type): array
    {
        $map = [
            'investor' => [
                'domestic_transfer' => [5000, 250000],
                'international_transfer' => [15000, 800000],
                'incoming' => [10000, 500000],
            ],
            'business' => [
                'domestic_transfer' => [1000, 75000],
                'international_transfer' => [5000, 200000],
                'incoming' => [3000, 120000],
            ],
            'student' => [
                'domestic_transfer' => [25, 800],
                'international_transfer' => [100, 2500],
                'incoming' => [50, 1200],
            ],
        ];
        $defaults = [
            'domestic_transfer' => [100, 8000],
            'international_transfer' => [500, 25000],
            'incoming' => [200, 15000],
        ];
        $styleMap = $map[$style] ?? $defaults;
        if ($type === 'incoming_domestic_transfer' || $type === 'incoming_international_transfer' || $type === 'salary_credit' || $type === 'investment_credit') {
            $type = 'incoming';
        }
        return $styleMap[$type] ?? $defaults[$type] ?? [100, 5000];
    }

    private function computePlanNet(array $events): float
    {
        $net = 0.0;
        foreach ($events as $e) {
            if (($e['status'] ?? 'completed') !== 'completed') {
                continue;
            }
            $amt = (float)$e['amount'];
            $fee = (float)($e['fee'] ?? 0);
            if ($this->isCreditEventType($e['event_type'])) {
                $net += $amt;
            } else {
                $net -= ($amt + $fee);
            }
        }
        return round($net, 2);
    }

    private function isCreditEventType(string $type): bool
    {
        return str_contains($type, 'credit')
            || str_contains($type, 'incoming_')
            || in_array($type, ['salary_credit', 'investment_credit', 'adjustment_credit', 'incoming_domestic_transfer', 'incoming_international_transfer'], true);
    }

    private function randInRange(array $range): float
    {
        return $range[0] + (mt_rand() / mt_getrandmax()) * ($range[1] - $range[0]);
    }

    private function resolveTargetCount(string $volume, string $start, string $end, callable $rng): int
    {
        $range = self::VOLUME_TX_RANGE[$volume] ?? self::VOLUME_TX_RANGE['medium'];
        $days = max(1, (int) ((strtotime($end) - strtotime($start)) / 86400) + 1);
        $scale = min(1.5, $days / 90);
        $min = (int) round($range[0] * $scale);
        $max = (int) round($range[1] * $scale);
        return $min + (int) floor($rng() * max(1, $max - $min + 1));
    }

    private function behaviourAmountMultiplier(string $behaviour): float
    {
        $map = [
            'conservative' => 0.75,
            'average' => 1.0,
            'active_spender' => 1.15,
            'luxury' => 1.45,
            'intl_traveller' => 1.2,
            'cash_heavy' => 0.95,
            'digital_first' => 1.05,
        ];
        return $map[$behaviour] ?? 1.0;
    }

    private function defaultSalaryRange(string $style): array
    {
        $map = [
            'personal' => [2500, 6500],
            'business' => [8000, 25000],
            'investor' => [5000, 18000],
            'student' => [600, 1500],
        ];
        return $map[$style] ?? [2500, 6500];
    }

    private function legacyDensityToVolume(string $density): string
    {
        $map = ['light' => 'low', 'normal' => 'medium', 'heavy' => 'high'];
        return $map[$density] ?? 'medium';
    }

    private function getOperatingCountry(): string
    {
        $stmt = $this->db->query(
            "SELECT setting_value FROM system_settings WHERE setting_key = 'bank_operating_country' LIMIT 1"
        );
        if (!$stmt) {
            return 'United States';
        }
        $row = $stmt->fetch();
        return $row['setting_value'] ?? 'United States';
    }

    private function loadBanks(string $operatingCountry): array
    {
        $stmt = $this->db->query("SELECT id, name, name AS bank_name, region, country, swift_code FROM banks WHERE is_active = 1 ORDER BY country ASC, name ASC");
        $all = $stmt ? $stmt->fetchAll() : [];
        $iso = generatorCountryIsoFromOperating($operatingCountry);
        $domestic = [];
        $intl = [];
        foreach ($all as $bank) {
            $bankIso = generatorCountryIsoFromOperating($bank['country'] ?? '');
            if (strcasecmp($bankIso, $iso) === 0 || strcasecmp(trim($bank['country'] ?? ''), trim($operatingCountry)) === 0) {
                $domestic[] = $bank;
            } else {
                $intl[] = $bank;
            }
        }
        return ['domestic' => $domestic, 'international' => $intl, 'all' => $all];
    }

    private function pickBank(array $banks, callable $rng): array
    {
        if (empty($banks)) {
            return ['name' => 'Local Bank'];
        }
        return $banks[(int) floor($rng() * count($banks))];
    }

    private function generateIntlAccount(array $bank, callable $rng): string
    {
        if (!empty($bank['swift_code'])) {
            return strtoupper(substr($bank['swift_code'], 0, 4)) . (string) random_int(10000000, 99999999);
        }
        return generateDomesticAccountNumber('US', $rng);
    }

    private function enumerateMonths(string $start, string $end): array
    {
        $months = [];
        $cur = strtotime(date('Y-m-01', strtotime($start)));
        $endTs = strtotime($end);
        while ($cur <= $endTs) {
            $months[] = ['year' => (int) date('Y', $cur), 'month' => (int) date('n', $cur)];
            $cur = strtotime('+1 month', $cur);
        }
        return $months ?: [['year' => (int) date('Y'), 'month' => (int) date('n')]];
    }

    private function randomDateTime(string $start, string $end, callable $rng, bool $weekdayBias): string
    {
        $startTs = strtotime($start . ' 08:00:00');
        $endTs = strtotime($end . ' 20:00:00');
        if ($endTs <= $startTs) {
            $endTs = $startTs + 86400;
        }
        for ($attempt = 0; $attempt < 20; $attempt++) {
            $ts = $startTs + (int) floor($rng() * ($endTs - $startTs));
            $dow = (int) date('N', $ts);
            if ($weekdayBias && $dow >= 6 && $rng() > 0.35) {
                continue;
            }
            return date('Y-m-d H:i:s', $ts);
        }
        return date('Y-m-d H:i:s', $startTs + (int) floor($rng() * max(1, $endTs - $startTs)));
    }

    private function seedRandom(string $seed): callable
    {
        $state = crc32($seed);
        return function () use (&$state) {
            $state = ($state * 1103515245 + 12345) & 0x7fffffff;
            return $state / 0x7fffffff;
        };
    }
}
