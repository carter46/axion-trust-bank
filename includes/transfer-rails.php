<?php
/**
 * Country-specific transfer rail definitions and helpers.
 * Used by transfer form, process-transfer API, and transaction receipts.
 */

/**
 * Map country name or ISO code to ISO 3166-1 alpha-2 code.
 */
function normalizeCountryCode($country) {
    $country = trim((string)$country);
    if ($country === '') {
        return 'US';
    }
    if (preg_match('/^[A-Z]{2}$/i', $country)) {
        return strtoupper($country);
    }

    static $nameToCode = [
        'Afghanistan' => 'AF', 'Albania' => 'AL', 'Algeria' => 'DZ', 'Andorra' => 'AD',
        'Angola' => 'AO', 'Argentina' => 'AR', 'Armenia' => 'AM', 'Australia' => 'AU',
        'Austria' => 'AT', 'Azerbaijan' => 'AZ', 'Bahamas' => 'BS', 'Bahrain' => 'BH',
        'Bangladesh' => 'BD', 'Barbados' => 'BB', 'Belarus' => 'BY', 'Belgium' => 'BE',
        'Belize' => 'BZ', 'Benin' => 'BJ', 'Bhutan' => 'BT', 'Bolivia' => 'BO',
        'Bosnia and Herzegovina' => 'BA', 'Botswana' => 'BW', 'Brazil' => 'BR',
        'Brunei' => 'BN', 'Bulgaria' => 'BG', 'Burkina Faso' => 'BF', 'Burundi' => 'BI',
        'Cabo Verde' => 'CV', 'Cambodia' => 'KH', 'Cameroon' => 'CM', 'Canada' => 'CA',
        'Central African Republic' => 'CF', 'Chad' => 'TD', 'Chile' => 'CL', 'China' => 'CN',
        'Colombia' => 'CO', 'Comoros' => 'KM', 'Costa Rica' => 'CR', 'Croatia' => 'HR',
        'Cuba' => 'CU', 'Cyprus' => 'CY', 'Czechia' => 'CZ', 'Czech Republic' => 'CZ',
        'Denmark' => 'DK', 'Djibouti' => 'DJ', 'Dominica' => 'DM', 'Dominican Republic' => 'DO',
        'Ecuador' => 'EC', 'Egypt' => 'EG', 'El Salvador' => 'SV', 'Estonia' => 'EE',
        'Eswatini' => 'SZ', 'Ethiopia' => 'ET', 'Fiji' => 'FJ', 'Finland' => 'FI',
        'France' => 'FR', 'Gabon' => 'GA', 'Gambia' => 'GM', 'Georgia' => 'GE',
        'Germany' => 'DE', 'Ghana' => 'GH', 'Greece' => 'GR', 'Grenada' => 'GD',
        'Guatemala' => 'GT', 'Guinea' => 'GN', 'Guyana' => 'GY', 'Haiti' => 'HT',
        'Honduras' => 'HN', 'Hungary' => 'HU', 'Iceland' => 'IS', 'India' => 'IN',
        'Indonesia' => 'ID', 'Iran' => 'IR', 'Iraq' => 'IQ', 'Ireland' => 'IE',
        'Israel' => 'IL', 'Italy' => 'IT', 'Jamaica' => 'JM', 'Japan' => 'JP',
        'Jordan' => 'JO', 'Kazakhstan' => 'KZ', 'Kenya' => 'KE', 'Kuwait' => 'KW',
        'Latvia' => 'LV', 'Lebanon' => 'LB', 'Libya' => 'LY', 'Liechtenstein' => 'LI',
        'Lithuania' => 'LT', 'Luxembourg' => 'LU', 'Madagascar' => 'MG', 'Malaysia' => 'MY',
        'Maldives' => 'MV', 'Malta' => 'MT', 'Mexico' => 'MX', 'Monaco' => 'MC',
        'Mongolia' => 'MN', 'Montenegro' => 'ME', 'Morocco' => 'MA', 'Mozambique' => 'MZ',
        'Myanmar (Burma)' => 'MM', 'Myanmar' => 'MM', 'Namibia' => 'NA', 'Nepal' => 'NP',
        'Netherlands' => 'NL', 'New Zealand' => 'NZ', 'Nicaragua' => 'NI', 'Nigeria' => 'NG',
        'North Macedonia' => 'MK', 'Norway' => 'NO', 'Oman' => 'OM', 'Pakistan' => 'PK',
        'Panama' => 'PA', 'Paraguay' => 'PY', 'Peru' => 'PE', 'Philippines' => 'PH',
        'Poland' => 'PL', 'Portugal' => 'PT', 'Qatar' => 'QA', 'Romania' => 'RO',
        'Russia' => 'RU', 'Rwanda' => 'RW', 'Saudi Arabia' => 'SA', 'Senegal' => 'SN',
        'Serbia' => 'RS', 'Singapore' => 'SG', 'Slovakia' => 'SK', 'Slovenia' => 'SI',
        'South Africa' => 'ZA', 'South Korea' => 'KR', 'Spain' => 'ES', 'Sri Lanka' => 'LK',
        'Sudan' => 'SD', 'Suriname' => 'SR', 'Sweden' => 'SE', 'Switzerland' => 'CH',
        'Syria' => 'SY', 'Taiwan' => 'TW', 'Tanzania' => 'TZ', 'Thailand' => 'TH',
        'Trinidad and Tobago' => 'TT', 'Tunisia' => 'TN', 'Turkey' => 'TR', 'Turkmenistan' => 'TM',
        'Uganda' => 'UG', 'Ukraine' => 'UA', 'United Arab Emirates' => 'AE',
        'United Kingdom' => 'GB', 'UK' => 'GB', 'Great Britain' => 'GB',
        'United States' => 'US', 'USA' => 'US', 'Uruguay' => 'UY', 'Uzbekistan' => 'UZ',
        'Venezuela' => 'VE', 'Vietnam' => 'VN', 'Yemen' => 'YE', 'Zambia' => 'ZM',
        'Zimbabwe' => 'ZW',
    ];

    if (isset($nameToCode[$country])) {
        return $nameToCode[$country];
    }

    $lower = strtolower($country);
    foreach ($nameToCode as $name => $code) {
        if (strtolower($name) === $lower) {
            return $code;
        }
    }

    return strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $country), 0, 2));
}

