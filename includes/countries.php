<?php
/**
 * Canonical country list (ISO-3166 alpha-2 codes with display names and regions).
 * Used for registration, transfers, and admin country dropdowns.
 */

function getCountriesData() {
    static $countries = null;
    if ($countries !== null) {
        return $countries;
    }

    $regions = [
        'north-america' => [
            'US' => 'United States',
            'CA' => 'Canada',
            'MX' => 'Mexico',
            'GT' => 'Guatemala',
            'BZ' => 'Belize',
            'HN' => 'Honduras',
            'SV' => 'El Salvador',
            'NI' => 'Nicaragua',
            'CR' => 'Costa Rica',
            'PA' => 'Panama',
            'CU' => 'Cuba',
            'JM' => 'Jamaica',
            'HT' => 'Haiti',
            'DO' => 'Dominican Republic',
            'BS' => 'Bahamas',
            'BB' => 'Barbados',
            'TT' => 'Trinidad and Tobago',
            'AG' => 'Antigua and Barbuda',
            'LC' => 'Saint Lucia',
            'VC' => 'Saint Vincent and the Grenadines',
            'GD' => 'Grenada',
            'DM' => 'Dominica',
            'KN' => 'Saint Kitts and Nevis',
            'BM' => 'Bermuda',
            'GL' => 'Greenland',
        ],
        'south-america' => [
            'BR' => 'Brazil',
            'AR' => 'Argentina',
            'CO' => 'Colombia',
            'CL' => 'Chile',
            'PE' => 'Peru',
            'VE' => 'Venezuela',
            'EC' => 'Ecuador',
            'BO' => 'Bolivia',
            'PY' => 'Paraguay',
            'UY' => 'Uruguay',
            'GY' => 'Guyana',
            'SR' => 'Suriname',
            'FK' => 'Falkland Islands',
            'GF' => 'French Guiana',
            'AW' => 'Aruba',
            'CW' => 'Curaçao',
            'BQ' => 'Caribbean Netherlands',
            'SX' => 'Sint Maarten',
            'PR' => 'Puerto Rico',
            'VI' => 'U.S. Virgin Islands',
            'KY' => 'Cayman Islands',
            'TC' => 'Turks and Caicos Islands',
            'MS' => 'Montserrat',
            'AI' => 'Anguilla',
            'VG' => 'British Virgin Islands',
        ],
        'europe' => [
            'GB' => 'United Kingdom',
            'DE' => 'Germany',
            'FR' => 'France',
            'IT' => 'Italy',
            'ES' => 'Spain',
            'NL' => 'Netherlands',
            'CH' => 'Switzerland',
            'BE' => 'Belgium',
            'AT' => 'Austria',
            'SE' => 'Sweden',
            'NO' => 'Norway',
            'DK' => 'Denmark',
            'FI' => 'Finland',
            'PL' => 'Poland',
            'IE' => 'Ireland',
            'PT' => 'Portugal',
            'GR' => 'Greece',
            'CZ' => 'Czechia',
            'RO' => 'Romania',
            'HU' => 'Hungary',
            'BG' => 'Bulgaria',
            'HR' => 'Croatia',
            'SK' => 'Slovakia',
            'SI' => 'Slovenia',
            'LT' => 'Lithuania',
            'LV' => 'Latvia',
            'EE' => 'Estonia',
            'LU' => 'Luxembourg',
            'MT' => 'Malta',
            'IS' => 'Iceland',
            'LI' => 'Liechtenstein',
            'MC' => 'Monaco',
            'AD' => 'Andorra',
            'SM' => 'San Marino',
            'VA' => 'Vatican City',
            'BY' => 'Belarus',
            'UA' => 'Ukraine',
            'MD' => 'Moldova',
            'RS' => 'Serbia',
            'ME' => 'Montenegro',
            'AL' => 'Albania',
            'MK' => 'North Macedonia',
            'BA' => 'Bosnia and Herzegovina',
            'XK' => 'Kosovo',
            'RU' => 'Russia',
        ],
        'asia' => [
            'CN' => 'China',
            'JP' => 'Japan',
            'IN' => 'India',
            'KR' => 'South Korea',
            'ID' => 'Indonesia',
            'TH' => 'Thailand',
            'MY' => 'Malaysia',
            'PH' => 'Philippines',
            'VN' => 'Vietnam',
            'PK' => 'Pakistan',
            'BD' => 'Bangladesh',
            'TW' => 'Taiwan',
            'SG' => 'Singapore',
            'HK' => 'Hong Kong',
            'LK' => 'Sri Lanka',
            'NP' => 'Nepal',
            'MM' => 'Myanmar (Burma)',
            'KH' => 'Cambodia',
            'LA' => 'Laos',
            'MN' => 'Mongolia',
            'KZ' => 'Kazakhstan',
            'UZ' => 'Uzbekistan',
            'KG' => 'Kyrgyzstan',
            'TJ' => 'Tajikistan',
            'TM' => 'Turkmenistan',
            'BT' => 'Bhutan',
            'MV' => 'Maldives',
            'BN' => 'Brunei',
            'TL' => 'Timor-Leste',
            'KP' => 'North Korea',
        ],
        'africa' => [
            'NG' => 'Nigeria',
            'ZA' => 'South Africa',
            'KE' => 'Kenya',
            'GH' => 'Ghana',
            'ET' => 'Ethiopia',
            'TZ' => 'Tanzania',
            'UG' => 'Uganda',
            'SS' => 'South Sudan',
            'AO' => 'Angola',
            'MZ' => 'Mozambique',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe',
            'BW' => 'Botswana',
            'NA' => 'Namibia',
            'SN' => 'Senegal',
            'CI' => 'Côte d’Ivoire',
            'CM' => 'Cameroon',
            'CD' => 'Democratic Republic of the Congo',
            'CG' => 'Congo (Congo-Brazzaville)',
            'RW' => 'Rwanda',
            'BI' => 'Burundi',
            'ML' => 'Mali',
            'NE' => 'Niger',
            'TD' => 'Chad',
            'MR' => 'Mauritania',
            'SO' => 'Somalia',
            'ER' => 'Eritrea',
            'DJ' => 'Djibouti',
            'MG' => 'Madagascar',
            'MW' => 'Malawi',
            'LS' => 'Lesotho',
            'SZ' => 'Eswatini',
            'GN' => 'Guinea',
            'SL' => 'Sierra Leone',
            'LR' => 'Liberia',
            'TG' => 'Togo',
            'BJ' => 'Benin',
            'BF' => 'Burkina Faso',
            'GA' => 'Gabon',
            'GQ' => 'Equatorial Guinea',
            'CF' => 'Central African Republic',
            'GM' => 'Gambia',
            'GW' => 'Guinea-Bissau',
            'CV' => 'Cabo Verde',
            'ST' => 'Sao Tome and Principe',
            'SC' => 'Seychelles',
            'MU' => 'Mauritius',
            'KM' => 'Comoros',
        ],
        'oceania' => [
            'AU' => 'Australia',
            'NZ' => 'New Zealand',
            'FJ' => 'Fiji',
            'PG' => 'Papua New Guinea',
            'WS' => 'Samoa',
            'TO' => 'Tonga',
            'VU' => 'Vanuatu',
            'SB' => 'Solomon Islands',
            'KI' => 'Kiribati',
            'TV' => 'Tuvalu',
            'NR' => 'Nauru',
            'PW' => 'Palau',
            'FM' => 'Micronesia',
            'MH' => 'Marshall Islands',
            'GU' => 'Guam',
            'NC' => 'New Caledonia',
            'PF' => 'French Polynesia',
            'AS' => 'American Samoa',
            'CK' => 'Cook Islands',
            'NU' => 'Niue',
            'TK' => 'Tokelau',
            'WF' => 'Wallis and Futuna',
            'MP' => 'Northern Mariana Islands',
            'PN' => 'Pitcairn Islands',
            'NF' => 'Norfolk Island',
        ],
        'middle-east' => [
            'IR' => 'Iran',
            'IQ' => 'Iraq',
            'YE' => 'Yemen',
            'SY' => 'Syria',
            'PS' => 'Palestine',
            'AE' => 'United Arab Emirates',
            'SA' => 'Saudi Arabia',
            'QA' => 'Qatar',
            'KW' => 'Kuwait',
            'OM' => 'Oman',
            'BH' => 'Bahrain',
            'JO' => 'Jordan',
            'LB' => 'Lebanon',
            'IL' => 'Israel',
            'TR' => 'Turkey',
            'CY' => 'Cyprus',
            'AF' => 'Afghanistan',
            'AM' => 'Armenia',
            'AZ' => 'Azerbaijan',
            'GE' => 'Georgia',
            'EG' => 'Egypt',
            'LY' => 'Libya',
            'DZ' => 'Algeria',
            'TN' => 'Tunisia',
            'MA' => 'Morocco',
            'SD' => 'Sudan',
        ],
    ];

    $countries = [];

    foreach ($regions as $region => $list) {
        foreach ($list as $code => $name) {
            $countries[] = [
                'code' => $code,
                'name' => $name,
                'region' => $region,
            ];
        }
    }

    return $countries;
}

