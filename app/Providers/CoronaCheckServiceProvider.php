<?php

declare(strict_types=1);

namespace App\Providers;

use App\Exceptions\CoronaCheckServiceException;
use App\Services\CoronaCheck\Config\Config;
use App\Services\CoronaCheck\ValueSetsInterface;
use App\Services\CoronaCheck\ValueSetsService;
use App\Services\CoronaCheck\ValueSetsServiceMock;
use CuyZ\Valinor\Mapper\MappingError;
use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MinVWS\Crypto\Laravel\Factory;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

class CoronaCheckServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(ValueSetsInterface::class, function (Application $app) {
            $config = $this->app->make(ConfigRepository::class);

            $coronaCheckConfig = $this->getCoronaCheckConfig($config->get('corona-check'));
            $valueSetsConfig = $coronaCheckConfig->getValueSetsConfig();

            if (empty($valueSetsConfig->getUrl())) {
                return new ValueSetsServiceMock();
            }

            $client = $this->createGuzzleClient($valueSetsConfig->getUrl(), $coronaCheckConfig->getProxy());
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
    protected function getCoronaCheckConfig(mixed $config): Config
    {
        if (!is_array($config)) {
            throw new CoronaCheckServiceException('Corona check config is not an array');
        }

        try {
            return (new MapperBuilder())
                ->mapper()
                ->map(
                    Config::class,
                    (Source::array($config))
                        ->camelCaseKeys()
                );
        } catch (MappingError $error) {
            throw new CoronaCheckServiceException("Could not initialize CoronaCheck service config", 0, $error);
        }
    }
}
