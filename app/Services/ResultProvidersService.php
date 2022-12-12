<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\ResultProvider;
use CuyZ\Valinor\Mapper\MappingError;
use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;
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
    public function __construct(private readonly string $providersConfigPath)
    {
        $this->providers = $this->loadProvidersFromConfig($this->providersConfigPath);
    }

    /**
     * @return array<string, ResultProvider>
     */
    protected function loadProvidersFromConfig(string $providersConfigPath): array
    {
        try {
            $providers = (new MapperBuilder())
                ->mapper()
                ->map(
                    'array<string, ' . ResultProvider::class . '>',
                    Source::file(new SplFileObject($providersConfigPath))
                );
        } catch (MappingError $error) {
            report($error);
            throw new RuntimeException('Providers config file is not valid');
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
