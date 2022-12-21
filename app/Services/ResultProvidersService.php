<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\ResultProvider;
use App\Data\ResultProviderCertificate;
use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;
use Exception;
use RuntimeException;
use SplFileObject;
use Throwable;

class ResultProvidersService
{
    /** @var array<string, ResultProvider> $providers */
    protected array $providers = [];

    /**
     * @throws Throwable
     */
    public function __construct(
        protected readonly string $providersConfigPath,
    ) {
        $this->providers = $this->loadProvidersFromConfig($this->providersConfigPath);
    }

    /**
     * @return array<string, ResultProvider>
     * @throws Throwable
     */
    protected function loadProvidersFromConfig(string $providersConfigPath): array
    {
        try {
            $providers = (new MapperBuilder())
                ->registerConstructor(ResultProviderCertificate::fromBase64CertAndChain(...))
                ->mapper()
                ->map(
                    'array<string, ' . ResultProvider::class . '>',
                    Source::file(new SplFileObject($providersConfigPath))
                );
        } catch (Exception $error) {
            throw new RuntimeException('Providers config file is not valid', 0, $error);
        }

        return $providers;
    }

    /**
     * @return array<string, ResultProvider> $providers
     */
    public function getProviders(): array
    {
        return $this->providers;
    }

    /**
     * @throws Throwable
     */
    public function getProvider(string $providerName): ResultProvider
    {
        $provider = $this->providers[$providerName] ?? null;
        throw_if($provider === null, new RuntimeException('Provider not found'));

        return $this->providers[$providerName];
    }
}
