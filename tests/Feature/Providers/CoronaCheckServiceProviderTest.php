<?php

declare(strict_types=1);

use App\Exceptions\CoronaCheckServiceException;
use App\Services\CoronaCheck\ValueSetsInterface;
use App\Services\CoronaCheck\ValueSetsService;
use App\Services\CoronaCheck\ValueSetsServiceMock;

it('loads the value sets service mock when the value sets url is empty', function () {
    Config::set('corona-check.value_sets.url', '');

    $service = app(ValueSetsInterface::class);
    expect($service)
        ->toBeInstanceOf(ValueSetsServiceMock::class)
        ->not()->toBeInstanceOf(ValueSetsService::class);
});

it('loads the value sets service', function () {
    Config::set('corona-check.value_sets.url', 'https://verifier-api.coronacheck.nl/v8/dcbs/value_sets');

    $service = app(ValueSetsInterface::class);
    expect($service)
        ->toBeInstanceOf(ValueSetsService::class);
});

it('loads the value sets service with proxy', function () {
    Config::set('corona-check.value_sets.url', 'https://verifier-api.coronacheck.nl/v8/dcbs/value_sets');
    Config::set('corona-check.proxy', 'https://rdobeheer.nl');

    $service = app(ValueSetsInterface::class);
    expect($service)
        ->toBeInstanceOf(ValueSetsService::class);
});

it('throws an exception when corona check config is not an array', function ($config) {
    Config::set('corona-check', $config);

    expect(static function () {
        $service = app(ValueSetsInterface::class);
    })->toThrow(CoronaCheckServiceException::class, 'Corona check config is not an array');
})->with([
    null,
    '',
]);

it('throws an exception when corona check config does not have the correct typings', function ($config) {
    Config::set('corona-check', $config);

    expect(static function () {
        $service = app(ValueSetsInterface::class);
    })->toThrow(CoronaCheckServiceException::class, 'Could not initialize CoronaCheck service config');
})->with([
    fn() => [
        'value_sets' => [
            'url' => null,
        ],
    ],
    fn() => [
        'value_sets' => [
            'url' => 'https://verifier-api.coronacheck.nl/v8/dcbs/value_sets',
            'cache_ttl' => '',
        ],
    ],
    fn() => [
        'value_sets' => [
            'url' => 'https://verifier-api.coronacheck.nl/v8/dcbs/value_sets',
            'certificate_file_paths' => null,
        ],
    ],
])->group('corona-check');
