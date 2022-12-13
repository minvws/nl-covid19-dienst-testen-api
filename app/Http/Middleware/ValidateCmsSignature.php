<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Data\ResultProviderCertificate;
use App\Exceptions\InvalidCmsSignatureException;
use App\Services\ResultProvidersService;
use Closure;
use Exception;
use Illuminate\Http\Request;
use MinVWS\Crypto\Laravel\Factory;
use MinVWS\Crypto\Laravel\Service\Signature\SignatureVerifyConfig;
use MinVWS\Crypto\Laravel\TempFileInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ValidateCmsSignature
{
    public function __construct(
        protected ResultProvidersService $resultProvidersService,
        protected TempFileInterface $tempFileService,
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response) $next
     * @return Response
     * @throws Throwable
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the provider from the payload
        $providerName = $request->getProvider();

        // Get the public key from the provider
        $providerCertificates = $this->getCertificatesOfProvider($providerName);

        // Get the payload and signature from the request
        $payload = $request->getJsonPayload();
        $signature = $request->getSignature();

        // Check if the signature is valid
        $verified = $this->verifySignatureAgainstProviderCertificates(
            providerCertificates: $providerCertificates,
            signature: $signature,
            payload: $payload
        );
        throw_if(!$verified, new InvalidCmsSignatureException());

        return $next($request);
    }

    /**
     * @param string $providerName The provider name
     * @return array<int, ResultProviderCertificate> Array of provider certificates
     * @throws InvalidCmsSignatureException
     */
    protected function getCertificatesOfProvider(string $providerName): array
    {
        try {
            $provider = $this->resultProvidersService->getProvider($providerName);
        } catch (Throwable) {
            throw new InvalidCmsSignatureException();
        }

        return $provider->getCertificates();
    }

    /**
     * @param array<int, ResultProviderCertificate> $providerCertificates Array of the provider certificates
     * @param string $signature String of the signature
     * @param string $payload String of the payload
     * @return bool True if the signature is valid, false if invalid
     */
    protected function verifySignatureAgainstProviderCertificates(
        array $providerCertificates,
        string $signature,
        string $payload
    ): bool {
        // @TODO: we probably do not want to use noverify, but use purpose:any or certificates with correct EKU
        $config = (new SignatureVerifyConfig())
            ->setNoVerify(true)
        ;

        foreach ($providerCertificates as $providerCertificate) {
            // Create a temporary file of the chain because the signature service requests a file path
            $chainFile = null;
            $chainFilePath = null;
            if ($providerCertificate->hasChain()) {
                $chainFile = $this->tempFileService->createTempFileWithContent($providerCertificate->getChain());
                $chainFilePath = $this->tempFileService->getTempFilePath($chainFile);
            }

            // Create new signature service with ca certificate of provider
            $signatureService = Factory::createSignatureCryptoService(
                certificateChain: $chainFilePath,
                forceProcessSpawn: (bool) config('crypto.force_process_spawn'),
            );

            try {
                // Verify the signature and payload with the certificate
                if (
                    $signatureService->verify(
                        signedPayload: $signature,
                        content: $payload,
                        detachedCertificate: $providerCertificate->getCert(),
                        verifyConfig: $config,
                    )
                ) {
                    return true;
                }
            } catch (Exception $exception) {
                report($exception);
            } finally {
                // Close the temp file so it will be removed
                $this->tempFileService->closeTempFile($chainFile);
            }
        }

        return false;
    }
}
