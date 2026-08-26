<?php
/**
 * Single source of truth for exchange rates (ExchangeRate-API v6).
 * Resolution: fresh cache → live API → inverse/bridge → stale cache → static offline fallback.
 * Never returns silent 1.0 across different currencies.
 */

class ExchangeRates {
    private static $instance = null;
    private $db;
    private $cacheTime = 3600;

    /**
     * Approximate USD-based rates used when live API / cache are unavailable.
     * Values are "units of currency per 1 USD" (e.g. EUR 0.92 means 1 USD = 0.92 EUR).
     */
    private static $staticUsdRates = [
        'USD' => 1.0,
        'EUR' => 0.92,
        'GBP' => 0.79,
        'JPY' => 149.0,
        'CNY' => 7.25,
        'INR' => 83.5,
        'CAD' => 1.36,
        'AUD' => 1.52,
        'NZD' => 1.64,
        'CHF' => 0.88,
        'SEK' => 10.5,
        'NOK' => 10.8,
        'DKK' => 6.85,
        'PLN' => 3.95,
        'CZK' => 22.8,
        'HUF' => 360.0,
        'RON' => 4.55,
        'BGN' => 1.80,
        'RUB' => 92.0,
        'TRY' => 32.0,
        'ILS' => 3.70,
        'HKD' => 7.82,
        'SGD' => 1.34,
        'MYR' => 4.70,
        'THB' => 35.5,
        'IDR' => 15800.0,
        'PHP' => 56.5,
        'VND' => 24500.0,
        'KRW' => 1320.0,
        'TWD' => 31.5,
        'NGN' => 1550.0,
        'GHS' => 15.5,
        'KES' => 129.0,
        'ZAR' => 18.5,
        'EGP' => 48.0,
        'MAD' => 10.0,
        'TND' => 3.10,
        'DZD' => 134.0,
        'AED' => 3.67,
        'SAR' => 3.75,
        'QAR' => 3.64,
        'KWD' => 0.31,
        'BRL' => 5.10,
        'MXN' => 17.2,
        'ARS' => 870.0,
        'CLP' => 920.0,
        'COP' => 3950.0,
        'PEN' => 3.75,
        'PKR' => 278.0,
        'BDT' => 110.0,
        'LKR' => 300.0,
        'XOF' => 605.0,
        'ZMW' => 26.0,
    ];

