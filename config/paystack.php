<?php

return [

    'env' => env('PAYSTACK_ENV', 'production'),

    'callback_url' => env('PAYSTACK_CALLBACK_URL', 'https://api.paystack.co/transaction/initialize'),

    'initialize_url' => env('PAYSTACK_INITIALIZE_URL', 'https://api.paystack.co/transaction/initialize'),

    'verify_url' => env('PAYSTACK_VERIFY_URL', 'https://api.paystack.co/transaction/verify/'),

    'test' => [
        'secret_key' => env('PAYSTACK_TEST_SECRET_KEY', 'sk_test_c6cec3d09ddfea187b6e57eb6b661d8463670669'),
        'public_key' => env('PAYSTACK_TEST_PUBLIC_KEY', 'pk_test_b86e31a4a6f091738f6549f9f997128bd36a6639'),
    ],

    'live' => [
        'secret_key' => env('PAYSTACK_LIVE_SECRET_KEY', 'sk_test_c6cec3d09ddfea187b6e57eb6b661d8463670669'),
        'public_key' => env('PAYSTACK_LIVE_PUBLIC_KEY', 'pk_test_b86e31a4a6f091738f6549f9f997128bd36a6639'),
    ],

];