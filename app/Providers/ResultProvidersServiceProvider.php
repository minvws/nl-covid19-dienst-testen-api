<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\ResultProvidersService;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class ResultProvidersServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->app->singleton(ResultProvidersService::class, function () {
            $configFilePath = config('result-providers.config_file_path');
            if (!is_string($configFilePath)) {
                throw new RuntimeException('Result providers config file path is not set');
            }

            return new ResultProvidersService($configFilePath);
        });
    }
}
