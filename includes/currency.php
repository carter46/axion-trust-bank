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
        return [
            'USD' => 'US Dollar', 'EUR' => 'Euro', 'GBP' => 'British Pound',
            'CAD' => 'Canadian Dollar', 'AUD' => 'Australian Dollar', 'JPY' => 'Japanese Yen',
            'CNY' => 'Chinese Yuan', 'INR' => 'Indian Rupee', 'NGN' => 'Nigerian Naira',
            'ZAR' => 'South African Rand', 'AED' => 'UAE Dirham', 'SAR' => 'Saudi Riyal',
            'QAR' => 'Qatari Riyal', 'KWD' => 'Kuwaiti Dinar', 'BRL' => 'Brazilian Real',
            'MXN' => 'Mexican Peso', 'SGD' => 'Singapore Dollar', 'HKD' => 'Hong Kong Dollar',
            'KRW' => 'South Korean Won', 'CHF' => 'Swiss Franc', 'SEK' => 'Swedish Krona',
            'NOK' => 'Norwegian Krone', 'DKK' => 'Danish Krone', 'THB' => 'Thai Baht',
            'MYR' => 'Malaysian Ringgit', 'IDR' => 'Indonesian Rupiah', 'PHP' => 'Philippine Peso',
            'EGP' => 'Egyptian Pound', 'TRY' => 'Turkish Lira', 'PLN' => 'Polish Zloty',
            'CZK' => 'Czech Koruna', 'HUF' => 'Hungarian Forint', 'RON' => 'Romanian Leu',
            'BGN' => 'Bulgarian Lev', 'RUB' => 'Russian Ruble', 'ILS' => 'Israeli Shekel',
            'NZD' => 'New Zealand Dollar', 'ARS' => 'Argentine Peso', 'CLP' => 'Chilean Peso',
            'COP' => 'Colombian Peso', 'PEN' => 'Peruvian Sol', 'VND' => 'Vietnamese Dong',
            'TWD' => 'Taiwan Dollar', 'PKR' => 'Pakistani Rupee', 'BDT' => 'Bangladeshi Taka',
            'LKR' => 'Sri Lankan Rupee', 'KES' => 'Kenyan Shilling', 'GHS' => 'Ghanaian Cedi',
            'XOF' => 'West African CFA Franc', 'ZMW' => 'Zambian Kwacha', 'JMD' => 'Jamaican Dollar',
            'BBD' => 'Barbadian Dollar', 'BZD' => 'Belize Dollar', 'BND' => 'Brunei Dollar',
            'FJD' => 'Fijian Dollar', 'GYD' => 'Guyanese Dollar', 'LRD' => 'Liberian Dollar',
            'SBD' => 'Solomon Islands Dollar', 'SRD' => 'Surinamese Dollar',
            'TTD' => 'Trinidad and Tobago Dollar', 'XCD' => 'East Caribbean Dollar',
            'AWG' => 'Aruban Florin', 'BMD' => 'Bermudian Dollar', 'BSD' => 'Bahamian Dollar',
            'KYD' => 'Cayman Islands Dollar', 'ANG' => 'Netherlands Antillean Guilder',
        ];
    }
}
