<?php

// phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

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
    $data = getTestResultsData(
        providerName: "aanbieder-123"
    );
    $payload = json_encode($data, JSON_THROW_ON_ERROR);

    // Sign payload, base64 encoded signature returned
    $signature = $cryptoService->sign($payload, true);

    postJson(route('api.test-results'), [
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
    $data = getTestResultsData();
    unset($data['Datum']);

    $payload = json_encode($data, JSON_THROW_ON_ERROR);

    // Sign payload, base64 encoded signature returned
    $signature = $cryptoService->sign($payload, true);

    // Send request
    postJson(route('api.test-results'), [
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

it('responds with an 400 error when test provider is unknown', function () {
    // Set signature certificate
    $cryptoService = getSignatureCryptoServiceOfFakeProvider();

    // Create payload and unset Datum field
    $data = getTestResultsData(
        providerName: "unknown-provider"
    );

    $payload = json_encode($data, JSON_THROW_ON_ERROR);

    // Sign payload, base64 encoded signature returned
    $signature = $cryptoService->sign($payload, true);

    // Send request
    postJson(route('api.test-results'), [
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
    postJson(route('api.test-results'), [
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
