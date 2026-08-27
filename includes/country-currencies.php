<?php
/**
 * ISO country → currency helpers for admin currency assignment and display.
 */

/**
 * Primary ISO-4217 currency for each ISO-3166 alpha-2 country in the app catalog.
 */
function getCountryPrimaryCurrencyMap() {
    return [
        // North America / Caribbean
        'US' => 'USD', 'CA' => 'CAD', 'MX' => 'MXN', 'GT' => 'GTQ', 'BZ' => 'BZD',
        'HN' => 'HNL', 'SV' => 'USD', 'NI' => 'NIO', 'CR' => 'CRC', 'PA' => 'USD',
        'CU' => 'CUP', 'JM' => 'JMD', 'HT' => 'HTG', 'DO' => 'DOP', 'BS' => 'BSD',
        'BB' => 'BBD', 'TT' => 'TTD', 'AG' => 'XCD', 'LC' => 'XCD', 'VC' => 'XCD',
        'GD' => 'XCD', 'DM' => 'XCD', 'KN' => 'XCD', 'BM' => 'BMD', 'GL' => 'DKK',
        // South America / Caribbean territories
        'BR' => 'BRL', 'AR' => 'ARS', 'CO' => 'COP', 'CL' => 'CLP', 'PE' => 'PEN',
        'VE' => 'VES', 'EC' => 'USD', 'BO' => 'BOB', 'PY' => 'PYG', 'UY' => 'UYU',
        'GY' => 'GYD', 'SR' => 'SRD', 'FK' => 'FKP', 'GF' => 'EUR', 'AW' => 'AWG',
        'CW' => 'ANG', 'BQ' => 'USD', 'SX' => 'ANG', 'PR' => 'USD', 'VI' => 'USD',
        'KY' => 'KYD', 'TC' => 'USD', 'MS' => 'XCD', 'AI' => 'XCD', 'VG' => 'USD',
        // Europe
        'GB' => 'GBP', 'DE' => 'EUR', 'FR' => 'EUR', 'IT' => 'EUR', 'ES' => 'EUR',
        'NL' => 'EUR', 'CH' => 'CHF', 'BE' => 'EUR', 'AT' => 'EUR', 'SE' => 'SEK',
        'NO' => 'NOK', 'DK' => 'DKK', 'FI' => 'EUR', 'PL' => 'PLN', 'IE' => 'EUR',
        'PT' => 'EUR', 'GR' => 'EUR', 'CZ' => 'CZK', 'RO' => 'RON', 'HU' => 'HUF',
        'BG' => 'BGN', 'HR' => 'EUR', 'SK' => 'EUR', 'SI' => 'EUR', 'LT' => 'EUR',
        'LV' => 'EUR', 'EE' => 'EUR', 'LU' => 'EUR', 'MT' => 'EUR', 'IS' => 'ISK',
        'LI' => 'CHF', 'MC' => 'EUR', 'AD' => 'EUR', 'SM' => 'EUR', 'VA' => 'EUR',
        'BY' => 'BYN', 'UA' => 'UAH', 'MD' => 'MDL', 'RS' => 'RSD', 'ME' => 'EUR',
        'AL' => 'ALL', 'MK' => 'MKD', 'BA' => 'BAM', 'XK' => 'EUR', 'RU' => 'RUB',
        // Asia
        'CN' => 'CNY', 'JP' => 'JPY', 'IN' => 'INR', 'KR' => 'KRW', 'ID' => 'IDR',
        'TH' => 'THB', 'MY' => 'MYR', 'PH' => 'PHP', 'VN' => 'VND', 'PK' => 'PKR',
        'BD' => 'BDT', 'TW' => 'TWD', 'SG' => 'SGD', 'HK' => 'HKD', 'LK' => 'LKR',
        'NP' => 'NPR', 'MM' => 'MMK', 'KH' => 'KHR', 'LA' => 'LAK', 'MN' => 'MNT',
        'KZ' => 'KZT', 'UZ' => 'UZS', 'KG' => 'KGS', 'TJ' => 'TJS', 'TM' => 'TMT',
        'BT' => 'BTN', 'MV' => 'MVR', 'BN' => 'BND', 'TL' => 'USD', 'KP' => 'KPW',
        // Africa
        'NG' => 'NGN', 'ZA' => 'ZAR', 'KE' => 'KES', 'GH' => 'GHS', 'ET' => 'ETB',
        'TZ' => 'TZS', 'UG' => 'UGX', 'SS' => 'SSP', 'AO' => 'AOA', 'MZ' => 'MZN',
        'ZM' => 'ZMW', 'ZW' => 'ZWL', 'BW' => 'BWP', 'NA' => 'NAD', 'SN' => 'XOF',
        'CI' => 'XOF', 'CM' => 'XAF', 'CD' => 'CDF', 'CG' => 'XAF', 'RW' => 'RWF',
        'BI' => 'BIF', 'ML' => 'XOF', 'NE' => 'XOF', 'TD' => 'XAF', 'MR' => 'MRU',
        'SO' => 'SOS', 'ER' => 'ERN', 'DJ' => 'DJF', 'MG' => 'MGA', 'MW' => 'MWK',
        'LS' => 'LSL', 'SZ' => 'SZL', 'GN' => 'GNF', 'SL' => 'SLE', 'LR' => 'LRD',
        'TG' => 'XOF', 'BJ' => 'XOF', 'BF' => 'XOF', 'GA' => 'XAF', 'GQ' => 'XAF',
        'CF' => 'XAF', 'GM' => 'GMD', 'GW' => 'XOF', 'CV' => 'CVE', 'ST' => 'STN',
        'SC' => 'SCR', 'MU' => 'MUR', 'KM' => 'KMF',
        // Oceania
        'AU' => 'AUD', 'NZ' => 'NZD', 'FJ' => 'FJD', 'PG' => 'PGK', 'WS' => 'WST',
        'TO' => 'TOP', 'VU' => 'VUV', 'SB' => 'SBD', 'KI' => 'AUD', 'TV' => 'AUD',
        'NR' => 'AUD', 'PW' => 'USD', 'FM' => 'USD', 'MH' => 'USD', 'GU' => 'USD',
        'NC' => 'XPF', 'PF' => 'XPF', 'AS' => 'USD', 'CK' => 'NZD', 'NU' => 'NZD',
        'TK' => 'NZD', 'WF' => 'XPF', 'MP' => 'USD', 'PN' => 'NZD', 'NF' => 'AUD',
        // Middle East / North Africa
        'IR' => 'IRR', 'IQ' => 'IQD', 'YE' => 'YER', 'SY' => 'SYP', 'PS' => 'ILS',
        'AE' => 'AED', 'SA' => 'SAR', 'QA' => 'QAR', 'KW' => 'KWD', 'OM' => 'OMR',
        'BH' => 'BHD', 'JO' => 'JOD', 'LB' => 'LBP', 'IL' => 'ILS', 'TR' => 'TRY',
        'CY' => 'EUR', 'AF' => 'AFN', 'AM' => 'AMD', 'AZ' => 'AZN', 'GE' => 'GEL',
        'EG' => 'EGP', 'LY' => 'LYD', 'DZ' => 'DZD', 'TN' => 'TND', 'MA' => 'MAD',
        'SD' => 'SDG',
    ];
}

