<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\ResultProvider;
use App\Exceptions\ResultProviderNotFoundException;

class BaseResultProvidersService implements ResultProvidersInterface
{
    /** @var array<string, ResultProvider> $providers */
    protected array $providers = [];

    /**
     * @return array<string, ResultProvider>
     */
    public function getProviders(): array
    {
        return $this->providers;
    }

    /**
     * @param string $providerName
     * @return ResultProvider
     * @throws ResultProviderNotFoundException
     */
    public function getProvider(string $providerName): ResultProvider
    {
        $provider = $this->providers[$providerName] ?? null;
        if ($provider === null) {
            throw new ResultProviderNotFoundException("Provider $providerName not found");
        }

        return $this->providers[$providerName];
    }
}
