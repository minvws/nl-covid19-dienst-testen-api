<?php

// phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use MinVWS\Crypto\Laravel\Factory;
use MinVWS\Crypto\Laravel\SignatureCryptoInterface;

use function Pest\Laravel\postJson;

beforeEach(function () {
    // Setup providers config
    setupResultProvidersConfig();

    // Setup mocked value sets
    setupMockedValueSetsService();

    // Setup certificates for signing
    setupAppCertificationForSigning();
});

it('responds with success', function () {
    // Create crypto service with test provider certificates
    $cryptoService = getSignatureCryptoServiceOfFakeProvider();

    // Create payload
    $data = getLeadTimeData(
        providerName: "aanbieder-123"
    );
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
        ])
        ->assertPayloadPath([
            'success' => true,
        ]);
});

it('responds with a validation exception when a field is missing', function () {
    // Set signature certificate
    $cryptoService = getSignatureCryptoServiceOfFakeProvider();

    // Create payload and unset Datum field
    $data = getLeadTimeData(
        providerName: "aanbieder-123"
    );
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
            'message' => 'The datum field is required.',
            'errors' => [
                'Datum' => [
                    'The datum field is required.',
                ],
            ],
        ]);
});

it('responds with an 400 error when test provider is unknown', function () {
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


it('responds with an 400 error when test provider is correct but sends a non json payload', function () {
    // Create crypto service with test provider certificates
    $cryptoService = getSignatureCryptoServiceOfFakeProvider();

    // Create payload
    $payload = "some-non-json-payload";

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
        ]);
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

function setupAppCertificationForSigning(): void
{
    Config::set('crypto.signature.x509_cert', base_path('tests/fixtures/certificates/app/app.pem'));
    Config::set('crypto.signature.x509_key', base_path('tests/fixtures/certificates/app/app.key'));
    Config::set('crypto.signature.x509_chain', base_path('tests/fixtures/certificates/app/ca.pem'));
}