/**
 * Currency code => English name for every currency used by catalog countries.
 */
function getAllCurrencyNames() {
    return [
        'USD' => 'US Dollar', 'CAD' => 'Canadian Dollar', 'MXN' => 'Mexican Peso',
        'GTQ' => 'Guatemalan Quetzal', 'BZD' => 'Belize Dollar', 'HNL' => 'Honduran Lempira',
        'NIO' => 'Nicaraguan Córdoba', 'CRC' => 'Costa Rican Colón', 'CUP' => 'Cuban Peso',
        'JMD' => 'Jamaican Dollar', 'HTG' => 'Haitian Gourde', 'DOP' => 'Dominican Peso',
        'BSD' => 'Bahamian Dollar', 'BBD' => 'Barbadian Dollar', 'TTD' => 'Trinidad and Tobago Dollar',
        'XCD' => 'East Caribbean Dollar', 'BMD' => 'Bermudian Dollar', 'DKK' => 'Danish Krone',
        'BRL' => 'Brazilian Real', 'ARS' => 'Argentine Peso', 'COP' => 'Colombian Peso',
        'CLP' => 'Chilean Peso', 'PEN' => 'Peruvian Sol', 'VES' => 'Venezuelan Bolívar',
        'BOB' => 'Bolivian Boliviano', 'PYG' => 'Paraguayan Guaraní', 'UYU' => 'Uruguayan Peso',
        'GYD' => 'Guyanese Dollar', 'SRD' => 'Surinamese Dollar', 'FKP' => 'Falkland Islands Pound',
        'EUR' => 'Euro', 'AWG' => 'Aruban Florin', 'ANG' => 'Netherlands Antillean Guilder',
        'KYD' => 'Cayman Islands Dollar', 'GBP' => 'British Pound', 'CHF' => 'Swiss Franc',
        'SEK' => 'Swedish Krona', 'NOK' => 'Norwegian Krone', 'PLN' => 'Polish Zloty',
        'CZK' => 'Czech Koruna', 'RON' => 'Romanian Leu', 'HUF' => 'Hungarian Forint',
        'BGN' => 'Bulgarian Lev', 'ISK' => 'Icelandic Króna', 'BYN' => 'Belarusian Ruble',
        'UAH' => 'Ukrainian Hryvnia', 'MDL' => 'Moldovan Leu', 'RSD' => 'Serbian Dinar',
        'ALL' => 'Albanian Lek', 'MKD' => 'Macedonian Denar', 'BAM' => 'Bosnia-Herzegovina Convertible Mark',
        'RUB' => 'Russian Ruble', 'CNY' => 'Chinese Yuan', 'JPY' => 'Japanese Yen',
        'INR' => 'Indian Rupee', 'KRW' => 'South Korean Won', 'IDR' => 'Indonesian Rupiah',
        'THB' => 'Thai Baht', 'MYR' => 'Malaysian Ringgit', 'PHP' => 'Philippine Peso',
        'VND' => 'Vietnamese Dong', 'PKR' => 'Pakistani Rupee', 'BDT' => 'Bangladeshi Taka',
        'TWD' => 'Taiwan Dollar', 'SGD' => 'Singapore Dollar', 'HKD' => 'Hong Kong Dollar',
        'LKR' => 'Sri Lankan Rupee', 'NPR' => 'Nepalese Rupee', 'MMK' => 'Myanmar Kyat',
        'KHR' => 'Cambodian Riel', 'LAK' => 'Lao Kip', 'MNT' => 'Mongolian Tögrög',
        'KZT' => 'Kazakhstani Tenge', 'UZS' => 'Uzbekistani Som', 'KGS' => 'Kyrgyzstani Som',
        'TJS' => 'Tajikistani Somoni', 'TMT' => 'Turkmenistani Manat', 'BTN' => 'Bhutanese Ngultrum',
        'MVR' => 'Maldivian Rufiyaa', 'BND' => 'Brunei Dollar', 'KPW' => 'North Korean Won',
        'NGN' => 'Nigerian Naira', 'ZAR' => 'South African Rand', 'KES' => 'Kenyan Shilling',
        'GHS' => 'Ghanaian Cedi', 'ETB' => 'Ethiopian Birr', 'TZS' => 'Tanzanian Shilling',
        'UGX' => 'Ugandan Shilling', 'SSP' => 'South Sudanese Pound', 'AOA' => 'Angolan Kwanza',
        'MZN' => 'Mozambican Metical', 'ZMW' => 'Zambian Kwacha', 'ZWL' => 'Zimbabwean Dollar',
        'BWP' => 'Botswana Pula', 'NAD' => 'Namibian Dollar', 'XOF' => 'West African CFA Franc',
        'XAF' => 'Central African CFA Franc', 'CDF' => 'Congolese Franc', 'RWF' => 'Rwandan Franc',
        'BIF' => 'Burundian Franc', 'MRU' => 'Mauritanian Ouguiya', 'SOS' => 'Somali Shilling',
        'ERN' => 'Eritrean Nakfa', 'DJF' => 'Djiboutian Franc', 'MGA' => 'Malagasy Ariary',
        'MWK' => 'Malawian Kwacha', 'LSL' => 'Lesotho Loti', 'SZL' => 'Swazi Lilangeni',
        'GNF' => 'Guinean Franc', 'SLE' => 'Sierra Leonean Leone', 'LRD' => 'Liberian Dollar',
        'GMD' => 'Gambian Dalasi', 'CVE' => 'Cape Verdean Escudo', 'STN' => 'São Tomé and Príncipe Dobra',
        'SCR' => 'Seychellois Rupee', 'MUR' => 'Mauritian Rupee', 'KMF' => 'Comorian Franc',
        'AUD' => 'Australian Dollar', 'NZD' => 'New Zealand Dollar', 'FJD' => 'Fijian Dollar',
        'PGK' => 'Papua New Guinean Kina', 'WST' => 'Samoan Tala', 'TOP' => 'Tongan Paʻanga',
        'VUV' => 'Vanuatu Vatu', 'SBD' => 'Solomon Islands Dollar', 'XPF' => 'CFP Franc',
        'IRR' => 'Iranian Rial', 'IQD' => 'Iraqi Dinar', 'YER' => 'Yemeni Rial',
        'SYP' => 'Syrian Pound', 'ILS' => 'Israeli Shekel', 'AED' => 'UAE Dirham',
        'SAR' => 'Saudi Riyal', 'QAR' => 'Qatari Riyal', 'KWD' => 'Kuwaiti Dinar',
        'OMR' => 'Omani Rial', 'BHD' => 'Bahraini Dinar', 'JOD' => 'Jordanian Dinar',
        'LBP' => 'Lebanese Pound', 'TRY' => 'Turkish Lira', 'AFN' => 'Afghan Afghani',
        'AMD' => 'Armenian Dram', 'AZN' => 'Azerbaijani Manat', 'GEL' => 'Georgian Lari',
        'EGP' => 'Egyptian Pound', 'LYD' => 'Libyan Dinar', 'DZD' => 'Algerian Dinar',
        'TND' => 'Tunisian Dinar', 'MAD' => 'Moroccan Dirham', 'SDG' => 'Sudanese Pound',
    ];
}

/**
 * All supported display currencies for admin assignment (sorted by code).
 */
function getFullSupportedCurrencies() {
    $names = getAllCurrencyNames();
    $used = [];
    foreach (getCountryPrimaryCurrencyMap() as $currency) {
        $used[$currency] = true;
    }
    $out = [];
    foreach (array_keys($used) as $code) {
        $out[$code] = $names[$code] ?? $code;
    }
    ksort($out);
    return $out;
}
