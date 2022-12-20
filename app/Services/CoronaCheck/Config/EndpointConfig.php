<?php

declare(strict_types=1);

namespace App\Services\CoronaCheck\Config;

use App\Exceptions\CoronaCheckServiceException;
use RuntimeException;
use Symfony\Component\Finder\SplFileInfo;

class EndpointConfig
{
    public function __construct(
        protected readonly string $url,
        protected readonly int $cacheTtl,
        protected readonly string $certificateFilePaths,
    ) {
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getCacheTtl(): int
    {
        return $this->cacheTtl;
    }

    /**
     * @return string[]
     */
    public function getCertificateFilePaths(): array
    {
        if (empty($this->certificateFilePaths)) {
            return [];
        }

        return array_filter(explode(",", $this->certificateFilePaths));
    }

    /**
     * @return string[] Array of certificate contents
     * @throws CoronaCheckServiceException
     */
    public function getCertificates(): array
    {
        $certificateFilePaths = $this->getCertificateFilePaths();
        if (count($certificateFilePaths) === 0) {
            return [];
        }

        $certificates = [];

        foreach ($certificateFilePaths as $certPath) {
            try {
                $certificateFile = new SplFileInfo($certPath, '', '');
                $certificates[] = $certificateFile->getContents();
            } catch (RuntimeException $e) {
                throw new CoronaCheckServiceException("Could not read certificate file: {$certPath}", 0, $e);
            }
        }

        return $certificates;
    }
}
