<?php
/** Personal and business names for transfer counterparty simulation */
return [
    'personal' => [
        'James Wilson', 'Maria Garcia', 'Michael Chen', 'Sarah Johnson', 'David Okonkwo',
        'Emma Thompson', 'Ahmed Al-Rashid', 'Sophie Martin', 'Daniel Brown', 'Fatima Hassan',
        'Robert Taylor', 'Lisa Anderson', 'Thomas Mueller', 'Priya Sharma', 'Carlos Rivera',
        'Jennifer Lee', 'Peter Schmidt', 'Amina Bello', 'William Davis', 'Olivia White',
        'John Smith', 'Paul Hartman', 'Matts Anderson', 'Michael Rodriguez', 'James Thornton',
        'Cobus Van Der West', 'Wright Caleb', 'Pascal Paul', 'Anna Becker', 'Lucas Ferreira',
        'Yuki Tanaka', 'Elena Popov', 'Marc Dubois', 'Hannah Klein', 'Omar Khalil',
        'Grace Okafor', 'Henry Clark', 'Isabella Rossi', 'Noah Williams', 'Chloe Nguyen',
        'Ethan Murphy', 'Mia Johansson', 'Alexander Petrov', 'Zara Malik', 'Benjamin Scott',
        'Amelia Wright', 'Lucas Silva', 'Charlotte Evans', 'Mason Brooks', 'Harper Reed',
    ],
    'business' => [
        'Acme Supplies Ltd', 'Global Tech Solutions', 'Summit Payroll Services', 'Metro Logistics GmbH',
        'Bright Future Consulting', 'Pacific Trade Co', 'Northern Energy LLC', 'Skyline Properties',
        'Vertex Software Inc', 'Harbor Freight Partners', 'Alpine Manufacturing', 'Crown Holdings AG',
        'Silverline Imports', 'Oakwood Services', 'Pinnacle Ventures', 'Blue Horizon Media',
        'Sterling Tax Advisors', 'Prime Distribution FZ', 'Unity Healthcare Group', 'Atlas Engineering',
        'Muster GmbH Payroll', 'Titan Core Assets Group LLC', 'Telekom Deutschland GmbH', 'Amazon Web Services',
        'Shopify Payments', 'Nike E-Commerce', 'Wilma wunder Einzelhandel', 'DAK-Gesundheit',
        'BKK Gesund', 'Muster GmbH HR', 'Leave@academi', 'Academi@Admin', 'Academi@Clinic',
    ],
];

function pickGeneratorName(string $type, callable $rng): string
{
    static $pools = null;
    if ($pools === null) {
        $loaded = require __DIR__ . '/personal-names.php';
        $pools = is_array($loaded) ? $loaded : ['personal' => [], 'business' => []];
    }
    $pool = $pools[$type === 'business' ? 'business' : 'personal'] ?? $pools['personal'];
    return $pool[(int) floor($rng() * count($pool))];
}

function generateDomesticAccountNumber(string $countryIso, callable $rng): string
{
    if (!function_exists('getDomesticAccountNumberRules')) {
        require_once dirname(__DIR__) . '/transfer-rails.php';
    }
    $rules = getDomesticAccountNumberRules($countryIso);
    $min = (int)($rules['min'] ?? 8);
    $max = (int)($rules['max'] ?? 12);
    $len = $min + (int) floor($rng() * max(1, $max - $min + 1));
    $num = '';
    for ($i = 0; $i < $len; $i++) {
        $num .= (string) random_int(0, 9);
    }
    return $num;
}
