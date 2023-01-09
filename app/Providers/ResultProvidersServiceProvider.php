<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\BaseResultProvidersService;
use App\Services\ResultProvidersFileService;
use App\Services\ResultProvidersInterface;
use App\Services\ResultProvidersService;
use Exception;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class ResultProvidersServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ResultProvidersFileService::class, function (Application $app) {
            $fileStoragePath = $app->make(ConfigRepository::class)->get('result-providers.storage_path');
            if (!is_string($fileStoragePath) || empty($fileStoragePath)) {
                throw new RuntimeException('Storage path in result providers config is not set');
            }

            return new ResultProvidersFileService($fileStoragePath);
        });

        $this->app->singleton(ResultProvidersInterface::class, function (Application $app) {
            try {
                $configFilePath = $app->make(ConfigRepository::class)->get('result-providers.config_file_path');
                if (!is_string($configFilePath) || empty($configFilePath)) {
                    throw new RuntimeException('Result providers config file path is not set');
                }

                return new ResultProvidersService($configFilePath);
            } catch (Exception) {
            }

            return new BaseResultProvidersService();
        });
    }
}
