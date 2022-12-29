<?php

return [
    'client_id' => env('PAYPAL_SANDBOX_CLIENT_ID', null),
    'client_secret' => env('PAYPAL_SANDBOX_CLIENT_SECRET', null),
    
    'currency' => [
        'USD',
        'AUD',
        'CAD',
        'SGD'
    ]
];