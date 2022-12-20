<?php

declare(strict_types=1);

namespace App\Services\CoronaCheck\Config;

class Config
{
    public function __construct(
        protected readonly EndpointConfig $valueSets,
        protected readonly ?string $proxy = null,
    ) {
    }

    public function getValueSetsConfig(): EndpointConfig
    {
        return $this->valueSets;
    }

    public function getProxy(): ?string
    {
        if (empty($this->proxy)) {
            return null;
        }

        return $this->proxy;
    }
}
