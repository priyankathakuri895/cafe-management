<?php

return [
    // Displayed in the sidebar, page title and on printed bills.
    'name' => env('CAFE_NAME', 'My Cafe'),
    'address' => env('CAFE_ADDRESS', 'Kathmandu, Nepal'),
    'phone' => env('CAFE_PHONE', ''),

    // Currency symbol used everywhere in the UI.
    'currency' => env('CAFE_CURRENCY', 'Rs.'),

    // Percentages applied when a bill is calculated. Set to 0 to disable.
    'service_charge_rate' => (float) env('CAFE_SERVICE_CHARGE', 10),
    'tax_rate' => (float) env('CAFE_TAX_RATE', 13),

    // Shown as a scannable QR code when a bill is settled as "Online".
    // Leave blank to hide the QR code until these are filled in.
    'bank' => [
        'name' => env('CAFE_BANK_NAME', ''),
        'account_name' => env('CAFE_BANK_ACCOUNT_NAME', ''),
        'account_number' => env('CAFE_BANK_ACCOUNT_NUMBER', ''),
    ],
];
