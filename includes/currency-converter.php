<?php
/**
 * Country/IP → currency mapping. Rate conversion delegates to ExchangeRates.
 */

class CurrencyConverter {
    private $rates;

    private $countryToCurrency = [
        'US' => 'USD', 'CA' => 'CAD', 'GB' => 'GBP', 'AU' => 'AUD',
        'NG' => 'NGN', 'KE' => 'KES', 'ZA' => 'ZAR', 'GH' => 'GHS',
        'IN' => 'INR', 'PK' => 'PKR', 'BD' => 'BDT', 'LK' => 'LKR',
        'CN' => 'CNY', 'JP' => 'JPY', 'KR' => 'KRW', 'SG' => 'SGD',
        'MY' => 'MYR', 'TH' => 'THB', 'ID' => 'IDR', 'PH' => 'PHP',
        'VN' => 'VND', 'EU' => 'EUR', 'DE' => 'EUR', 'FR' => 'EUR',
        'IT' => 'EUR', 'ES' => 'EUR', 'NL' => 'EUR', 'BE' => 'EUR',
        'CH' => 'CHF', 'SE' => 'SEK', 'NO' => 'NOK', 'DK' => 'DKK',
        'AE' => 'AED', 'SA' => 'SAR', 'QA' => 'QAR', 'KW' => 'KWD',
        'BH' => 'BHD', 'OM' => 'OMR', 'JO' => 'JOD', 'LB' => 'LBP',
        'EG' => 'EGP', 'MA' => 'MAD', 'TN' => 'TND', 'DZ' => 'DZD',
        'BR' => 'BRL', 'MX' => 'MXN', 'AR' => 'ARS', 'CL' => 'CLP',
        'CO' => 'COP', 'PE' => 'PEN', 'VE' => 'VES', 'TR' => 'TRY',
        'IL' => 'ILS', 'NZ' => 'NZD', 'HK' => 'HKD', 'TW' => 'TWD',
    ];

    public function __construct() {
        require_once __DIR__ . '/exchange-rates.php';
        $this->rates = ExchangeRates::getInstance();
    }

    public function getCountryFromIP($ip = null) {
        if (!$ip) {
            $ip = $this->getClientIP();
        }

        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return 'US';
        }

        try {
            $url = "http://ip-api.com/json/{$ip}?fields=status,countryCode";
            $response = @file_get_contents($url, false, stream_context_create([
                'http' => ['timeout' => 3, 'user_agent' => 'Banking App'],
            ]));

            if ($response) {
                $data = json_decode($response, true);
                if ($data && ($data['status'] ?? '') === 'success' && !empty($data['countryCode'])) {
                    return $data['countryCode'];
                }
            }
        } catch (Exception $e) {
            error_log('IP Geolocation Error: ' . $e->getMessage());
        }

        return 'US';
    }

    private function getClientIP() {
        $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];

        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                foreach (explode(',', (string)$_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                        return $ip;
                    }
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public function getCurrencyFromCountry($countryCode) {
        return $this->countryToCurrency[strtoupper(trim((string)$countryCode))] ?? 'USD';
    }

    /**
     * @deprecated Use ExchangeRates::getInstance()->getRate($from, $to)
     */
    public function getExchangeRate($toCurrency, $fromCurrency = 'USD') {
        return $this->rates->getRate($fromCurrency, $toCurrency);
    }

    public function convert($amount, $fromCurrency, $toCurrency) {
        return $this->rates->convert($amount, $fromCurrency, $toCurrency);
    }

    public function format($amount, $currency) {
        require_once __DIR__ . '/currency.php';
        return (new Currency())->format($amount, $currency);
    }

    public function getCurrencyForUser($ip = null) {
        return $this->getCurrencyFromCountry($this->getCountryFromIP($ip));
    }

    public function refreshAllRates() {
        $base = defined('DEFAULT_CURRENCY') ? DEFAULT_CURRENCY : 'USD';
        return $this->rates->refreshRates($base);
    }
}