    private function __construct() {
        $this->db = Database::getInstance();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function normalizeCode($currency) {
        $code = strtoupper(trim((string)$currency));
        return preg_match('/^[A-Z]{3}$/', $code) ? $code : (defined('DEFAULT_CURRENCY') ? DEFAULT_CURRENCY : 'USD');
    }

    /**
     * API key from system_settings, then config constant. No hardcoded project key.
     */
    public function getApiKey() {
        try {
            if (!class_exists('SystemSettings')) {
                require_once __DIR__ . '/system-settings.php';
            }
            $fromSettings = trim((string)SystemSettings::getInstance()->get('exchange_rate_api_key', ''));
            if ($fromSettings !== '' && $fromSettings !== 'your-api-key') {
                return $fromSettings;
            }
        } catch (Exception $e) {
            // fall through
        }

        $key = defined('EXCHANGE_RATE_API_KEY') ? trim((string)EXCHANGE_RATE_API_KEY) : '';
        if ($key === '' || $key === 'your-api-key') {
            return '';
        }
        return $key;
    }

    /**
     * Rate to multiply amount in $from to get amount in $to.
     * Returns null when no rate can be resolved (callers must not invent 1.0).
     */
    public function getRate($fromCurrency, $toCurrency) {
        $from = self::normalizeCode($fromCurrency);
        $to = self::normalizeCode($toCurrency);

        if ($from === $to) {
            return 1.0;
        }

        $rate = $this->resolveRate($from, $to);
        if ($rate !== null && $rate > 0) {
            return $rate;
        }

        error_log("ExchangeRates: no rate for {$from} -> {$to}");
        return null;
    }

    public function convert($amount, $fromCurrency, $toCurrency) {
        $from = self::normalizeCode($fromCurrency);
        $to = self::normalizeCode($toCurrency);
        $amount = (float)$amount;

        if ($from === $to) {
            return round($amount, 2);
        }

        $rate = $this->getRate($from, $to);
        if ($rate === null || $rate <= 0) {
            error_log("ExchangeRates: convert failed {$from} -> {$to}, returning original amount");
            return round($amount, 2);
        }

        return round($amount * $rate, 2);
    }

    /**
     * Refresh all rates from a base currency into exchange_rates cache.
     */
    public function refreshRates($baseCurrency = null) {
        $apiKey = $this->getApiKey();
        if ($apiKey === '') {
            error_log('ExchangeRates: refresh skipped — no API key configured');
            return false;
        }

        $base = self::normalizeCode($baseCurrency ?? (defined('DEFAULT_CURRENCY') ? DEFAULT_CURRENCY : 'USD'));
        $url = 'https://v6.exchangerate-api.com/v6/' . rawurlencode($apiKey) . '/latest/' . rawurlencode($base);

        $data = $this->fetchJson($url);
        if (!$data || ($data['result'] ?? '') !== 'success' || empty($data['conversion_rates'])) {
            error_log('ExchangeRates: refresh failed for base ' . $base);
            return false;
        }

        foreach ($data['conversion_rates'] as $currency => $rate) {
            $this->cacheRate($base, self::normalizeCode($currency), (float)$rate);
        }

        $this->cacheRate($base, $base, 1.0);
        return true;
    }

    private function resolveRate($from, $to, $depth = 0) {
        if ($depth > 4) {
            error_log("ExchangeRates: max recursion depth for {$from} -> {$to}");
            return null;
        }

        $fresh = $this->getCachedRate($from, $to, false);
        if ($fresh !== null) {
            return $fresh;
        }

        $apiRate = $this->fetchPairFromAPI($from, $to);
        if ($apiRate !== null && $apiRate > 0) {
            $this->cacheRate($from, $to, $apiRate);
            return $apiRate;
        }

        $inverse = $this->getCachedRate($to, $from, false);
        if ($inverse !== null && $inverse > 0) {
            return 1.0 / $inverse;
        }

        $inverseApi = $this->fetchPairFromAPI($to, $from);
        if ($inverseApi !== null && $inverseApi > 0) {
            $this->cacheRate($to, $from, $inverseApi);
            return 1.0 / $inverseApi;
        }

        $bridge = defined('DEFAULT_CURRENCY') ? self::normalizeCode(DEFAULT_CURRENCY) : 'USD';
        if ($from !== $bridge && $to !== $bridge) {
            $fromBridge = $this->resolveRate($from, $bridge, $depth + 1);
            $bridgeTo = $this->resolveRate($bridge, $to, $depth + 1);
            if ($fromBridge > 0 && $bridgeTo > 0) {
                return $fromBridge * $bridgeTo;
            }
        }

        $stale = $this->getCachedRate($from, $to, true);
        if ($stale !== null && $stale > 0) {
            return $stale;
        }

        $staleInverse = $this->getCachedRate($to, $from, true);
        if ($staleInverse !== null && $staleInverse > 0) {
            return 1.0 / $staleInverse;
        }

        return $this->getStaticRate($from, $to);
    }

    /**
     * Offline fallback: rates vs USD, bridge through USD for cross pairs.
     */
    private function getStaticRate($from, $to) {
        $fromUsd = self::$staticUsdRates[$from] ?? null;
        $toUsd = self::$staticUsdRates[$to] ?? null;

        if ($fromUsd === null || $toUsd === null || $fromUsd <= 0 || $toUsd <= 0) {
            return null;
        }

        // staticUsdRates: units of currency per 1 USD
        // amount_to = amount_from * (toUsd / fromUsd)  when converting from→to
        // e.g. USD→EUR: 1 * (0.92 / 1) = 0.92
        // e.g. EUR→USD: 1 * (1 / 0.92) = 1.087
        // e.g. EUR→GBP: 1 * (0.79 / 0.92)
        return $toUsd / $fromUsd;
    }

    private function getCachedRate($from, $to, $allowStale) {
        $sql = 'SELECT rate, updated_at FROM exchange_rates WHERE from_currency = ? AND to_currency = ? LIMIT 1';
        $stmt = $this->db->query($sql, [$from, $to]);
        $cached = $stmt->fetch();
        if (!$cached) {
            return null;
        }

        if (!$allowStale && (time() - strtotime($cached['updated_at'])) >= $this->cacheTime) {
            return null;
        }

        $rate = (float)$cached['rate'];
        return $rate > 0 ? $rate : null;
    }

    private function fetchPairFromAPI($from, $to) {
        $apiKey = $this->getApiKey();
        if ($apiKey === '') {
            return null;
        }

        $url = 'https://v6.exchangerate-api.com/v6/' . rawurlencode($apiKey)
            . '/pair/' . rawurlencode($from) . '/' . rawurlencode($to);
        $data = $this->fetchJson($url);

        if ($data && ($data['result'] ?? '') === 'success' && isset($data['conversion_rate'])) {
            return (float)$data['conversion_rate'];
        }

        return null;
    }

    private function fetchJson($url) {
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 8,
                    'user_agent' => 'AxionTrustBank/1.0',
                ],
            ]);
            $response = @file_get_contents($url, false, $context);
            if ($response === false) {
                return null;
            }
            $data = json_decode($response, true);
            return is_array($data) ? $data : null;
        } catch (Exception $e) {
            error_log('ExchangeRates API error: ' . $e->getMessage());
            return null;
        }
    }

    private function cacheRate($fromCurrency, $toCurrency, $rate) {
        $from = self::normalizeCode($fromCurrency);
        $to = self::normalizeCode($toCurrency);
        $rate = (float)$rate;
        if ($rate <= 0) {
            return;
        }

        $sql = 'INSERT INTO exchange_rates (from_currency, to_currency, rate, updated_at)
                VALUES (?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE rate = VALUES(rate), updated_at = NOW()';
        try {
            $this->db->query($sql, [$from, $to, $rate]);
        } catch (Exception $e) {
            error_log('ExchangeRates cache error: ' . $e->getMessage());
        }
    }
}
