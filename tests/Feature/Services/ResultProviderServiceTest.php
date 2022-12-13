<?php

declare(strict_types=1);

use App\Data\ResultProvider;
use App\Services\ResultProvidersService;
use Illuminate\Contracts\Debug\ExceptionHandler;

it('loads an json configuration file and maps to an array of ResultProvider objects', function () {
    $mock = mock(ExceptionHandler::class)->expect();

    $service = new ResultProvidersService(
        providersConfigPath: base_path('tests/fixtures/result-providers/result-providers.json'),
        exceptionHandler: $mock,
    );

    $providers = $service->getProviders();

    expect($providers)->toBeArray()
        ->and($providers)->toHaveCount(1)
        ->and($providers)->toHaveKey('aanbieder-123')
        ->and($providers['aanbieder-123'])->toBeInstanceOf(ResultProvider::class);
});

it('throws an exception when it loads an configuration file with an unsupported extension', function () {
    $mock = mock(ExceptionHandler::class)->expect();
    $mock->shouldReceive('report')
        ->once();

    $service = new ResultProvidersService(
        providersConfigPath: base_path('tests/fixtures/result-providers/result-providers.json.txt'),
        exceptionHandler: $mock
    );
})->throws(RuntimeException::class, 'Providers config file is not valid');
