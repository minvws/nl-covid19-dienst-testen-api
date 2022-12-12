<?php

declare(strict_types=1);

return [
    // Allows to use openssl binary instead of the native openssl_* functions
    'force_process_spawn' => true,

    'cms' => [
        // A list of certificates that is used to encrypt data. Can be decrypted by any of the certificates privkeys.
        'encryption_certificate_paths' => explode(',', env('CMS_ENCRYPTION_CERT_PATHS', '')), // @phpstan-ignore-line

        // When decrypting CMS data, this is the cert we will use. There can only be one certificate here.
        'decryption_certificate_path' => env('CMS_DECRYPTION_CERT_PATH'),

        'decryption_certificate_key_path' => env('CMS_DECRYPTION_CERT_KEY_PATH'),
    ],

    'sealbox' => [
        // Our own private key
        'private_key' => env('CMS_SEAL_PRIVKEY'),

        // Public key from the other side
        'recipient_public_key' => env('CMS_SEAL_RECIPIENT_PUBKEY'),
    ],

    'signature' => [
        'x509_cert' => env('CMS_SIGN_X509_CERT'),
        'x509_key' => env('CMS_SIGN_X509_KEY'),
        'x509_pass' => env('CMS_SIGN_X509_PASS'),
        'x509_chain' => env('CMS_SIGN_X509_CHAIN'),
    ],
];
