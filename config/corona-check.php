<?php

declare(strict_types=1);

return [

    /**
     * Configuration for the value sets
     */
    'value_sets' => [
        /**
         * The URL to the value sets endpoint.
         */
        'url' => env('CORONA_CHECK_VALUE_SETS_URL', 'https://verifier-api.coronacheck.nl/v8/dcbs/value_sets'),

        /**
         * Cache TTL in seconds to cache the value sets.
         */
        'cache_ttl' => intval(env('CORONA_CHECK_VALUE_SETS_CACHE_TTL', 900)),

        /**
         * A comma seperated string of paths to the certificate files, so we can check it with the signature.
         */
        'certificate_file_paths' => env('CORONA_CHECK_VALUE_SETS_CERTIFICATE_FILE_PATHS', ''),
    ],

    /**
     * General proxy settings for corona check endpoints.
     */
    'proxy' => env('CORONA_CHECK_PROXY'),
];
