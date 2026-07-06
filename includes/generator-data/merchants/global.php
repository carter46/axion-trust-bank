<?php
/** Global online merchants (allowed ~10% for any operating country) */
return [
    ['name' => 'Netflix', 'category' => 'entertainment', 'country' => 'GLOBAL', 'amount_min' => 9, 'amount_max' => 25, 'recurring' => true, 'recurrence' => 'monthly', 'channel' => 'online', 'global' => true, 'tags' => ['streaming', 'subscription']],
    ['name' => 'Spotify', 'category' => 'entertainment', 'country' => 'GLOBAL', 'amount_min' => 5, 'amount_max' => 15, 'recurring' => true, 'recurrence' => 'monthly', 'channel' => 'online', 'global' => true, 'tags' => ['streaming', 'subscription']],
    ['name' => 'Amazon', 'category' => 'shopping', 'country' => 'GLOBAL', 'amount_min' => 12, 'amount_max' => 350, 'recurring' => false, 'channel' => 'online', 'global' => true, 'tags' => ['amazon', 'retail', 'gifts']],
    ['name' => 'Apple Services', 'category' => 'entertainment', 'country' => 'GLOBAL', 'amount_min' => 2, 'amount_max' => 50, 'recurring' => true, 'recurrence' => 'monthly', 'channel' => 'online', 'global' => true, 'tags' => ['subscription', 'streaming']],
    ['name' => 'Google One', 'category' => 'bills', 'country' => 'GLOBAL', 'amount_min' => 2, 'amount_max' => 20, 'recurring' => true, 'recurrence' => 'monthly', 'channel' => 'online', 'global' => true, 'tags' => ['subscription', 'saas']],
    ['name' => 'Microsoft 365', 'category' => 'bills', 'country' => 'GLOBAL', 'amount_min' => 7, 'amount_max' => 15, 'recurring' => true, 'recurrence' => 'monthly', 'channel' => 'online', 'global' => true, 'tags' => ['saas', 'subscription']],
    ['name' => 'Adobe Creative Cloud', 'category' => 'bills', 'country' => 'GLOBAL', 'amount_min' => 25, 'amount_max' => 80, 'recurring' => true, 'recurrence' => 'monthly', 'channel' => 'online', 'global' => true, 'tags' => ['saas', 'subscription']],
];