/**
 * SEPA zone country codes (EU + associated).
 * BIC is typically optional within SEPA; IBAN is the primary account identifier.
 */
function getSepaCountryCodes() {
    return [
        'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU',
        'IS', 'IE', 'IT', 'LV', 'LI', 'LT', 'LU', 'MT', 'MC', 'NL', 'NO', 'PL', 'PT',
        'RO', 'SM', 'SK', 'SI', 'ES', 'SE', 'CH', 'AD', 'VA', 'GI', 'ME', 'MD', 'RS',
        'MK',
    ];
}

/**
 * Countries / territories with an official IBAN format in the SWIFT ISO 13616 registry.
 * Non-IBAN countries (US, CA, AU, NZ, CN, JP, IN, most of Asia/LatAm/Africa) must NOT show IBAN.
 */
function getIbanCountryCodes() {
    return [
        // Europe / SEPA-related
        'AL', 'AD', 'AT', 'AZ', 'BA', 'BE', 'BG', 'BY', 'CH', 'CY', 'CZ', 'DE', 'DK',
        'EE', 'ES', 'FI', 'FO', 'FR', 'GB', 'GE', 'GI', 'GL', 'GR', 'HR', 'HU', 'IE',
        'IS', 'IT', 'XK', 'LI', 'LT', 'LU', 'LV', 'MC', 'MD', 'ME', 'MK', 'MT', 'NL',
        'NO', 'PL', 'PT', 'RO', 'RS', 'SE', 'SI', 'SK', 'SM', 'UA', 'VA',
        // Middle East
        'AE', 'BH', 'IL', 'IQ', 'JO', 'KW', 'LB', 'OM', 'PS', 'QA', 'SA', 'TR', 'YE',
        // Africa
        'BI', 'DJ', 'DZ', 'EG', 'KM', 'LY', 'MR', 'SD', 'SN', 'SO', 'TD', 'TG', 'TN',
        // Americas (IBAN adopters only)
        'BR', 'CR', 'DO', 'GT', 'HN', 'LC', 'NI', 'SV', 'TT', 'VG',
        // Asia (IBAN adopters only)
        'KZ', 'MN', 'PK',
        // Other
        'FK',
    ];
}

/**
 * True when destination country uses IBAN (ISO 13616 / SWIFT registry).
 */
function countryUsesIban($countryCode) {
    $code = strtoupper(trim((string)$countryCode));
    return in_array($code, getIbanCountryCodes(), true);
}

/**
 * True when destination country uses ABA-style routing numbers (United States).
 */
function countryUsesRoutingNumber($countryCode) {
    return strtoupper(trim((string)$countryCode)) === 'US';
}

/**
 * Shared field definition templates keyed by field name.
 */
function getTransferRailFieldTemplates() {
    return [
        'routing_number' => [
            'key' => 'routing_number',
            'label' => 'Routing Number',
            'placeholder' => 'Enter 9-digit routing number (optional)',
            'required' => false,
            'pattern' => '^\d{9}$',
            'maxlength' => 9,
        ],
        'transit_number' => [
            'key' => 'transit_number',
            'label' => 'Transit Number',
            'placeholder' => 'Enter 5-digit transit number',
            'required' => true,
            'pattern' => '^\d{5}$',
            'maxlength' => 5,
        ],
        'institution_number' => [
            'key' => 'institution_number',
            'label' => 'Institution Number',
            'placeholder' => 'Enter 3-digit institution number',
            'required' => true,
            'pattern' => '^\d{3}$',
            'maxlength' => 3,
        ],
        'sort_code' => [
            'key' => 'sort_code',
            'label' => 'Sort Code',
            'placeholder' => 'Enter 6-digit sort code (e.g. 12-34-56)',
            'required' => true,
            'pattern' => '^(\d{6}|\d{2}-\d{2}-\d{2})$',
            'maxlength' => 8,
        ],
        'account_number' => [
            'key' => 'account_number',
            'label' => 'Account Number',
            'placeholder' => 'Enter account number',
            'required' => true,
        ],
        'iban' => [
            'key' => 'iban',
            'label' => 'IBAN',
            'placeholder' => 'Enter IBAN (optional)',
            'required' => false,
            'minlength' => 15,
            'maxlength' => 34,
        ],
        'swift' => [
            'key' => 'swift',
            'label' => 'SWIFT/BIC Code',
            'placeholder' => 'Enter SWIFT/BIC code',
            'required' => true,
            'pattern' => '^[A-Z]{4}[A-Z]{2}[A-Z0-9]{2}([A-Z0-9]{3})?$',
            'maxlength' => 11,
        ],
        'bic' => [
            'key' => 'bic',
            'label' => 'BIC',
            'placeholder' => 'Enter BIC code (optional for SEPA)',
            'required' => false,
            'pattern' => '^[A-Z]{4}[A-Z]{2}[A-Z0-9]{2}([A-Z0-9]{3})?$',
            'maxlength' => 11,
        ],
        'beneficiary_state' => [
            'key' => 'beneficiary_state',
            'label' => 'State / Province',
            'placeholder' => 'Enter state or province (optional)',
            'required' => false,
            'maxlength' => 100,
        ],
        'interac_email' => [
            'key' => 'interac_email',
            'label' => 'Interac Email',
            'placeholder' => 'Enter recipient email for Interac e-Transfer',
            'required' => true,
            'type' => 'email',
        ],
        'interac_phone' => [
            'key' => 'interac_phone',
            'label' => 'Interac Mobile Number',
            'placeholder' => 'Enter mobile number (optional)',
            'required' => false,
        ],
        'bsb' => [
            'key' => 'bsb',
            'label' => 'BSB Code',
            'placeholder' => 'Enter 6-digit BSB code',
            'required' => true,
            'pattern' => '^\d{6}$',
            'maxlength' => 6,
        ],
        'ifsc' => [
            'key' => 'ifsc',
            'label' => 'IFSC Code',
            'placeholder' => 'Enter 11-character IFSC code',
            'required' => true,
            'pattern' => '^[A-Z]{4}0[A-Z0-9]{6}$',
            'maxlength' => 11,
        ],
    ];
}

