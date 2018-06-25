<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => 'slack2gmail.io',
        'secret' => getenv('MAILGUN_SECRET'),
    ],


    // 'ses' => [
    //     'key' => env('SES_KEY'),
    //     'secret' => env('SES_SECRET'),
    //     'region' => env('SES_REGION', 'us-east-1'),
    // ],

    // 'sparkpost' => [
    //     'secret' => env('SPARKPOST_SECRET'),
    // ],

    // 'stripe' => [
    //     'model' => App\User::class,
    //     'key' => env('STRIPE_KEY'),
    //     'secret' => env('STRIPE_SECRET'),
    // ],

    'google' => [
        'client_id' => env('GOOGLE_OAUTH2_CLIENT_ID'),
        'client_secret' => env('GOOGLE_OAUTH2_CLIENT_SECRET'),
        'redirect' => getenv('APP_URL') . '/oauth2/gmail/validate',
        'scopes' => [
            'https://www.googleapis.com/auth/userinfo.profile',
            'https://www.googleapis.com/auth/gmail.labels',
            'https://www.googleapis.com/auth/gmail.insert',
            // 'https://www.googleapis.com/auth/gmail.readonly',
        ],
    ],

    'slack' => [
        // token is set before each request with the right organisation token
        'token' => NULL,
    ],

    'slack-notifications' => [
        'enabled' => strtolower(getenv('SLACK_ENABLED')) === "true",
        'webhook' => getenv('SLACK_WEBHOOK'),
        'username' => 'App',
        'icon' => ':robot_face:',
    ],

    'gmail' => [
        'address_suffix' => getenv('SYNC_GMAIL_ADDRESS_SUFFIX'),
        'label' => getenv('SYNC_GMAIL_LABEL'),
    ],

];
