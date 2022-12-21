<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EndpointType;
use DateTimeImmutable;
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
     * @param DateTimeImmutable $date
     * @param array<string, string> $data
     * @return void
     */
    public function storeLeadTimeData(string $provider, DateTimeImmutable $date, array $data): void
    {
        $filePath = $this->getFilePath($provider, $date, EndpointType::LeadTime);

        $this->storeCsvFile($filePath, $data);
    }

    /**
     * @param string $filePath
     * @param array<string, string> $data
     * @return void
     */
    protected function storeCsvFile(string $filePath, array $data): void
    {
        $dirPath = dirname($filePath);

        if (!file_exists($dirPath) && !mkdir($dirPath, 0777, true) && !is_dir($dirPath)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $dirPath));
        }

        $file = fopen($filePath, 'w');
        if (!is_resource($file)) {
            throw new RuntimeException(sprintf('File "%s" was not created', $filePath));
        }

        foreach ($this->getCsvRows($data) as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
    }

    /**
     * @param array<string, string> $data
     * @return array<array<string>>
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
