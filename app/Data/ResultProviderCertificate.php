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

    public static function fromBase64CertAndChain(string $cert, string $chain = ""): self
    {
        return new self(
            cert: base64_decode($cert),
            chain: !empty($chain) ? base64_decode($chain) : "",
        );
    }
}
