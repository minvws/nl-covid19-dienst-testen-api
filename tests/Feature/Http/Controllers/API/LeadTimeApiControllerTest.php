<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use MinVWS\Crypto\Laravel\SignatureCryptoInterface;

use function Pest\Faker\faker;
use function Pest\Laravel\post;

it('responds with a 200 code', function () {
    // Set signature certificate
    Config::set('crypto.signature.x509_cert', storage_path('app/testprovider.example/cert.pem'));
    Config::set('crypto.signature.x509_key', storage_path('app/testprovider.example/key.pem'));
    Config::set('crypto.signature.x509_chain', storage_path('app/testprovider.example/ca.pem'));

    // Create payload
    $data = getLeadTimeData();
    $payload = json_encode($data, JSON_THROW_ON_ERROR);

    // Sign payload
    $cryptoService = app(SignatureCryptoInterface::class);
    $signature = $cryptoService->sign($payload, true);

    // Send request
    post(route('api.lead-time'), [
        'payload' => base64_encode($payload),
        'signature' => base64_encode($signature),
    ])
        ->assertOk()
        ->assertJsonStructure([
            'payload',
            'signature',
        ]);
});

function getLeadTimeData()
{

    $faker = faker();

    return [
        'Aanbieder' => $faker->company(),
        'Datum' => $faker->date(),
        'Testtype' => $faker->randomElement(['PCR', 'Antigeen', 'Antistoffen']),
        'TestenAfgenomen' => $faker->numberBetween(0, 1000000),
        'TestenMetResultaat' => $faker->numberBetween(0, 1000000),
        'TestenPositief' => $faker->numberBetween(0, 1000000),
        'TestenNegatief' => $faker->numberBetween(0, 1000000),
        'TestenOndefinieerbaar' => $faker->numberBetween(0, 1000000),
        'TestenAfwachtingResultaat' => $faker->numberBetween(0, 1000000),
        'TestenAfwachtingValidatie' => $faker->numberBetween(0, 1000000),
        'TestenZonderUitslag' => $faker->numberBetween(0, 1000000),
    ];
}
