<?php

// phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

use App\Services\CoronaCheck\ValueSetsInterface;
use App\Services\CoronaCheck\ValueSetsServiceMock;
use Illuminate\Support\Facades\Config;
use MinVWS\Crypto\Laravel\Factory;
use MinVWS\Crypto\Laravel\SignatureCryptoInterface;

use function Pest\Faker\faker;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(Tests\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/


function getLeadTimeData(?string $providerName = null): array
{
    $valueSetsService = new ValueSetsServiceMock();
    $faker = faker();

    return [
        'Aanbieder' => $providerName ?? "aanbieder-123",
        'TeststraatID' => $faker->randomElement(['AABBBCCCDDD']),
        'Datum' => $faker->date(),
        'Testtype' => $faker->randomElement($valueSetsService->getCovid19LabTestManufacturerAndNameValues()),
        'GemTijdIdentificatieUitslag' => $faker->numberBetween(0, 10000),
        'GemTijdIdentificatieEmail' => $faker->numberBetween(0, 10000),
    ];
}

function getTestResultsData(?string $providerName = null): array
{
    $valueSetsService = new ValueSetsServiceMock();
    $faker = faker();

    return [
        'Aanbieder' => $providerName ?? "aanbieder-123",
        'Datum' => $faker->date(),
        'Testtype' => $faker->randomElement($valueSetsService->getCovid19LabTestManufacturerAndNameValues()),
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

function getTestRealisationData(?string $providerName = null): array
{
    $valueSetsService = new ValueSetsServiceMock();
    $faker = faker();

    return [
        'Aanbieder' => $providerName ?? "aanbieder-123",
        'TeststraatID' => $faker->randomElement(['AABBBCCCDDD']),
        'Datum' => $faker->date(),
        'Uur' => $faker->time('H:i'),
        'Testtype' => $faker->randomElement($valueSetsService->getCovid19LabTestManufacturerAndNameValues()),
        'TestenGeboekt' => $faker->numberBetween(0, 1000000),
        'TestenAfgenomen' => $faker->numberBetween(0, 1000000),
        'TestenMetResultaat' => $faker->numberBetween(0, 1000000),
        'TestenMetResultaatAfsprakenportaal' => $faker->numberBetween(0, 1000000),
        'TestenMetResultaatAdhoc' => $faker->numberBetween(0, 1000000),
        'Hertesten' => $faker->numberBetween(0, 1000000),
        'TestenNoShows' => $faker->numberBetween(0, 1000000),
        'TestenAfwachtingResultaat' => $faker->numberBetween(0, 1000000),
        'TestenAfwachtingValidatie' => $faker->numberBetween(0, 1000000),
        'TestenZonderUitslag' => $faker->numberBetween(0, 1000000),
    ];
}

function setupMockedValueSetsService(): void
{
    \Illuminate\Support\Facades\App::bind(ValueSetsInterface::class, ValueSetsServiceMock::class);
}

function setupResultProvidersConfig(): void
{
    Config::set(
        'result-providers.config_file_path',
        base_path('tests/fixtures/result-providers/result-providers.json')
    );

    Config::set(
        'result-providers.storage_path',
        sys_get_temp_dir()
    );
}

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