/**
 * Country-specific overrides for rail field length/format (domestic + international).
 */
function getRailFieldCountryOverrides() {
    return [
        'AE' => [
            'iban' => [
                'minlength' => 23,
                'maxlength' => 23,
                'placeholder' => '23 characters (optional)',
            ],
        ],
        'SA' => [
            'iban' => ['minlength' => 24, 'maxlength' => 24, 'placeholder' => '24 characters (optional)'],
        ],
        'QA' => [
            'iban' => ['minlength' => 29, 'maxlength' => 29, 'placeholder' => '29 characters (optional)'],
        ],
        'KW' => [
            'iban' => ['minlength' => 30, 'maxlength' => 30, 'placeholder' => '30 characters (optional)'],
        ],
        'BH' => [
            'iban' => ['minlength' => 22, 'maxlength' => 22, 'placeholder' => '22 characters (optional)'],
        ],
        'OM' => [
            'iban' => ['minlength' => 23, 'maxlength' => 23, 'placeholder' => '23 characters (optional)'],
        ],
        'DE' => [
            'iban' => ['minlength' => 22, 'maxlength' => 22, 'placeholder' => '22 characters (optional)'],
        ],
        'FR' => [
            'iban' => ['minlength' => 27, 'maxlength' => 27, 'placeholder' => '27 characters (optional)'],
        ],
        'GB' => [
            'iban' => ['minlength' => 22, 'maxlength' => 22, 'placeholder' => '22 characters (optional)'],
        ],
        'ES' => [
            'iban' => ['minlength' => 24, 'maxlength' => 24, 'placeholder' => '24 characters (optional)'],
        ],
        'IT' => [
            'iban' => ['minlength' => 27, 'maxlength' => 27, 'placeholder' => '27 characters (optional)'],
        ],
        'NL' => [
            'iban' => ['minlength' => 18, 'maxlength' => 18, 'placeholder' => '18 characters (optional)'],
        ],
        'CH' => [
            'iban' => ['minlength' => 21, 'maxlength' => 21, 'placeholder' => '21 characters (optional)'],
        ],
        'TR' => [
            'iban' => ['minlength' => 26, 'maxlength' => 26, 'placeholder' => '26 characters (optional)'],
        ],
        'EG' => [
            'iban' => ['minlength' => 29, 'maxlength' => 29, 'placeholder' => '29 characters (optional)'],
        ],
        'BR' => [
            'iban' => ['minlength' => 29, 'maxlength' => 29, 'placeholder' => '29 characters (optional)'],
        ],
        'DO' => [
            'iban' => ['minlength' => 28, 'maxlength' => 28, 'placeholder' => '28 characters (optional)'],
        ],
        'PK' => [
            'iban' => ['minlength' => 24, 'maxlength' => 24, 'placeholder' => '24 characters (optional)'],
        ],
        'NO' => [
            'iban' => ['minlength' => 15, 'maxlength' => 15, 'placeholder' => '15 characters (optional)'],
        ],
        'BE' => [
            'iban' => ['minlength' => 16, 'maxlength' => 16, 'placeholder' => '16 characters (optional)'],
        ],
    ];
}

/**
 * Optional min/max length for domestic account number by operating country.
 */
function getDomesticAccountNumberRules($countryCode) {
    $code = strtoupper(trim((string)$countryCode));
    $rules = [
        'AE' => ['min' => 6, 'max' => 16, 'pattern' => '^\d+$', 'hint' => '6–16 digits'],
        'US' => ['min' => 4, 'max' => 17, 'pattern' => '^\d+$', 'hint' => '4–17 digits'],
        'GB' => ['min' => 8, 'max' => 8, 'pattern' => '^\d+$', 'hint' => '8 digits'],
        'IN' => ['min' => 9, 'max' => 18, 'pattern' => '^\d+$', 'hint' => '9–18 digits'],
        'NG' => ['min' => 10, 'max' => 10, 'pattern' => '^\d+$', 'hint' => '10 digits'],
    ];
    return $rules[$code] ?? ['min' => 4, 'max' => 20, 'pattern' => null, 'hint' => '4–20 characters'];
}

/**
 * Resolve field keys to full field definitions.
 *
 * @param string $context 'domestic' or 'international'
 */
