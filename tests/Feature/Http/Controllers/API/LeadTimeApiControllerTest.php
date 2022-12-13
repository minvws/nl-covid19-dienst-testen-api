<?php

// phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use MinVWS\Crypto\Laravel\Factory;
use MinVWS\Crypto\Laravel\SignatureCryptoInterface;

use function Pest\Laravel\postJson;

it('responds with success', function () {
    // Setup providers config
    setupResultProvidersConfig();

    // Setup certificates for signing
    setupAppCertificationForSigning();

    // Create crypto service with test provider certificates
    $cryptoService = getSignatureCryptoServiceOfFakeProvider();

    // Create payload
    $data = getLeadTimeData();
    $payload = json_encode($data, JSON_THROW_ON_ERROR);

    // Sign payload, base64 encoded signature returned
    $signature = $cryptoService->sign($payload, true);

    // Send request
    postJson(route('api.lead-time'), [
        'payload' => base64_encode($payload),
        'signature' => $signature,
    ])
        ->assertOk()
        ->assertJsonStructure([
            'payload',
            'signature',
        ]);
});

it('responds with a validation exception when a field is missing', function () {
    // Setup providers config
    setupResultProvidersConfig();

    // Setup certificates for signing
    setupAppCertificationForSigning();

    // Set signature certificate
    $cryptoService = getSignatureCryptoServiceOfFakeProvider();

    // Create payload and unset Datum field
    $data = getLeadTimeData();
    unset($data['Datum']);

    $payload = json_encode($data, JSON_THROW_ON_ERROR);

    // Sign payload, base64 encoded signature returned
    $signature = $cryptoService->sign($payload, true);

    // Send request
    postJson(route('api.lead-time'), [
        'payload' => base64_encode($payload),
        'signature' => $signature,
    ])
        ->assertStatus(422)
        ->assertJsonStructure([
            'payload',
            'signature',
        ])
        ->assertPayloadPath([
            'errors' => [
                'Datum' => [
                    'The datum field is required.',
                ],
            ],
        ]);
});

it('responds with an exception when test provider is unknown', function () {
    // Setup providers config
    setupResultProvidersConfig();

    // Setup certificates for signing
    setupAppCertificationForSigning();

    // Set signature certificate
    $cryptoService = getSignatureCryptoServiceOfFakeProvider();

    // Create payload and unset Datum field
    $data = getLeadTimeData(
        providerName: 'UnknownProvider',
    );

    $payload = json_encode($data, JSON_THROW_ON_ERROR);

    // Sign payload, base64 encoded signature returned
    $signature = $cryptoService->sign($payload, true);

    // Send request
    postJson(route('api.lead-time'), [
        'payload' => base64_encode($payload),
        'signature' => $signature,
    ])
        ->assertStatus(400)
        ->assertJsonStructure([
            'payload',
            'signature',
        ])
        ->assertPayloadPath([
            'success' => false,
        ])
        ->assertSignedWith(
            config('crypto.signature.x509_cert'),
            config('crypto.signature.x509_chain'),
        );
});

function getSignatureCryptoServiceOfFakeProvider(): SignatureCryptoInterface
{
    return Factory::createSignatureCryptoService(
        certificatePath: base_path('tests/fixtures/certificates/aanbieder-123/cert.pem'),
        certificateKeyPath: base_path('tests/fixtures/certificates/aanbieder-123/key.pem'),
        certificateChain: base_path('tests/fixtures/certificates/aanbieder-123/chain.pem'),
        forceProcessSpawn: config('crypto.force_process_spawn'),
    );
}

function setupResultProvidersConfig(): void
{
    Config::set('result-providers.config_file_path', base_path('tests/fixtures/result-providers.json'));
}

function setupAppCertificationForSigning(): void
{
    Config::set('crypto.signature.x509_cert', base_path('tests/fixtures/certificates/app/app.pem'));
    Config::set('crypto.signature.x509_key', base_path('tests/fixtures/certificates/app/app.key'));
    Config::set('crypto.signature.x509_chain', base_path('tests/fixtures/certificates/app/ca.pem'));
}
