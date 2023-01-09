<?php

declare(strict_types=1);

use App\Services\BaseResultProvidersService;
use App\Services\ResultProvidersFileService;
use App\Services\ResultProvidersInterface;
use App\Services\ResultProvidersService;

it('loads the base result provider service on exception when config file path is invalid', function ($path) {
    Config::set('result-providers.config_file_path', $path);

    $service = app(ResultProvidersInterface::class);
    expect($service)
        ->toBeInstanceOf(BaseResultProvidersService::class)
        ->not()->toBeInstanceOf(ResultProvidersService::class);
})->with([
    null,
    '',
]);

it('loads the base result provider service on exception when loading an invalid config file', function () {
    Config::set(
        'result-providers.config_file_path',
        base_path('tests/fixtures/result-providers/invalid-file-type-result-providers.txt')
    );

    $service = app(ResultProvidersInterface::class);
    expect($service)
        ->toBeInstanceOf(BaseResultProvidersService::class)
        ->not()->toBeInstanceOf(ResultProvidersService::class);
});

it('loads the result provider service when loading a correct config file', function () {
    Config::set(
        'result-providers.config_file_path',
        base_path('tests/fixtures/result-providers/result-providers.json')
    );

    $service = app(ResultProvidersInterface::class);
    expect($service)
        ->toBeInstanceOf(ResultProvidersService::class);
});


it('loads the result provider file service when a storage path is provided', function () {
    Config::set(
        'result-providers.config_file_path',
        base_path('tests/fixtures/result-providers/result-providers.json')
    );

    $service = app(ResultProvidersInterface::class);
    expect($service)
        ->toBeInstanceOf(ResultProvidersService::class);
});

it('throws an exception when the storage path for the result provider file service is not a string', function ($path) {
    Config::set('result-providers.storage_path', $path);

    expect(static function () {
        $service = app(ResultProvidersFileService::class);
    })->toThrow(RuntimeException::class, 'Storage path in result providers config is not set');
})->with([
    null,
    '',
]);
