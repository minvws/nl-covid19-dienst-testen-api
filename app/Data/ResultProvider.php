<?php

declare(strict_types=1);

namespace App\Data;

class ResultProvider
{
    public function __construct(
        protected readonly string $name,
        /** @var array<int, ResultProviderCertificate> $certificates */
        protected readonly array $certificates,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array<int, ResultProviderCertificate>
     */
    public function getCertificates(): array
    {
        return $this->certificates;
    }
}
