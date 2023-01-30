<?php

declare(strict_types=1);

namespace App\Services\CoronaCheck;

use App\Exceptions\CoronaCheckServiceException;
use GuzzleHttp\ClientInterface;
use Illuminate\Contracts\Cache\Repository;
use JsonException;
use MinVWS\Crypto\Laravel\Service\Signature\SignatureVerifyConfig;
use MinVWS\Crypto\Laravel\SignatureCryptoInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Throwable;

class Service
{
    public const CACHE_KEY = '';

    /**
     * @param ClientInterface $client
     * @param Repository|null $cacheRepository
     * @param SignatureCryptoInterface|null $signatureService
     * @param array<string|null> $certificates
     * @param int $cacheTtl
     */
    public function __construct(
        protected ClientInterface $client,
        protected ?Repository $cacheRepository = null,
        protected ?SignatureCryptoInterface $signatureService = null,
        protected array $certificates = [],
        protected int $cacheTtl = 900,
    ) {
        if (!static::CACHE_KEY) {
            $this->cacheRepository = null;
        }
    }

    /**
     * @return array<string, mixed>
     * @throws CoronaCheckServiceException
     * @throws InvalidArgumentException
     */
    public function fetch(): array
    {
        if ($this->cacheRepository) {
            $value = $this->cacheRepository->get(static::CACHE_KEY);
            if (is_array($value)) {
                return $value;
            }
        }

        $content = $this->getRemoteContent();
        $signature = $content['signature'];
        $payload = base64_decode($content['payload']);

        // Verify against all given certs
        if (! $this->signatureIsValid($signature, $payload)) {
            throw new CoronaCheckServiceException("Unable to validate remote content against any signature");
        }

        try {
            $value = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new CoronaCheckServiceException("Something went wrong with json decoding the payload", 0, $e);
        }

        if (empty($value) || !is_array($value)) {
            throw new CoronaCheckServiceException("Remote content is not a valid JSON array");
        }

        $this->cacheRepository?->put(static::CACHE_KEY, $value, $this->cacheTtl);

        return $value;
    }

    public function clearCache(): void
    {
        $this->cacheRepository?->forget(static::CACHE_KEY);
    }

    /**
     * @return array<int, string|null>
     */
    protected function getCertificatesToCheck(): array
    {
        $certificates = $this->certificates;
        // Add null, which means we check against signature's internal certificate
        if (count($certificates) === 0) {
            $certificates[] = null;
        }
        return $certificates;
    }

    /**
     * @return array{payload: string, signature: string}
     * @throws CoronaCheckServiceException
     */
    protected function getRemoteContent(): array
    {
        try {
            $result = $this->client->request('GET', '');
            $contents = $result->getBody()->getContents();
            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $e) {
            throw new CoronaCheckServiceException("Unable to fetch remote content or content is not json", 0, $e);
        }

        if (
            !is_array($data)
            || !is_string($data['payload'] ?? null)
            || !is_string($data['signature'] ?? null)
        ) {
            throw new CoronaCheckServiceException("Remote content does not contain payload or signature");
        }
        return $data;
    }

    protected function signatureIsValid(string $signature, string $payload): bool
    {
        // If we do not have a signature service we can't verify the signature
        if ($this->signatureService === null) {
            return true;
        }

        // @TODO: we probably do not want to use noverify, but use purpose:any or certificates with correct EKU
        $config = (new SignatureVerifyConfig())
            ->setNoVerify(true);

        foreach ($this->getCertificatesToCheck() as $cert) {
            if (
                $this->signatureService->verify(
                    signedPayload: $signature,
                    content: $payload,
                    detachedCertificate: $cert,
                    verifyConfig: $config
                )
            ) {
                return true;
            }
        }

        return false;
    }
}
