<?php

declare(strict_types=1);

return [
    /**
     * File path the providers config JSON file.
     */
    'config_file_path' => env('RESULT_PROVIDERS_CONFIG_PATH'),

    /**
     * Number of days that test providers have to send or correct the data.
     * If this is set to 5 days, we will accept the date of today minus 5 days.
     */
    'max_days_accepted' => env('RESULT_PROVIDERS_MAX_DAYS_ACCEPTED', 5),

    /**
     * Path where we will store the provider's data.
     */
    'storage_path' => env('RESULT_PROVIDERS_STORAGE_PATH', storage_path('app/result-providers')),
];