function getCountriesByRegion() {
    $grouped = [];
    foreach (getCountriesData() as $country) {
        $grouped[$country['region']][] = $country;
    }

    foreach ($grouped as $region => $list) {
        usort($grouped[$region], function ($a, $b) {
            return strcasecmp($a['name'], $b['name']);
        });
    }

    ksort($grouped);
    return $grouped;
}

function getAllCountriesFlat() {
    $countries = getCountriesData();
    usort($countries, function ($a, $b) {
        return strcasecmp($a['name'], $b['name']);
    });
    return $countries;
}

function getCountryByCode($code) {
    $code = strtoupper(trim((string)$code));
    if ($code === '') {
        return null;
    }

    foreach (getCountriesData() as $country) {
        if ($country['code'] === $code) {
            return $country;
        }
    }

    return null;
}

function getCountryByName($name) {
    $name = normalizeCountryLookupName($name);
    if ($name === '') {
        return null;
    }

    foreach (getCountriesData() as $country) {
        if (normalizeCountryLookupName($country['name']) === $name) {
            return $country;
        }
    }

    $aliases = [
        'IVORY COAST' => 'CI',
        'COTE D IVOIRE' => 'CI',
        'CÔTE D IVOIRE' => 'CI',
        'BURMA' => 'MM',
        'MYANMAR' => 'MM',
        'USA' => 'US',
        'UNITED STATES OF AMERICA' => 'US',
        'U.S.A' => 'US',
        'UK' => 'GB',
        'GREAT BRITAIN' => 'GB',
        'BRITAIN' => 'GB',
        'UAE' => 'AE',
        'CONGO' => 'CG',
        'DRC' => 'CD',
        'DEMOCRATIC REPUBLIC OF CONGO' => 'CD',
        'REPUBLIC OF THE CONGO' => 'CG',
        'SWAZILAND' => 'SZ',
        'CZECH REPUBLIC' => 'CZ',
        'MACEDONIA' => 'MK',
        'BOSNIA' => 'BA',
        'HONG KONG SAR' => 'HK',
    ];

    if (isset($aliases[$name])) {
        return getCountryByCode($aliases[$name]);
    }

    return null;
}

function normalizeCountryLookupName($name) {
    $name = trim((string)$name);
    if ($name === '') {
        return '';
    }
    $name = preg_replace('/\s+/', ' ', $name);
    $name = str_replace(['’', '`', '´'], "'", $name);
    $name = preg_replace("/\s*\([^)]*\)\s*/", ' ', $name);
    return strtoupper(trim($name));
}

/**
 * Backward-compatible flat list of country display names (sorted).
 */
function getAllCountryNames() {
    return array_column(getAllCountriesFlat(), 'name');
}