function resolveRailFieldDefinitions(array $fieldKeys, $countryCode = null, $context = 'international') {
    $templates = getTransferRailFieldTemplates();
    $overrides = getRailFieldCountryOverrides();
    $code = $countryCode ? strtoupper(trim((string)$countryCode)) : null;
    $fields = [];
    foreach ($fieldKeys as $key) {
        if (!isset($templates[$key])) {
            continue;
        }
        $field = $templates[$key];
        if ($code && isset($overrides[$code][$key])) {
            $field = array_merge($field, $overrides[$code][$key]);
            if (array_key_exists('pattern', $overrides[$code][$key]) && $overrides[$code][$key]['pattern'] === null) {
                unset($field['pattern']);
            }
        }

        // Domestic US transfers still need ABA routing; international keeps it optional
        if ($key === 'routing_number' && $context === 'domestic' && $code === 'US') {
            $field['required'] = true;
            $field['placeholder'] = 'Enter 9-digit routing number';
        }

        $fields[] = $field;
    }
    return $fields;
}

/**
 * Expand raw rail config into API-ready structure with resolved field definitions.
 */
function expandRailConfig(array $config, $countryCode, $context = 'international') {
    $methods = [];
    foreach ($config['methods'] as $methodKey => $method) {
        $methods[$methodKey] = [
            'label' => $method['label'],
            'receipt_label' => $method['receipt_label'] ?? $method['label'],
            'fields' => resolveRailFieldDefinitions($method['fields'], $countryCode, $context),
        ];
    }

    return [
        'country_code' => $countryCode,
        'country_name' => $config['country_name'] ?? $countryCode,
        'default_method' => $config['default_method'],
        'methods' => $methods,
    ];
}

/**
 * Domestic rail config keyed by ISO country code.
 */
function getDomesticRailConfigByCode() {
    return [
        'CA' => [
            'country_name' => 'Canada',
            'default_method' => 'eft',
            'methods' => [
                'interac' => [
                    'label' => 'Interac e-Transfer',
                    'receipt_label' => 'Interac e-Transfer',
                    'fields' => ['interac_email', 'interac_phone'],
                ],
                'eft' => [
                    'label' => 'EFT',
                    'receipt_label' => 'EFT',
                    'fields' => ['transit_number', 'institution_number'],
                ],
                'wire' => [
                    'label' => 'Wire Transfer',
                    'receipt_label' => 'Wire Transfer',
                    'fields' => ['transit_number', 'institution_number', 'swift'],
                ],
            ],
        ],
        'US' => [
            'country_name' => 'United States',
            'default_method' => 'ach',
            'methods' => [
                'ach' => [
                    'label' => 'ACH',
                    'receipt_label' => 'ACH',
                    'fields' => ['routing_number'],
                ],
                'wire' => [
                    'label' => 'Wire Transfer',
                    'receipt_label' => 'Wire Transfer',
                    'fields' => ['routing_number', 'swift'],
                ],
            ],
        ],
        'GB' => [
            'country_name' => 'United Kingdom',
            'default_method' => 'fps',
            'methods' => [
                'fps' => [
                    'label' => 'Faster Payments (FPS)',
                    'receipt_label' => 'Faster Payments',
                    'fields' => ['sort_code'],
                ],
                'bacs' => [
                    'label' => 'BACS',
                    'receipt_label' => 'BACS',
                    'fields' => ['sort_code'],
                ],
                'chaps' => [
                    'label' => 'CHAPS',
                    'receipt_label' => 'CHAPS',
                    'fields' => ['sort_code', 'swift'],
                ],
            ],
        ],
        'AU' => [
            'country_name' => 'Australia',
            'default_method' => 'eft',
            'methods' => [
                'eft' => [
                    'label' => 'EFT / Direct Entry',
                    'receipt_label' => 'EFT',
                    'fields' => ['bsb'],
                ],
                'wire' => [
                    'label' => 'Wire Transfer',
                    'receipt_label' => 'Wire Transfer',
                    'fields' => ['bsb', 'swift'],
                ],
            ],
        ],
        'IN' => [
            'country_name' => 'India',
            'default_method' => 'neft',
            'methods' => [
                'neft' => [
                    'label' => 'NEFT/RTGS',
                    'receipt_label' => 'NEFT/RTGS',
                    'fields' => ['ifsc'],
                ],
                'wire' => [
                    'label' => 'Wire Transfer',
                    'receipt_label' => 'Wire Transfer',
                    'fields' => ['ifsc', 'swift'],
                ],
            ],
        ],
        'AE' => [
            'country_name' => 'United Arab Emirates',
            'default_method' => 'local',
            'methods' => [
                'local' => [
                    'label' => 'Local Bank Transfer',
                    'receipt_label' => 'Local Transfer',
                    // Account number is collected on the main form — no extra IBAN field for domestic UAE
                    'fields' => [],
                ],
            ],
        ],
        'SA' => [
            'country_name' => 'Saudi Arabia',
            'default_method' => 'local',
            'methods' => [
                'local' => [
                    'label' => 'Local Bank Transfer',
                    'receipt_label' => 'Local Transfer',
                    'fields' => [],
                ],
            ],
        ],
        'QA' => [
            'country_name' => 'Qatar',
            'default_method' => 'local',
            'methods' => [
                'local' => [
                    'label' => 'Local Bank Transfer',
                    'receipt_label' => 'Local Transfer',
                    'fields' => [],
                ],
            ],
        ],
        'KW' => [
            'country_name' => 'Kuwait',
            'default_method' => 'local',
            'methods' => [
                'local' => [
                    'label' => 'Local Bank Transfer',
                    'receipt_label' => 'Local Transfer',
                    'fields' => [],
                ],
            ],
        ],
        'BH' => [
            'country_name' => 'Bahrain',
            'default_method' => 'local',
            'methods' => [
                'local' => [
                    'label' => 'Local Bank Transfer',
                    'receipt_label' => 'Local Transfer',
                    'fields' => [],
                ],
            ],
        ],
        'OM' => [
            'country_name' => 'Oman',
            'default_method' => 'local',
            'methods' => [
                'local' => [
                    'label' => 'Local Bank Transfer',
                    'receipt_label' => 'Local Transfer',
                    'fields' => [],
                ],
            ],
        ],
        'NG' => [
            'country_name' => 'Nigeria',
            'default_method' => 'local',
            'methods' => [
                'local' => [
                    'label' => 'Local Bank Transfer',
                    'receipt_label' => 'Local Transfer',
                    'fields' => [],
                ],
            ],
        ],
    ];
}

