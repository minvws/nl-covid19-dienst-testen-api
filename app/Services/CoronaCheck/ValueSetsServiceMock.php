<?php

declare(strict_types=1);

namespace App\Services\CoronaCheck;

class ValueSetsServiceMock implements ValueSetsInterface
{
    /**
     * @return array<int, string>
     */
    public function getCovid19LabTestManufacturerAndNameValues(): array
    {
        return [
            '1234',
            '5678',
            'name1',
            'name2',
            'name3',
            'PCR',
            'Antigeen',
            'Antistoffen',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function fetch(): array
    {
        return [
            'mock' => [],
        ];
    }

    public function isHealthy(): bool
    {
        return true;
    }

    public function clearCache(): void
    {
        // Mock does not have cache
    }
}
