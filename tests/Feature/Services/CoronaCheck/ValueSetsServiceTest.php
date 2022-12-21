<?php

// phpcs:disable PSR1.Files.SideEffects
// phpcs:disable Generic.Files.LineLength


declare(strict_types=1);

use App\Services\CoronaCheck\Service;
use App\Services\CoronaCheck\ValueSetsService;
use GuzzleHttp\ClientInterface;
use Illuminate\Contracts\Cache\Repository;

it('creates a value sets service with only a client', function () {
    $client = mock(ClientInterface::class)->expect();

    $service = new ValueSetsService(
        client: $client,
    );

    expect($service)
        ->toBeInstanceOf(ValueSetsService::class);
});

it('creates a value sets service', function () {
    $client = mock(ClientInterface::class)->expect();
    $cache = mock(Repository::class)->expect();

    $service = new ValueSetsService(
        client: $client,
        cacheRepository: $cache,
        signatureService: null,
        certificates: [],
        cacheTtl: 900,
    );

    expect($service)
        ->toBeInstanceOf(ValueSetsService::class);
});

it('clears the cache', function () {
    $client = mock(ClientInterface::class)->expect();
    $client->shouldNotReceive('request');

    $cache = mock(Repository::class)->expect();
    $cache->shouldReceive('forget')
        ->once()
        ->withArgs(['value_sets_config']);

    $service = new ValueSetsService(
        client: $client,
        cacheRepository: $cache,
        signatureService: null,
        certificates: [],
        cacheTtl: 900,
    );

    expect($service)
        ->toBeInstanceOf(Service::class)
        ->clearCache();
});


it('fetches, puts in cache and returns content', function () {
    $cache = mock(Repository::class)->expect();
    $cache
        ->shouldReceive('get')
            ->once()
            ->withArgs(['value_sets_config'])
            ->andReturn(null)
        ->shouldReceive('put')
            ->once()
            ->withArgs(['value_sets_config', [
                'some' => 'content',
            ], 900]);

    $client = getMockClient(getMockClientMessageWithPayloadAndFakeSignature([
        'some' => 'content',
    ]));

    $service = new ValueSetsService(
        client: $client,
        cacheRepository: $cache,
        signatureService: null,
        certificates: [],
        cacheTtl: 900,
    );

    expect($service)
        ->toBeInstanceOf(Service::class)
        ->fetch()->toBe([
            'some' => 'content',
        ]);
});

it('can not get getCovid19LabTestManufacturerAndName when the key covid-19-lab-test-manufacturer-and-name does not exists', function () {
    $content = [
        'some' => 'content',
    ];

    $client = getMockClient(getMockClientMessageWithPayloadAndFakeSignature($content));

    $service = new ValueSetsService(
        client: $client,
        cacheRepository: null,
        signatureService: null,
        certificates: [],
        cacheTtl: 900,
    );

    expect($service)
        ->toBeInstanceOf(Service::class)
        ->getCovid19LabTestManufacturerAndName();
})->throws(\App\Exceptions\CoronaCheckServiceException::class, 'Invalid data for key covid-19-lab-test-manufacturer-and-name');

it('can not get getCovid19LabTestManufacturerAndName when the value for key covid-19-lab-test-manufacturer-and-name is not an array', function () {
    $content = [
        'covid-19-lab-test-type' => '',
    ];

    $client = getMockClient(getMockClientMessageWithPayloadAndFakeSignature($content));

    $service = new ValueSetsService(
        client: $client,
        cacheRepository: null,
        signatureService: null,
        certificates: [],
        cacheTtl: 900,
    );

    expect($service)
        ->toBeInstanceOf(Service::class)
        ->getCovid19LabTestManufacturerAndName();
})->throws(\App\Exceptions\CoronaCheckServiceException::class, 'Invalid data for key covid-19-lab-test-manufacturer-and-name');


it('can get getCovid19LabTestManufacturerAndName', function () {
    $content = [
        'covid-19-lab-test-manufacturer-and-name' => [
            "1341" => [
                "display" => "SARS-CoV-2 Antigen Rapid Test",
                "lang" => "en",
                "active" => true,
                "system" => "https://covid-19-diagnostics.jrc.ec.europa.eu/devices",
                "version" => "2021-07-07 05:23:59 CEST",
                "validUntil" => null,
            ],
            "1065" => [
                "display" => "System for Rapid Detection of SARS CoV 2",
                "lang" => "en",
                "active" => true,
                "system" => "https://covid-19-diagnostics.jrc.ec.europa.eu/devices",
                "version" => "2021-07-07 05:13:00 CEST",
                "validUntil" => null,
            ],
            "1581" => [
                "display" => "OnSite COVID-19 Ag Rapid Test",
                "lang" => "en",
                "active" => true,
                "system" => "https://covid-19-diagnostics.jrc.ec.europa.eu/devices",
                "version" => "2021-07-07 05:10:05 CEST",
                "validUntil" => null,
            ],
        ],
    ];

    $client = getMockClient(getMockClientMessageWithPayloadAndFakeSignature($content));

    $service = new ValueSetsService(
        client: $client,
        cacheRepository: null,
        signatureService: null,
        certificates: [],
        cacheTtl: 900,
    );

    expect($service)
        ->toBeInstanceOf(Service::class)
        ->getCovid19LabTestManufacturerAndName()->toBe([
            [
                'code' => '1065',
                'name' => 'System for Rapid Detection of SARS CoV 2',
                'active' => true,
            ],
            [
                'code' => '1341',
                'name' => 'SARS-CoV-2 Antigen Rapid Test',
                'active' => true,
            ],
            [
                'code' => '1581',
                'name' => 'OnSite COVID-19 Ag Rapid Test',
                'active' => true,
            ],
        ]);
});
