<?php

declare(strict_types=1);

namespace App\Providers;

use App\Exceptions\CoronaCheckServiceException;
use App\Services\CoronaCheck\Config\Config;
use App\Services\CoronaCheck\ValueSetsInterface;
use App\Services\CoronaCheck\ValueSetsService;
use CuyZ\Valinor\Mapper\MappingError;
use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\ServiceProvider;
use MinVWS\Crypto\Laravel\Factory;
use Illuminate\Contracts\Cache\Repository;

class CoronaCheckServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->app->bind(ValueSetsInterface::class, ValueSetsService::class);
        $this->app->singleton(ValueSetsService::class, function ($app) {
            $config = $this->getConfig();
            $valueSetsConfig = $config->getValueSetsConfig();

            $client = $this->createGuzzleClient($valueSetsConfig->getUrl(), $config->getProxy());
            $service = Factory::createSignatureCryptoService();

            return new ValueSetsService(
                client: $client,
                cacheRepository: $app->make(Repository::class),
                signatureService: $service,
                certificates: $valueSetsConfig->getCertificates(),
                cacheTtl: $valueSetsConfig->getCacheTtl(),
            );
        });
    }

    protected function createGuzzleClient(string $baseUri, ?string $proxy = null): Client
    {
        $options = [
            'base_uri' => $baseUri,
        ];

        if (!empty($proxy)) {
            $options[RequestOptions::PROXY] = $proxy;
        }

        return new Client($options);
    }

    /**
     * @throws CoronaCheckServiceException
     */
    protected function getConfig(): Config
    {
        $coronaCheckConfig = config('corona-check');
        if (!is_array($coronaCheckConfig)) {
            throw new CoronaCheckServiceException('Corona check config is not an array');
        }

        try {
            return (new MapperBuilder())
                ->mapper()
                ->map(
                    Config::class,
                    (Source::array($coronaCheckConfig))
                        ->camelCaseKeys()
                );
        } catch (MappingError $error) {
            throw new CoronaCheckServiceException("Could not initialize CoronaCheck service config", 0, $error);
        }
    }
}
