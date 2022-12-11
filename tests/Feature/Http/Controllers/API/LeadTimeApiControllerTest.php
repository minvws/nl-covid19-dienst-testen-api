<?php

// phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use MinVWS\Crypto\Laravel\Factory;
use MinVWS\Crypto\Laravel\SignatureCryptoInterface;

use function Pest\Laravel\postJson;

it('responds with success', function () {
    // TODO: Initialise crypto service that will sign the response
    // TODO: Use other certificates inside the test folder
    Config::set('crypto.signature.x509_cert', storage_path('app/testprovider.example/cert.pem'));
    Config::set('crypto.signature.x509_key', storage_path('app/testprovider.example/key.pem'));
    Config::set('crypto.signature.x509_chain', storage_path('app/testprovider.example/ca.pem'));

    // Create crypto service with test provider certificates
    $cryptoService = getSignatureCryptoServiceOfFakeProvider();

    // Create payload
    $data = getLeadTimeData();
    $payload = json_encode($data, JSON_THROW_ON_ERROR);

    // Sign payload
    $signature = $cryptoService->sign($payload, true);

    // Send request
    postJson(route('api.lead-time'), [
        'payload' => base64_encode($payload),
        'signature' => base64_encode($signature),
    ])
        ->assertOk()
        ->assertJsonStructure([
            'payload',
            'signature',
        ]);
});

it('responds with a validation exception when a field is missing', function () {
    // TODO: Initialise crypto service that will sign the response
    Config::set('crypto.signature.x509_cert', storage_path('app/testprovider.example/cert.pem'));
    Config::set('crypto.signature.x509_key', storage_path('app/testprovider.example/key.pem'));
    Config::set('crypto.signature.x509_chain', storage_path('app/testprovider.example/ca.pem'));

    // Set signature certificate
    $cryptoService = getSignatureCryptoServiceOfFakeProvider();

    // Create payload and unset Datum field
    $data = getLeadTimeData();
    unset($data['Datum']);

    $payload = json_encode($data, JSON_THROW_ON_ERROR);

    // Sign payload
    $signature = $cryptoService->sign($payload, true);

    // Send request
    postJson(route('api.lead-time'), [
        'payload' => base64_encode($payload),
        'signature' => base64_encode($signature),
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

function getSignatureCryptoServiceOfFakeProvider(): SignatureCryptoInterface
{
    return Factory::createSignatureCryptoService(
        certificatePath: storage_path('app/testprovider.example/cert.pem'),
        certificateKeyPath: storage_path('app/testprovider.example/key.pem'),
        certificateChain: storage_path('app/testprovider.example/ca.pem'),
        forceProcessSpawn: config('crypto.force_process_spawn'),
    );
}
