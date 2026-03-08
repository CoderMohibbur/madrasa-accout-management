<?php

return [
    'primary_provider' => env('PAYMENT_PRIMARY_PROVIDER', 'shurjopay'),
    'offline_fallback_provider' => env('PAYMENT_OFFLINE_FALLBACK_PROVIDER', 'manual_bank'),
    'provider_mode' => env('PAYMENT_PROVIDER_MODE', 'sandbox'),
    'default_currency' => env('PAYMENT_DEFAULT_CURRENCY', 'BDT'),

    'customer_defaults' => [
        'city' => env('PAYMENT_DEFAULT_CUSTOMER_CITY', 'Dhaka'),
        'post_code' => env('PAYMENT_DEFAULT_CUSTOMER_POST_CODE', '1207'),
        'country' => env('PAYMENT_DEFAULT_CUSTOMER_COUNTRY', 'Bangladesh'),
        'state' => env('PAYMENT_DEFAULT_CUSTOMER_STATE', 'Dhaka'),
        'address' => env('PAYMENT_DEFAULT_CUSTOMER_ADDRESS', 'Bangladesh'),
    ],

    'receipt' => [
        'sandbox_prefix' => env('PAYMENT_SANDBOX_RECEIPT_PREFIX', 'RCT-SBX'),
        'live_prefix' => env('PAYMENT_LIVE_RECEIPT_PREFIX', 'RCT-LIVE'),
    ],

    'posting' => [
        'student_fee' => [
            'enabled' => env('PAYMENT_STUDENT_FEE_POSTING_ENABLED', false),
            'transaction_type_key' => env('PAYMENT_STUDENT_FEE_TRANSACTION_TYPE_KEY', 'student_fee'),
            'account_id' => env('PAYMENT_STUDENT_FEE_ACCOUNT_ID'),
        ],
    ],

    'shurjopay' => [
        'order_prefix' => env('SHURJOPAY_ORDER_PREFIX', 'HFS'),
        'success_url' => env('SHURJOPAY_SUCCESS_URL'),
        'fail_url' => env('SHURJOPAY_FAIL_URL'),
        'cancel_url' => env('SHURJOPAY_CANCEL_URL'),
        'callback_url' => env('SHURJOPAY_CALLBACK_URL'),
        'signature_secret' => env('SHURJOPAY_SIGNATURE_SECRET'),
        'sandbox' => [
            'base_url' => env('SHURJOPAY_SANDBOX_BASE_URL', 'https://sandbox.shurjopayment.com'),
            'username' => env('SHURJOPAY_SANDBOX_API_USERNAME'),
            'password' => env('SHURJOPAY_SANDBOX_API_PASSWORD'),
        ],
        'live' => [
            'base_url' => env('SHURJOPAY_LIVE_BASE_URL', 'https://engine.shurjopayment.com'),
            'username' => env('SHURJOPAY_LIVE_API_USERNAME'),
            'password' => env('SHURJOPAY_LIVE_API_PASSWORD'),
        ],
        'endpoints' => [
            'token' => '/api/get_token',
            'payment' => '/api/secret-pay',
            'verify' => '/api/verification',
        ],
    ],

    'manual_bank' => [
        'enabled' => env('MANUAL_BANK_ENABLED', true),
        'display_name' => env('MANUAL_BANK_DISPLAY_NAME', 'Bank Transfer'),
        'account_name' => env('MANUAL_BANK_ACCOUNT_NAME'),
        'account_number' => env('MANUAL_BANK_ACCOUNT_NUMBER'),
        'bank_name' => env('MANUAL_BANK_BANK_NAME'),
        'branch_name' => env('MANUAL_BANK_BRANCH_NAME'),
        'routing_number' => env('MANUAL_BANK_ROUTING_NUMBER'),
        'instructions' => env(
            'MANUAL_BANK_INSTRUCTIONS',
            'Submit the bank transfer reference after sending the full invoice balance. Management review is required before the payment is treated as paid.'
        ),
    ],
];
