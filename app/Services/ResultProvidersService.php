<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\ResultProvider;
use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;
use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;
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
        protected ExceptionHandler $exceptionHandler,
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
            /** @var array<string, ResultProvider> $providers */
            $providers = (new MapperBuilder())
                ->mapper()
                ->map(
                    'array<string, ' . ResultProvider::class . '>',
                    Source::file(new SplFileObject($providersConfigPath))
                );
        } catch (Exception $error) {
            $this->exceptionHandler->report($error);
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
