<?php
/**
 * Month-based merchant tag boosts for transaction simulation seasonality.
 */
return [
    1 => ['boost' => ['gym', 'subscription', 'streaming'], 'reduce' => ['luxury', 'travel']],
    2 => ['boost' => ['subscription', 'utilities'], 'reduce' => []],
    3 => ['boost' => ['retail', 'groceries'], 'reduce' => []],
    4 => ['boost' => ['groceries', 'fuel'], 'reduce' => []],
    5 => ['boost' => ['travel', 'restaurants'], 'reduce' => []],
    6 => ['boost' => ['travel', 'hotels', 'airlines', 'fuel'], 'reduce' => []],
    7 => ['boost' => ['travel', 'hotels', 'airlines', 'fuel', 'restaurants'], 'reduce' => []],
    8 => ['boost' => ['travel', 'education', 'tuition', 'retail', 'back_to_school'], 'reduce' => []],
    9 => ['boost' => ['education', 'tuition', 'retail', 'back_to_school'], 'reduce' => []],
    10 => ['boost' => ['groceries', 'fuel'], 'reduce' => []],
    11 => ['boost' => ['gifts', 'amazon', 'retail'], 'reduce' => []],
    12 => ['boost' => ['gifts', 'amazon', 'airlines', 'hotels', 'restaurants', 'retail'], 'reduce' => []],
];
