<?php

declare(strict_types=1);

namespace App\Data;

use RuntimeException;

class ResultProviderCertificate
{
    /**
     * @psalm-pure
     * @param string $cert
     * @param string $chain
     */
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

    /**
     * @psalm-pure
     * @param string $cert
     * @param string $chain
     * @return self
     */
    public static function fromBase64CertAndChain(string $cert, string $chain = ""): self
    {
        $cert = base64_decode($cert, true);
        if (!empty($chain)) {
            $chain = base64_decode($chain, true);
        }

        if (!is_string($cert) || !is_string($chain)) {
            throw new RuntimeException('Invalid base64 encoded certificate or chain');
        }

        return new self(
            cert: $cert,
            chain: $chain,
        );
    }
}
