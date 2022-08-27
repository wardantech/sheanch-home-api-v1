<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],


    'google' => [
        'client_id' => '522908860794-f3s95fbk115ceqgbtoiin8panpi76jnr.apps.googleusercontent.com',
        'client_secret' => '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',
        'redirect' => 'http://127.0.0.1:8000/auth/google/callback',
    ],

//    $google = \App\Models\Settings\FrontendSetting::first() ,
//
//    'google' => [
//        'client_id' => $google->google_client_id ?? '',
//        'client_secret' => $google->google_client_secret ?? '',
//        'redirect' => $google->google_call_back_url ?? '',
//    ],

];
