<?php

declare(strict_types=1);

namespace App\Services\CoronaCheck;

use App\Exceptions\CoronaCheckServiceException;

interface ValueSetsInterface
{
    /**
     * @return array<int, string>
     * @throws CoronaCheckServiceException
     */
    public function getCovid19LabTestManufacturerAndNameValues(): array;

    /**
     * @return array<string, mixed>
     * @throws CoronaCheckServiceException
     */
    public function fetch(): array;

    public function isHealthy(): bool;

    public function clearCache(): void;
}
