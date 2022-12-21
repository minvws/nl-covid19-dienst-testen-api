<?php

declare(strict_types=1);

use App\Exceptions\CoronaCheckServiceException;
use App\Services\CoronaCheck\Config\EndpointConfig;

it('can create a corona check endpoint config object', function () {
    $config = new EndpointConfig(
        url: 'https://example.com',
        cacheTtl: 60,
        certificateFilePaths: "",
    );

    expect($config)
        ->toBeInstanceOf(EndpointConfig::class)
        ->getUrl()->toBe('https://example.com')
        ->getCacheTtl()->toBe(60)
        ->getCertificateFilePaths()->toBeArray()->toBeEmpty()
        ->getCertificates()->toBeArray()->toBeEmpty();
});

it('will return certificates when you provide them', function () {
    $config = new EndpointConfig(
        url: 'https://example.com',
        cacheTtl: 60,
        certificateFilePaths: base_path('tests/fixtures/certificates/app/app.pem'),
    );

    expect($config)
        ->toBeInstanceOf(EndpointConfig::class)
        ->getUrl()->toBe('https://example.com')
        ->getCacheTtl()->toBe(60)
        ->getCertificateFilePaths()->toBeArray()->toHaveCount(1)
        ->getCertificates()
            ->toBeArray()
            ->toHaveCount(1)
            ->toContain(file_get_contents(base_path('tests/fixtures/certificates/app/app.pem')));
});

it('will throw an exception when a certificate is not readable', function () {
    $config = new EndpointConfig(
        url: 'https://example.com',
        cacheTtl: 60,
        certificateFilePaths: base_path('tests/fixtures/certificates/app/something-not-existing.pem'),
    );

    $config->getCertificates();
})->throws(CoronaCheckServiceException::class);
