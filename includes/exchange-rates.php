<?php
/**
 * Single source of truth for exchange rates (ExchangeRate-API v6).
 * All conversions and rate lookups should go through this class.
 */

class ExchangeRates {
    private static $instance = null;
    private $db;
    private $cacheTime = 3600;

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
     * API key from config; falls back to legacy project key when env is unset.
     */
    public function getApiKey() {
        $key = defined('EXCHANGE_RATE_API_KEY') ? trim((string)EXCHANGE_RATE_API_KEY) : '';
        if ($key === '' || $key === 'your-api-key') {
            return '237f6e67ee2389de0cc0e4f5';
        }
        return $key;
    }

    /**
     * Rate to multiply amount in $from to get amount in $to.
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

        error_log("ExchangeRates: no rate for {$from} -> {$to}, refusing silent 1.0");
        return 1.0;
    }

    public function convert($amount, $fromCurrency, $toCurrency) {
        $from = self::normalizeCode($fromCurrency);
        $to = self::normalizeCode($toCurrency);
        $amount = (float)$amount;

        if ($from === $to) {
            return round($amount, 2);
        }

        return round($amount * $this->getRate($from, $to), 2);
    }

    /**
     * Refresh all rates from a base currency into exchange_rates cache.
     */
    public function refreshRates($baseCurrency = null) {
        $base = self::normalizeCode($baseCurrency ?? (defined('DEFAULT_CURRENCY') ? DEFAULT_CURRENCY : 'USD'));
        $url = 'https://v6.exchangerate-api.com/v6/' . rawurlencode($this->getApiKey()) . '/latest/' . rawurlencode($base);

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

        return null;
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
        $url = 'https://v6.exchangerate-api.com/v6/' . rawurlencode($this->getApiKey())
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
