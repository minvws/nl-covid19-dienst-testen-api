<?php

declare(strict_types=1);

namespace App\Services\CoronaCheck;

use App\Exceptions\CoronaCheckServiceException;
use Illuminate\Support\Arr;

class ValueSetsService extends Service implements ValueSetsInterface
{
    public const CACHE_KEY = 'value_sets_config';

    /**
     * @return array<int, array{code: string, name: string, active: bool}>
     * @throws CoronaCheckServiceException
     */
    public function getCovid19LabTestManufacturerAndName(): array
    {
        return $this->fetchAndMapVaccinesData('covid-19-lab-test-manufacturer-and-name');
    }

    /**
     * @return array<int, string>
     * @throws CoronaCheckServiceException
     */
    public function getCovid19LabTestManufacturerAndNameValues(): array
    {
        $values = $this->getCovid19LabTestManufacturerAndName();

        return array_merge(
            array_column($values, 'code'),
            array_column($values, 'name'),
        );
    }

    /**
     * @return array<int, array{code: string, name: string, active: bool}>
     * @throws CoronaCheckServiceException
     */
    protected function fetchAndMapVaccinesData(string $key): array
    {
        $data = Arr::get($this->fetch(), $key);
        if (!is_array($data)) {
            throw new CoronaCheckServiceException("Invalid data for key {$key}");
        }

        return $this->mapVaccinesData($data);
    }

    /**
     * @param array<string, array{display: string, active: bool}> $data
     * @return array<int, array{code: string, name: string, active: bool}>
     */
    protected function mapVaccinesData(array $data): array
    {
        /**
         * @var array<int, array{code: string, name: string, active: bool}>
         */
        return collect($data)
            ->map(function ($item, $key) {
                $active = false;
                if (!empty($item['active'])) {
                    $active = (bool) $item['active'];
                }

                return [
                    'code' => (string) $key,
                    'name' => $item['display'] ?? '',
                    'active' => $active,
                ];
            })
            ->sortBy('code')
            ->values()
            ->toArray();
    }
}
