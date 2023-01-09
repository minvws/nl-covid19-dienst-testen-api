<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\ResultProvider;
use App\Exceptions\ResultProviderNotFoundException;

interface ResultProvidersInterface
{
    /**
     * @return array<string, ResultProvider>
     */
    public function getProviders(): array;

    /**
     * @param string $providerName
     * @return ResultProvider
     * @throws ResultProviderNotFoundException
     */
    public function getProvider(string $providerName): ResultProvider;
}