/**
 * Default domestic rails for countries without explicit config.
 * Uses IBAN where applicable — not US routing/SWIFT (those are not domestic fields abroad).
 */
function getDefaultDomesticRailConfig($countryCode) {
    return [
        'country_name' => $countryCode,
        'default_method' => 'local',
        'methods' => [
            'local' => [
                'label' => 'Local Bank Transfer',
                'receipt_label' => 'Local Transfer',
                'fields' => [],
            ],
        ],
    ];
}

/**
 * International rail config keyed by ISO country code.
 * Field visibility follows real-world payment rails:
 * - IBAN only for SWIFT IBAN-registry countries
 * - Routing number only for United States
 * - SWIFT/BIC for cross-border wire identification (BIC optional inside SEPA)
 */
function getInternationalRailConfigByCode() {
    $config = [];

    // SEPA: IBAN (optional) + BIC (optional). No ABA routing.
    foreach (getSepaCountryCodes() as $code) {
        $config[$code] = [
            'default_method' => 'sepa',
            'methods' => [
                'sepa' => [
                    'label' => 'SEPA',
                    'receipt_label' => 'SEPA',
                    'fields' => ['iban', 'bic', 'beneficiary_state'],
                ],
                'swift' => [
                    'label' => 'SWIFT Wire',
                    'receipt_label' => 'SWIFT',
                    'fields' => ['swift', 'iban', 'beneficiary_state'],
                ],
            ],
        ];
    }

    // Other IBAN countries (non-SEPA): SWIFT + optional IBAN. No routing number.
    foreach (getIbanCountryCodes() as $code) {
        if (isset($config[$code])) {
            continue;
        }
        $config[$code] = [
            'default_method' => 'swift',
            'methods' => [
                'swift' => [
                    'label' => 'SWIFT Wire',
                    'receipt_label' => 'SWIFT',
                    'fields' => ['swift', 'iban', 'beneficiary_state'],
                ],
            ],
        ];
    }

    // Country-specific overrides for non-IBAN / special domestic identifiers
    $overrides = [
        'US' => [
            'default_method' => 'wire',
            'methods' => [
                'ach' => [
                    'label' => 'ACH (International)',
                    'receipt_label' => 'ACH',
                    // US does not use IBAN; routing is optional
                    'fields' => ['routing_number', 'beneficiary_state'],
                ],
                'wire' => [
                    'label' => 'SWIFT Wire',
                    'receipt_label' => 'SWIFT',
                    'fields' => ['swift', 'routing_number', 'beneficiary_state'],
                ],
            ],
        ],
        'GB' => [
            'default_method' => 'swift',
            'methods' => [
                'fps' => [
                    'label' => 'Faster Payments',
                    'receipt_label' => 'Faster Payments',
                    'fields' => ['sort_code', 'beneficiary_state'],
                ],
                'swift' => [
                    'label' => 'SWIFT Wire',
                    'receipt_label' => 'SWIFT',
                    // UK uses IBAN for international; routing number is US-only
                    'fields' => ['swift', 'sort_code', 'iban', 'beneficiary_state'],
                ],
            ],
        ],
        'CA' => [
            'default_method' => 'wire',
            'methods' => [
                'eft' => [
                    'label' => 'EFT',
                    'receipt_label' => 'EFT',
                    'fields' => ['transit_number', 'institution_number', 'beneficiary_state'],
                ],
                'wire' => [
                    'label' => 'SWIFT Wire',
                    'receipt_label' => 'SWIFT',
                    // Canada does not use IBAN or ABA routing
                    'fields' => ['swift', 'transit_number', 'institution_number', 'beneficiary_state'],
                ],
            ],
        ],
        'IN' => [
            'default_method' => 'swift',
            'methods' => [
                'swift' => [
                    'label' => 'SWIFT Wire',
                    'receipt_label' => 'SWIFT',
                    // India does not use IBAN
                    'fields' => ['swift', 'ifsc', 'beneficiary_state'],
                ],
            ],
        ],
        'AU' => [
            'default_method' => 'swift',
            'methods' => [
                'swift' => [
                    'label' => 'SWIFT Wire',
                    'receipt_label' => 'SWIFT',
                    // Australia does not use IBAN
                    'fields' => ['swift', 'bsb', 'beneficiary_state'],
                ],
            ],
        ],
        'NZ' => [
            'default_method' => 'swift',
            'methods' => [
                'swift' => [
                    'label' => 'SWIFT Wire',
                    'receipt_label' => 'SWIFT',
                    'fields' => ['swift', 'beneficiary_state'],
                ],
            ],
        ],
        'JP' => [
            'default_method' => 'swift',
            'methods' => [
                'swift' => [
                    'label' => 'SWIFT Wire',
                    'receipt_label' => 'SWIFT',
                    'fields' => ['swift', 'beneficiary_state'],
                ],
            ],
        ],
        'CN' => [
            'default_method' => 'swift',
            'methods' => [
                'swift' => [
                    'label' => 'SWIFT Wire',
                    'receipt_label' => 'SWIFT',
                    'fields' => ['swift', 'beneficiary_state'],
                ],
            ],
        ],
        'KR' => [
            'default_method' => 'swift',
            'methods' => [
                'swift' => [
                    'label' => 'SWIFT Wire',
                    'receipt_label' => 'SWIFT',
                    'fields' => ['swift', 'beneficiary_state'],
                ],
            ],
        ],
        'MX' => [
            'default_method' => 'swift',
            'methods' => [
                'swift' => [
                    'label' => 'SWIFT Wire',
                    'receipt_label' => 'SWIFT',
                    'fields' => ['swift', 'beneficiary_state'],
                ],
            ],
        ],
        'NG' => [
            'default_method' => 'swift',
            'methods' => [
                'swift' => [
                    'label' => 'SWIFT Wire',
                    'receipt_label' => 'SWIFT',
                    'fields' => ['swift', 'beneficiary_state'],
                ],
            ],
        ],
        'SG' => [
            'default_method' => 'swift',
            'methods' => [
                'swift' => [
                    'label' => 'SWIFT Wire',
                    'receipt_label' => 'SWIFT',
                    'fields' => ['swift', 'beneficiary_state'],
                ],
            ],
        ],
        'HK' => [
            'default_method' => 'swift',
            'methods' => [
                'swift' => [
                    'label' => 'SWIFT Wire',
                    'receipt_label' => 'SWIFT',
                    'fields' => ['swift', 'beneficiary_state'],
                ],
            ],
        ],
        'PH' => [
            'default_method' => 'swift',
            'methods' => [
                'swift' => [
                    'label' => 'SWIFT Wire',
                    'receipt_label' => 'SWIFT',
                    'fields' => ['swift', 'beneficiary_state'],
                ],
            ],
        ],
        'TH' => [
            'default_method' => 'swift',
            'methods' => [
                'swift' => [
                    'label' => 'SWIFT Wire',
                    'receipt_label' => 'SWIFT',
                    'fields' => ['swift', 'beneficiary_state'],
                ],
            ],
        ],
        'MY' => [
            'default_method' => 'swift',
            'methods' => [
                'swift' => [
                    'label' => 'SWIFT Wire',
                    'receipt_label' => 'SWIFT',
                    'fields' => ['swift', 'beneficiary_state'],
                ],
            ],
        ],
        'ID' => [
            'default_method' => 'swift',
            'methods' => [
                'swift' => [
                    'label' => 'SWIFT Wire',
                    'receipt_label' => 'SWIFT',
                    'fields' => ['swift', 'beneficiary_state'],
                ],
            ],
        ],
        'ZA' => [
            'default_method' => 'swift',
            'methods' => [
                'swift' => [
                    'label' => 'SWIFT Wire',
                    'receipt_label' => 'SWIFT',
                    'fields' => ['swift', 'beneficiary_state'],
                ],
            ],
        ],
    ];

    return array_merge($config, $overrides);
}

