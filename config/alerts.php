<?php
return [
    'admin_key'       => env('ADMIN_KEY'),
    'telegram_token'  => env('TELEGRAM_TOKEN'),
    'telegram_bot'    => env('TELEGRAM_BOT'),
    'telegram_secret' => env('TELEGRAM_SECRET'),
    'sms_url'         => env('SMS_URL'),
    'sms_key'         => env('SMS_KEY'),
    'sms_from'        => env('SMS_FROM', 'Alerte'),
];
