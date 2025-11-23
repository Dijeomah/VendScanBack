<?php
return [
    'publicKey' => env('FLUTTERWAVE_PUBLIC_KEY'),
    'secretKey' => env('FLUTTERWAVE_SECRET_KEY'),
    'encryptionKey' => env('FLUTTERWAVE_ENCRYPTION_KEY'),
    'env' => env('FLUTTERWAVE_ENV', 'staging'),
];
