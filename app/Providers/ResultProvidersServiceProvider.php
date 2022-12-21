<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\ResultProvidersFileService;
use App\Services\ResultProvidersService;
use Illuminate\Contracts\Debug\ExceptionHandler;
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

            return new ResultProvidersService($configFilePath, app(ExceptionHandler::class));
        });

        $this->app->singleton(ResultProvidersFileService::class, function () {
            $fileStoragePath = config('result-providers.storage_path');
            if (!is_string($fileStoragePath)) {
                throw new RuntimeException('Storage path in result providers config is not set');
            }

            return new ResultProvidersFileService($fileStoragePath);
        });
    }
}
