<?php

require_once __DIR__ . '/transfer-rails.php';
require_once __DIR__ . '/generator-data/generator-helpers.php';
require_once __DIR__ . '/generator-data/personal-names.php';
require_once __DIR__ . '/generator-data/merchant-selector.php';

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
        $salaryRange = $persona['salary_range'] ?? $this->defaultSalaryRange($style);
        $behaviourMult = $this->behaviourAmountMultiplier($behaviour);

        $banks = $this->loadBanks($operatingCountry);
        $domesticBanks = $banks['domestic'];
        $intlBanks = $banks['international'];

        $startDate = $params['start_date'];
        $endDate = $params['end_date'];
        $months = $this->enumerateMonths($startDate, $endDate);
        $targetCount = $this->resolveTargetCount($volume, $startDate, $endDate, $rng);

        $mix = $this->resolveMixWeights($style, $behaviour, $volume);
        $events = [];
        $recipients = [];
        $recurringMerchants = [];

        foreach ($months as $monthInfo) {
            $month = $monthInfo['month'];
            $season = getSeasonalTagBoosts($month);
            $tags = array_merge($merchantTags, $season['boost'] ?? []);

            $salaryDay = 1 + (int) floor($rng() * 4);
            $salaryDate = sprintf('%04d-%02d-%02d 09:%02d:00', $monthInfo['year'], $month, $salaryDay, (int) floor($rng() * 30));
            if ($salaryDate >= $startDate . ' 00:00:00' && $salaryDate <= $endDate . ' 23:59:59') {
                $salaryAmount = round(($salaryRange[0] + $rng() * ($salaryRange[1] - $salaryRange[0])) * $behaviourMult, 2);
                $employer = pickGeneratorName('business', $rng);
                $bank = $this->pickBank($domesticBanks, $rng);
                $events[] = $this->makeEvent('salary_credit', $salaryDate, $salaryAmount, [
                    'recipient_name' => $employer,
                    'recipient_bank' => $bank['name'] ?? '',
                    'recipient_account' => generateDomesticAccountNumber($countryIso, $rng),
                    'expense_category' => $style === 'business' ? 'bonus' : 'salary',
                ]);
            }

            if ($style === 'business') {
                $payrollDate = sprintf('%04d-%02d-%02d 10:00:00', $monthInfo['year'], $month, min(28, 15 + (int) floor($rng() * 5)));
                if ($payrollDate >= $startDate . ' 00:00:00' && $payrollDate <= $endDate . ' 23:59:59') {
                    $payrollBase = isset($salaryAmount)
                        ? $salaryAmount
                        : round(($salaryRange[0] + $rng() * ($salaryRange[1] - $salaryRange[0])) * $behaviourMult, 2);
                    $events[] = $this->makeEvent('salary_credit', $payrollDate, round($payrollBase * (0.5 + $rng()), 2), [
                        'recipient_name' => 'Client Payment — ' . pickGeneratorName('business', $rng),
                        'recipient_bank' => ($this->pickBank($domesticBanks, $rng)['name'] ?? ''),
                        'recipient_account' => generateDomesticAccountNumber($countryIso, $rng),
                        'expense_category' => 'salary',
                    ]);
                }
            }

            $billCount = (int) round($mix['bills'] * (0.8 + $rng() * 0.4));
            for ($b = 0; $b < $billCount; $b++) {
                $day = 3 + (int) floor($rng() * 25);
                $dt = sprintf('%04d-%02d-%02d %02d:%02d:00', $monthInfo['year'], $month, min(28, $day), 8 + (int) floor($rng() * 10), (int) floor($rng() * 59));
                if ($dt < $startDate . ' 00:00:00' || $dt > $endDate . ' 23:59:59') {
                    continue;
                }
                $merchant = selectGeneratorMerchant($operatingCountry, array_merge($tags, ['utilities', 'subscription']), 'online', $rng);
                if (!$merchant) {
                    continue;
                }
                $key = $merchant['name'];
                if (!empty($merchant['recurring'])) {
                    if (!isset($recurringMerchants[$key])) {
                        $recurringMerchants[$key] = ['merchant' => $merchant, 'day' => min(28, $day)];
                    }
                    $dt = sprintf('%04d-%02d-%02d %02d:%02d:00', $monthInfo['year'], $month, $recurringMerchants[$key]['day'], 10, (int) floor($rng() * 30));
                }
                $events[] = $this->makeEvent('bill_payment', $dt, merchantAmount($merchant, $rng, $behaviourMult), [
                    'merchant' => $merchant,
                    'description' => ($merchant['name'] ?? 'Bill') . ' — monthly payment',
                ]);
            }
        }

        $remaining = max(0, $targetCount - count($events));
        $domesticCount = (int) round($remaining * $mix['domestic_pct']);
        $intlCount = (int) round($remaining * $mix['intl_pct']);
        $cardCount = (int) round($remaining * $mix['card_pct']);
        $atmCount = $behaviour === 'cash_heavy' ? (int) round($remaining * 0.08) : 0;

        for ($i = 0; $i < $domesticCount; $i++) {
            $dt = $this->randomDateTime($startDate, $endDate, $rng, true);
            $name = pickGeneratorName('personal', $rng);
            $bank = $this->pickBank($domesticBanks, $rng);
            $amount = round((50 + $rng() * 2500) * $behaviourMult, 2);
            if ($style === 'business') {
                $name = pickGeneratorName('business', $rng);
                $amount = round((500 + $rng() * 15000) * $behaviourMult, 2);
            }
            $acct = generateDomesticAccountNumber($countryIso, $rng);
            $recipients[] = ['name' => $name, 'bank' => $bank['name'] ?? '', 'account' => $acct, 'scope' => 'domestic'];
            $events[] = $this->makeEvent('domestic_transfer', $dt, $amount, [
                'recipient_name' => $name,
                'recipient_bank' => $bank['name'] ?? '',
                'recipient_account' => $acct,
                'fee' => round($amount * 0.005 * $rng(), 2),
                'operating_country' => $operatingCountry,
                'country_iso' => $countryIso,
            ]);
        }

        for ($i = 0; $i < $intlCount; $i++) {
            if (empty($intlBanks)) {
                break;
            }
            $dt = $this->randomDateTime($startDate, $endDate, $rng, true);
            $bank = $intlBanks[(int) floor($rng() * count($intlBanks))];
            $name = pickGeneratorName('personal', $rng);
            $amount = round((200 + $rng() * 8000) * $behaviourMult, 2);
            $events[] = $this->makeEvent('international_transfer', $dt, $amount, [
                'recipient_name' => $name,
                'recipient_bank' => $bank['bank_name'] ?? $bank['name'] ?? '',
                'recipient_account' => $this->generateIntlAccount($bank, $rng),
                'fee' => round(15 + $rng() * 45, 2),
                'region' => $bank['region'] ?? '',
                'country' => $bank['country'] ?? '',
                'swift' => $bank['swift_code'] ?? '',
            ]);
        }

        $channel = ($behaviour === 'digital_first') ? 'online' : (($behaviour === 'cash_heavy') ? 'pos' : null);
        for ($i = 0; $i < $cardCount; $i++) {
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

        for ($i = 0; $i < $atmCount; $i++) {
            $dt = $this->randomDateTime($startDate, $endDate, $rng, false);
            $events[] = $this->makeEvent('atm_withdrawal', $dt, round((40 + $rng() * 300) * $behaviourMult, 2), [
                'description' => 'ATM Cash Withdrawal',
            ]);
        }

        if ($style === 'investor' && $rng() > 0.4) {
            $dt = $this->randomDateTime($startDate, $endDate, $rng, true);
            $events[] = $this->makeEvent('investment_credit', $dt, round((500 + $rng() * 5000) * $behaviourMult, 2), [
                'recipient_name' => 'Dividend — ' . pickGeneratorName('business', $rng),
                'description' => 'Investment dividend credit',
                'expense_category' => 'investment',
            ]);
        }

        usort($events, fn($a, $b) => strcmp($a['scheduled_at'], $b['scheduled_at']));

        $historyImpact = round((float)($params['history_impact'] ?? 0), 2);
        $events = $this->balancePlanToImpact($events, $historyImpact, $style, $rng, $domesticBanks, $countryIso, $operatingCountry);

        $summary = $this->summarizePlan($events, $persona, $style, $behaviour, $volume);

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
        $transactionType = str_contains($type, 'credit') || $type === 'salary_credit' || $type === 'investment_credit' || $type === 'adjustment_credit'
            ? 'credit' : 'debit';

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
                $expenseCategory = normalizeExpenseCategory($expenseCategory ?? ($type === 'investment_credit' ? 'investment' : 'salary'));
                if (empty($description)) {
                    $description = $type === 'salary_credit'
                        ? 'Transfer from ' . $recipientName . ' at ' . $recipientBank
                        : ($event['description'] ?? 'Credit');
                }
                if ($recipientBank) {
                    $meta['transfer_scope'] = 'domestic';
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

    private function balancePlanToImpact(array $events, float $historyImpact, string $style, callable $rng, array $domesticBanks, string $countryIso, string $operatingCountry): array
    {
        $net = 0.0;
        foreach ($events as $e) {
            if (($e['status'] ?? 'completed') !== 'completed') {
                continue;
            }
            $amt = (float)$e['amount'];
            $fee = (float)($e['fee'] ?? 0);
            if (str_contains($e['event_type'], 'credit') || in_array($e['event_type'], ['salary_credit', 'investment_credit', 'adjustment_credit'], true)) {
                $net += $amt;
            } else {
                $net -= ($amt + $fee);
            }
        }

        $diff = round($historyImpact - $net, 2);
        if (abs($diff) < 0.01) {
            return $events;
        }

        $lastDate = end($events)['scheduled_at'] ?? date('Y-m-d H:i:s');
        if ($diff > 0) {
            $bank = $this->pickBank($domesticBanks, $rng);
            $events[] = $this->makeEvent('adjustment_credit', $lastDate, abs($diff), [
                'recipient_name' => pickGeneratorName('business', $rng),
                'recipient_bank' => $bank['name'] ?? '',
                'recipient_account' => generateDomesticAccountNumber($countryIso, $rng),
                'expense_category' => $style === 'investor' ? 'investment' : 'salary',
                'description' => $style === 'investor' ? 'Investment return credit' : 'Incoming transfer credit',
            ]);
        } else {
            $events[] = $this->makeEvent('adjustment_debit', $lastDate, abs($diff), [
                'description' => 'Outgoing transfer adjustment',
            ]);
        }

        usort($events, fn($a, $b) => strcmp($a['scheduled_at'], $b['scheduled_at']));
        return $events;
    }

    private function summarizePlan(array $events, ?array $persona, string $style, string $behaviour, string $volume): array
    {
        $counts = [
            'domestic_transfers' => 0,
            'international_transfers' => 0,
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
            } elseif ($t === 'salary_credit' || $t === 'adjustment_credit') {
                $counts['salary_credits']++;
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
        $ranges = [
            'domestic_pct' => [0.40, 0.55],
            'intl_pct' => [0.10, 0.22],
            'card_pct' => [0.18, 0.32],
            'bills' => [2, 5],
        ];
        if ($style === 'business') {
            $ranges['domestic_pct'] = [0.45, 0.60];
            $ranges['intl_pct'] = [0.12, 0.25];
            $ranges['card_pct'] = [0.08, 0.18];
        } elseif ($style === 'student') {
            $ranges['domestic_pct'] = [0.30, 0.45];
            $ranges['intl_pct'] = [0.05, 0.12];
            $ranges['card_pct'] = [0.25, 0.40];
        } elseif ($style === 'investor') {
            $ranges['intl_pct'] = [0.18, 0.30];
            $ranges['card_pct'] = [0.10, 0.20];
        }
        if ($behaviour === 'intl_traveller') {
            $ranges['intl_pct'] = [0.20, 0.35];
        } elseif ($behaviour === 'conservative') {
            $ranges['card_pct'] = [0.10, 0.20];
            $ranges['intl_pct'] = [0.05, 0.12];
        } elseif ($behaviour === 'digital_first') {
            $ranges['card_pct'] = [0.22, 0.38];
        }
        if ($volume === 'low') {
            $ranges['bills'] = [1, 2];
        } elseif ($volume === 'high') {
            $ranges['bills'] = [3, 6];
        }

        return [
            'domestic_pct' => $this->randInRange($ranges['domestic_pct']),
            'intl_pct' => $this->randInRange($ranges['intl_pct']),
            'card_pct' => $this->randInRange($ranges['card_pct']),
            'bills' => (int) round($this->randInRange($ranges['bills'])),
        ];
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
        $row = $this->db->query(
            "SELECT setting_value FROM system_settings WHERE setting_key = 'bank_operating_country' LIMIT 1"
        )->fetch();
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
