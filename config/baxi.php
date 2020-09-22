<?php

return [

    'env' => env('BAXI_ENV', 'production'),

    'api_url' => env('BAXI_API_URL', 'https://payments.baxipay.com.ng/api/baxipay'),

    'test' => [
        'username' => env('BAXI_USERNAME', 'baxi_test'),
        'user_secret' => env('BAXI_USER_SECRET', '5xjqQ7MafFJ5XBTN'),
        'api_key' => env('BAXI_API_KEY', '5adea9-044a85-708016-7ae662-646d59'),
    ],

    'live' => [
        'username' => env('BAXI_USERNAME', 'baxi_test'),
        'user_secret' => env('BAXI_USER_SECRET', '5xjqQ7MafFJ5XBTN'),
        'api_key' => env('BAXI_API_KEY', '5adea9-044a85-708016-7ae662-646d59'),
    ],

];