/**
 * Domestic transfer rails for the bank operating country.
 */
function getDomesticRailFields($operatingCountry) {
    $code = normalizeCountryCode($operatingCountry);
    $configs = getDomesticRailConfigByCode();
    $config = $configs[$code] ?? getDefaultDomesticRailConfig($code);
    if (!isset($config['country_name']) || $config['country_name'] === $code) {
        $config['country_name'] = is_string($operatingCountry) && strlen($operatingCountry) > 2
            ? $operatingCountry
            : $code;
    }
    return expandRailConfig($config, $code, 'domestic');
}

/**
 * International transfer rails for a destination country.
 */
function getInternationalRailFields($destCountry) {
    $code = normalizeCountryCode($destCountry);
    $configs = getInternationalRailConfigByCode();

    // Default for unknown / non-IBAN countries: SWIFT only (no IBAN, no routing)
    if (countryUsesIban($code)) {
        $defaultConfig = [
            'default_method' => 'swift',
            'methods' => [
                'swift' => [
                    'label' => 'SWIFT Wire',
                    'receipt_label' => 'SWIFT',
                    'fields' => ['swift', 'iban', 'beneficiary_state'],
                ],
            ],
        ];
    } else {
        $defaultConfig = [
            'default_method' => 'swift',
            'methods' => [
                'swift' => [
                    'label' => 'SWIFT Wire',
                    'receipt_label' => 'SWIFT',
                    'fields' => ['swift', 'beneficiary_state'],
                ],
            ],
        ];
    }

    $config = $configs[$code] ?? $defaultConfig;
    $config['country_name'] = is_string($destCountry) && strlen($destCountry) > 2 ? $destCountry : $code;
    return expandRailConfig($config, $code, 'international');
}

/**
 * Get method config for a transfer type and country.
 */
function getTransferRailMethod($type, $country, $methodKey = null) {
    $rails = $type === 'international'
        ? getInternationalRailFields($country)
        : getDomesticRailFields($country);

    $methodKey = $methodKey ?: $rails['default_method'];
    if (!isset($rails['methods'][$methodKey])) {
        $methodKey = $rails['default_method'];
    }

    return [
        'rails' => $rails,
        'method_key' => $methodKey,
        'method' => $rails['methods'][$methodKey] ?? null,
    ];
}

