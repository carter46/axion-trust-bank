<?php
/**
 * Currency helpers — formatting, supported codes, system currency.
 * Exchange rates: see includes/exchange-rates.php (single source of truth).
 */

class Currency {
    private $db;
    private $rates;

    public function __construct() {
        $this->db = Database::getInstance();
        require_once __DIR__ . '/exchange-rates.php';
        $this->rates = ExchangeRates::getInstance();
    }

    public function getExchangeRate($fromCurrency, $toCurrency) {
        return $this->rates->getRate($fromCurrency, $toCurrency);
    }

    public function convert($amount, $fromCurrency, $toCurrency) {
        return $this->rates->convert($amount, $fromCurrency, $toCurrency);
    }

    public function refreshRates($baseCurrency = null) {
        if ($baseCurrency === null) {
            $baseCurrency = $this->getSystemCurrency();
        }
        return $this->rates->refreshRates($baseCurrency);
    }

    public function format($amount, $currency) {
        $symbols = [
            'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'JPY' => '¥',
            'CNY' => '¥', 'INR' => '₹', 'CAD' => 'CA$', 'AUD' => 'A$',
            'NGN' => '₦', 'ZAR' => 'R', 'AED' => 'د.إ', 'SAR' => 'ر.س',
            'QAR' => 'ر.ق', 'KWD' => 'د.ك', 'BRL' => 'R$', 'MXN' => 'MX$',
            'SGD' => 'S$', 'HKD' => 'HK$', 'KRW' => '₩', 'CHF' => 'CHF',
            'THB' => '฿', 'TRY' => '₺', 'PLN' => 'zł', 'RUB' => '₽',
            'ILS' => '₪', 'NZD' => 'NZ$', 'ARS' => '$', 'VND' => '₫',
            'PKR' => '₨', 'BDT' => '৳', 'KES' => 'KSh', 'GHS' => '₵',
        ];

        $code = ExchangeRates::normalizeCode($currency);
        $symbol = $symbols[$code] ?? $code . ' ';

        if (in_array($code, ['JPY', 'KRW', 'VND'], true)) {
            return $symbol . number_format((float)$amount, 0);
        }

        return $symbol . number_format((float)$amount, 2);
    }

    public function getSystemCurrency() {
        $sql = "SELECT setting_value FROM system_settings WHERE setting_key = 'default_currency' LIMIT 1";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result ? ExchangeRates::normalizeCode($result['setting_value']) : 'USD';
    }

    public function getSupportedCurrencies() {
        if (!function_exists('getFullSupportedCurrencies')) {
            require_once __DIR__ . '/country-currencies.php';
        }
        return getFullSupportedCurrencies();
    }
}
