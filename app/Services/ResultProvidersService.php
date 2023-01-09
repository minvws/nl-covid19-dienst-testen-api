<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\ResultProvider;
use App\Data\ResultProviderCertificate;
use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;
use RuntimeException;
use SplFileObject;
use Throwable;

class ResultProvidersService extends BaseResultProvidersService
{
    /**
     * @throws RuntimeException
     */
    public function __construct(
        protected readonly string $providersConfigPath,
    ) {
        $this->providers = $this->loadProvidersFromConfig($this->providersConfigPath);
    }

    /**
     * @return array<string, ResultProvider>
     * @throws RuntimeException
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
        } catch (Throwable $error) {
            throw new RuntimeException('Providers config file is not valid', 0, $error);
        }

        return $providers;
    }
}
