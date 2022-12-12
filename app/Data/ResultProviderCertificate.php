<?php

declare(strict_types=1);

namespace App\Data;

class ResultProviderCertificate
{
    public function __construct(
        protected readonly string $cert,
        protected readonly string $chain = "",
    ) {
    }

    public function getChain(): string
    {
        return $this->chain;
    }

    public function getCert(): string
    {
        return $this->cert;
    }

    public function hasChain(): bool
    {
        return !empty($this->chain);
    }
}