/**
 * Validate rail field values and return sanitized values.
 */
function validateTransferRailFields($type, $country, array $input, $methodKey = null) {
    $resolved = getTransferRailMethod($type, $country, $methodKey ?: ($input['transfer_method'] ?? null));
    $method = $resolved['method'];

    if (!$method) {
        return ['success' => false, 'message' => 'Invalid transfer method'];
    }

    $values = [];
    $errors = [];

    foreach ($method['fields'] as $field) {
        $key = $field['key'];
        $raw = isset($input[$key]) ? trim((string)$input[$key]) : '';
        $required = !empty($field['required']);

        if ($required && $raw === '') {
            $errors[] = $field['label'] . ' is required';
            continue;
        }

        if ($raw !== '' && isset($field['minlength']) && strlen($raw) < (int)$field['minlength']) {
            $errors[] = $field['label'] . ' must be at least ' . (int)$field['minlength'] . ' characters';
            continue;
        }

        if ($raw !== '' && isset($field['maxlength']) && strlen($raw) > (int)$field['maxlength']) {
            $errors[] = $field['label'] . ' must be at most ' . (int)$field['maxlength'] . ' characters';
            continue;
        }

        if ($raw !== '' && isset($field['minlength']) && isset($field['maxlength'])
            && (int)$field['minlength'] === (int)$field['maxlength']
            && strlen($raw) !== (int)$field['minlength']) {
            $errors[] = $field['label'] . ' must be exactly ' . (int)$field['minlength'] . ' characters';
            continue;
        }

        if ($raw !== '' && !empty($field['pattern'])) {
            if (!preg_match('/' . $field['pattern'] . '/i', $raw)) {
                $errors[] = $field['label'] . ' format is invalid';
                continue;
            }
        }

        if ($raw !== '') {
            $values[$key] = Security::sanitize($raw);
        }
    }

    if (!empty($errors)) {
        return ['success' => false, 'message' => implode('. ', $errors)];
    }

    return [
        'success' => true,
        'method_key' => $resolved['method_key'],
        'method' => $method,
        'rails' => $resolved['rails'],
        'values' => $values,
    ];
}

/**
 * Build transaction metadata for a transfer including rail fields.
 */
function buildTransferMetadata($type, array $data) {
    $type = strtolower(trim((string)$type));
    if (!in_array($type, ['domestic', 'international'], true)) {
        return [
            'success' => true,
            'payment_method' => null,
            'metadata' => [],
        ];
    }

    $country = $type === 'domestic'
        ? ($data['operating_country'] ?? $data['country'] ?? '')
        : ($data['country'] ?? '');

    if ($country === '') {
        return ['success' => false, 'message' => 'Country is required for transfer metadata'];
    }

    $validation = validateTransferRailFields($type, $country, $data, $data['transfer_method'] ?? null);
    if (!$validation['success']) {
        return $validation;
    }

    $metadata = [
        'transfer_scope' => $type,
        'transfer_method' => $validation['method_key'],
        'transfer_method_label' => $validation['method']['receipt_label'],
        'country_code' => $validation['rails']['country_code'],
    ];

    if ($type === 'international') {
        $metadata['region'] = Security::sanitize($data['region'] ?? '');
        $countryInfo = function_exists('getCountryByCode') ? getCountryByCode($country) : null;
        if (!$countryInfo && function_exists('getCountryByName')) {
            $countryInfo = getCountryByName($country);
        }
        $metadata['country_code'] = $countryInfo['code'] ?? normalizeCountryCode($country);
        $metadata['country'] = Security::sanitize(
            $countryInfo['name'] ?? ($data['country'] ?? $country)
        );
    } else {
        $metadata['country_code'] = $validation['rails']['country_code'];
    }

    $metadata['bank_name'] = Security::sanitize($data['bank_name'] ?? '');
    $metadata['account_number'] = Security::sanitize(
        $data['account_number'] ?? ($validation['values']['account_number'] ?? '')
    );

    foreach ($validation['values'] as $key => $value) {
        $metadata[$key] = $value;
    }

    // Legacy alias: map bic → swift for downstream consumers
    if (!empty($metadata['bic']) && empty($metadata['swift'])) {
        $metadata['swift'] = $metadata['bic'];
    }

    // Legacy compat: if client sent routing_number without rail validation path
    if ($type === 'domestic' && empty($metadata['transfer_method']) && !empty($data['routing_number'])) {
        $metadata['routing_number'] = Security::sanitize($data['routing_number']);
    }

    return [
        'success' => true,
        'payment_method' => $validation['method_key'],
        'metadata' => $metadata,
    ];
}

/**
 * Whether metadata uses the new dynamic rail format.
 */
function isDynamicRailMetadata(array $metadata) {
    if (!empty($metadata['transfer_method'])) {
        return true;
    }
    $dynamicKeys = ['transit_number', 'institution_number', 'sort_code', 'bsb', 'ifsc', 'bic', 'interac_email'];
    foreach ($dynamicKeys as $key) {
        if (!empty($metadata[$key])) {
            return true;
        }
    }
    return false;
}

/**
 * Resolve receipt field rows for a transaction (dynamic or legacy).
 *
 * @return array<int, array{label: string, value: string}>
 */
