<?php
return [
    ['name' => 'Shoprite', 'category' => 'food', 'country' => 'NG', 'amount_min' => 2500, 'amount_max' => 35000, 'recurring' => false, 'channel' => 'pos', 'tags' => ['groceries']],
    ['name' => 'GTBank POS', 'category' => 'withdrawal', 'country' => 'NG', 'amount_min' => 5000, 'amount_max' => 100000, 'recurring' => false, 'channel' => 'pos', 'tags' => ['atm', 'cash']],
    ['name' => 'MTN Nigeria', 'category' => 'bills', 'country' => 'NG', 'amount_min' => 1000, 'amount_max' => 15000, 'recurring' => true, 'recurrence' => 'monthly', 'channel' => 'online', 'tags' => ['utilities']],
    ['name' => 'Airtel Nigeria', 'category' => 'bills', 'country' => 'NG', 'amount_min' => 1000, 'amount_max' => 12000, 'recurring' => true, 'recurrence' => 'monthly', 'channel' => 'online', 'tags' => ['utilities']],
    ['name' => 'IKEDC', 'category' => 'utility', 'country' => 'NG', 'amount_min' => 3000, 'amount_max' => 45000, 'recurring' => true, 'recurrence' => 'monthly', 'channel' => 'online', 'tags' => ['utilities']],
    ['name' => 'Uber Lagos', 'category' => 'transport', 'country' => 'NG', 'amount_min' => 800, 'amount_max' => 8000, 'recurring' => false, 'channel' => 'online', 'tags' => ['transit']],
    ['name' => 'University of Lagos', 'category' => 'education', 'country' => 'NG', 'amount_min' => 50000, 'amount_max' => 250000, 'recurring' => false, 'channel' => 'online', 'tags' => ['tuition', 'education']],
    ['name' => 'Jumia Nigeria', 'category' => 'shopping', 'country' => 'NG', 'amount_min' => 2000, 'amount_max' => 80000, 'recurring' => false, 'channel' => 'online', 'tags' => ['retail', 'amazon']],
    ['name' => 'Total Energies NG', 'category' => 'transport', 'country' => 'NG', 'amount_min' => 5000, 'amount_max' => 25000, 'recurring' => false, 'channel' => 'pos', 'tags' => ['fuel']],
];
