<?php

// phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

use App\Services\CoronaCheck\ValueSetsInterface;
use App\Services\ResultProvidersFileService;
use App\Services\ResultProvidersInterface;
use Illuminate\Support\Facades\Config;

use function Pest\Laravel\getJson;

beforeEach(function () {
    // Setup providers config
    setupResultProvidersConfig();

    // Setup mocked value sets
    setupMockedValueSetsService();
});

it('responds with success', function () {
    // Send request
    getJson(route('health'))
        ->assertOk()
        ->assertJson([
            'healthy' => true,
            'externals' => [
                'value_sets' => true
            ]
        ]);
});


it('responds with 500 error when a dependency is not bound', function ($dependency) {
    \Illuminate\Support\Facades\App::offsetUnset($dependency);

    // Send request
    getJson(route('health'))
        ->assertServerError();
})->with([
    ResultProvidersInterface::class,
    ResultProvidersFileService::class,
    ValueSetsInterface::class,
]);

it('responds with status 500 when we have an empty storage path', function () {
    Config::set('result-providers.storage_path', '');

    // Send request
    getJson(route('health'))
        ->assertServerError();
});

it('responds with status 503 when the value sets service is not healthy', function () {
    $valueSets = mock(ValueSetsInterface::class)->expect();
    $valueSets->expects('isHealthy')
        ->andReturn(false);

    \Illuminate\Support\Facades\App::instance(ValueSetsInterface::class, $valueSets);

    // Send request
    getJson(route('health'))
        ->assertStatus(503)
        ->assertJson([
            'healthy' => false,
            'externals' => [
                'value_sets' => false
            ]
        ]);
});
