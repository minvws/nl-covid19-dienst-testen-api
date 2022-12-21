<?php

declare(strict_types=1);

use App\Services\CoronaCheck\Config\Config;
use App\Services\CoronaCheck\Config\EndpointConfig;

it('can create a corona check config object', function () {
    $config = new Config(
        valueSets: new EndpointConfig(
            url: 'https://example.com',
            cacheTtl: 60,
            certificateFilePaths: base_path('tests/fixtures/certificates/app/app.pem'),
        ),
        proxy: null
    );
    expect($config)
        ->toBeInstanceOf(Config::class)
        ->getValueSetsConfig()
            ->toBeInstanceOf(EndpointConfig::class)
            ->getValueSetsConfig()->getUrl()->toBe('https://example.com')
            ->getValueSetsConfig()->getCacheTtl()->toBe(60)
            ->getValueSetsConfig()->getCertificateFilePaths()
                    ->toBeArray()
                    ->toHaveCount(1)
            ->getValueSetsConfig()->getCertificates()
                    ->toBeArray()
                    ->toHaveCount(1)
                    ->toContain(file_get_contents(base_path('tests/fixtures/certificates/app/app.pem')))
            ->getProxy()->toBeNull();
});

it('can have a proxy value', function () {
    $config = new Config(
        valueSets: new EndpointConfig(
            url: 'https://example.com',
            cacheTtl: 60,
            certificateFilePaths: "",
        ),
        proxy: "http://127.0.0.1:8080"
    );
    expect($config)
        ->toBeInstanceOf(Config::class)
        ->getProxy()->toBe("http://127.0.0.1:8080");
});