function getReceiptFields($transaction) {
    $metadata = [];
    if (is_array($transaction['metadata'] ?? null)) {
        $metadata = $transaction['metadata'];
    } else {
        $metadata = json_decode($transaction['metadata'] ?? '{}', true) ?: [];
    }

    $fields = [];

    $methodLabel = $metadata['transfer_method_label'] ?? null;
    if (!$methodLabel && !empty($metadata['transfer_method'])) {
        $methodLabel = strtoupper(str_replace('_', ' ', $metadata['transfer_method']));
    }
    if (!$methodLabel && !empty($transaction['payment_method'])) {
        $methodLabel = strtoupper(str_replace('_', ' ', $transaction['payment_method']));
    }
    if ($methodLabel) {
        $fields[] = ['label' => 'Transfer Method', 'value' => $methodLabel];
    }

    if (isDynamicRailMetadata($metadata)) {
        $type = inferTransferSubType($metadata, $transaction['category'] ?? 'transfer');
        if ($type === 'other') {
            $type = (!empty($metadata['country']) || !empty($metadata['region'])) ? 'international' : 'domestic';
        }
        $country = $type === 'international'
            ? ($metadata['country'] ?? $metadata['country_code'] ?? '')
            : ($metadata['country_code'] ?? '');

        if ($country !== '') {
            $resolved = getTransferRailMethod($type, $country, $metadata['transfer_method'] ?? null);
            $methodFields = $resolved['method']['fields'] ?? [];
            $shown = [];
            foreach ($methodFields as $fieldDef) {
                $key = $fieldDef['key'];
                if ($key === 'account_number') {
                    continue;
                }
                if (!empty($metadata[$key]) && !isset($shown[$key])) {
                    $fields[] = [
                        'label' => $fieldDef['label'],
                        'value' => (string)$metadata[$key],
                    ];
                    $shown[$key] = true;
                }
            }
        }

        foreach (['transit_number', 'institution_number', 'sort_code', 'bsb', 'ifsc', 'interac_email', 'interac_phone', 'beneficiary_state', 'iban', 'routing_number'] as $extraKey) {
            if (empty($metadata[$extraKey])) {
                continue;
            }
            $already = false;
            foreach ($fields as $f) {
                if (strcasecmp($f['label'], getTransferRailFieldTemplates()[$extraKey]['label'] ?? '') === 0) {
                    $already = true;
                    break;
                }
            }
            if (!$already) {
                $tpl = getTransferRailFieldTemplates()[$extraKey] ?? null;
                $fields[] = [
                    'label' => $tpl['label'] ?? ucwords(str_replace('_', ' ', $extraKey)),
                    'value' => (string)$metadata[$extraKey],
                ];
            }
        }
    } else {
        if (!empty($metadata['routing_number'])) {
            $fields[] = ['label' => 'Routing Number', 'value' => (string)$metadata['routing_number']];
        }
        if (!empty($metadata['swift'])) {
            $fields[] = ['label' => 'SWIFT/BIC Code', 'value' => (string)$metadata['swift']];
        }
        if (!empty($metadata['iban'])) {
            $fields[] = ['label' => 'IBAN', 'value' => (string)$metadata['iban']];
        }
    }

    return $fields;
}

/**
 * Check if transactions.payment_method column exists (cached).
 */
function transactionsHasPaymentMethodColumn() {
    static $hasColumn = null;
    if ($hasColumn !== null) {
        return $hasColumn;
    }
    try {
        $db = Database::getInstance();
        $stmt = $db->query(
            "SELECT COUNT(*) AS cnt FROM information_schema.columns
             WHERE table_schema = DATABASE() AND table_name = 'transactions' AND column_name = 'payment_method'"
        );
        $row = $stmt->fetch();
        $hasColumn = ((int)($row['cnt'] ?? 0)) > 0;
    } catch (Exception $e) {
        $hasColumn = false;
    }
    return $hasColumn;
}

/**
 * Infer transfer sub-type from metadata (backward compatible).
 */
function inferTransferSubType(array $metadata, $category = 'transfer') {
    if ($category !== 'transfer') {
        return 'other';
    }

    foreach (['transfer_scope', 'transfer_type'] as $key) {
        if (!empty($metadata[$key])) {
            $scope = strtolower(trim((string)$metadata[$key]));
            if (in_array($scope, ['domestic', 'international', 'internal'], true)) {
                return $scope;
            }
        }
    }

    // Legacy heuristics — country/region name only set on international transfers
    if (!empty($metadata['region']) || !empty($metadata['country'])) {
        return 'international';
    }
    if (!empty($metadata['routing_number']) || !empty($metadata['transit_number'])
        || !empty($metadata['institution_number']) || !empty($metadata['sort_code'])
        || !empty($metadata['bsb']) || !empty($metadata['ifsc']) || !empty($metadata['iban'])
        || !empty($metadata['interac_email']) || !empty($metadata['transfer_method'])) {
        return 'domestic';
    }
    if (!empty($metadata['swift']) || !empty($metadata['bic'])) {
        return 'international';
    }

    return 'internal';
}

/**
 * Human-readable receipt title for a transfer transaction.
 */
function getTransferReceiptTitle($transferType, array $metadata = []) {
    $transferType = strtolower(trim((string)$transferType));
    if ($transferType === 'international') {
        return 'Receipt for International Wire Transfer';
    }
    if ($transferType === 'domestic') {
        $methodLabel = trim((string)($metadata['transfer_method_label'] ?? ''));
        return $methodLabel !== '' ? 'Receipt for Domestic ' . $methodLabel : 'Receipt for Domestic Transfer';
    }
    if ($transferType === 'internal') {
        return 'Receipt for Internal Transfer';
    }
    return 'Receipt for Transfer';
}
