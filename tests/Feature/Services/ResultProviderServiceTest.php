<?php

declare(strict_types=1);

use App\Data\ResultProvider;
use App\Services\ResultProvidersService;

it('loads an json configuration file and maps to an array of ResultProvider objects', function () {
    $service = new ResultProvidersService(
        providersConfigPath: base_path('tests/fixtures/result-providers/result-providers.json'),
    );

    $providers = $service->getProviders();

    expect($providers)->toBeArray()
        ->and($providers)->toHaveCount(1)
        ->and($providers)->toHaveKey('aanbieder-123')
        ->and($providers['aanbieder-123'])->toBeInstanceOf(ResultProvider::class);
});

it('throws an exception when it loads an configuration file with an unsupported extension', function () {
    $service = new ResultProvidersService(
        providersConfigPath: base_path('tests/fixtures/result-providers/invalid-file-type-result-providers.txt'),
    );
})->throws(RuntimeException::class, 'Providers config file is not valid');
