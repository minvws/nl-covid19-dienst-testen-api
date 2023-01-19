<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EndpointType;
use DateTimeImmutable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Uid\Uuid;

class ResultProvidersFileService
{
    public function __construct(
        protected readonly string $storagePath,
    ) {
    }

    /**
     * @param string $provider
     * @param array<string, bool|float|int|string|null> $data
     * @param EndpointType $endpointType
     * @return void
     */
    public function storeProviderData(
        string $provider,
        array $data,
        EndpointType $endpointType
    ): void {
        $filePath = $this->getFilePath($provider, Carbon::now()->toDateTimeImmutable(), $endpointType);

        $this->storeCsvFile($filePath, $this->getCsvRows($data));
    }

    /**
     * @param string $filePath
     * @param array<array<int|string, bool|float|int|string|null>> $rows
     * @return void
     */
    protected function storeCsvFile(string $filePath, array $rows): void
    {
        $dirPath = dirname($filePath);

        if (!file_exists($dirPath) && !mkdir($dirPath, 0777, true) && !is_dir($dirPath)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $dirPath));
        }

        $file = fopen($filePath, 'w');
        if (!is_resource($file)) {
            throw new RuntimeException(sprintf('File "%s" was not created', $filePath));
        }

        foreach ($rows as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
    }

    /**
     * @param array<string, bool|float|int|string|null> $data
     * @return array<int, array<int, bool|float|int|string|null>>
     */
    protected function getCsvRows(array $data): array
    {
        $rows = [];
        $rows[] = array_keys($data);
        $rows[] = array_values($data);

        return $rows;
    }

    protected function getFilePath(string $provider, DateTimeImmutable $date, EndpointType $endpointType): string
    {
        $path = $this->storagePath . '/' . $date->format('Y-m-d');
        $provider = Str::slug($provider);

        return $path . '/' . $provider . '-' . $endpointType->value . '-' . Uuid::v4() . '.csv';
    }
}
