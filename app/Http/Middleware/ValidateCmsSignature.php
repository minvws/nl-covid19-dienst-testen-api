<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Exceptions\InvalidCmsSignatureException;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use MinVWS\Crypto\Laravel\Service\Signature\SignatureVerifyConfig;
use MinVWS\Crypto\Laravel\SignatureCryptoInterface;

class ValidateCmsSignature
{
    public function __construct(protected SignatureCryptoInterface $signatureService)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the provider from the payload
        $provider = $request->getProvider();

        // Get the public key from the provider
        $providerCertificates = $this->getCertificatesOfProvider($provider);

        // Get the signature from the request
        $payload = $request->getJsonPayload();
        $signature = $request->getSignature();

        // @TODO: we probably do not want to use noverify, but use purpose:any or certificates with correct EKU
        $config = (new SignatureVerifyConfig())
            ->setNoVerify(true)
        ;

        $verified = false;
        foreach ($providerCertificates as $cert) {
            if ($this->signatureService->verify($signature, $payload, $cert, verifyConfig: $config)) {
                $verified = true;
                break;
            }
        }
        throw_if(!$verified, new InvalidCmsSignatureException());

        return $next($request);
    }

    /**
     * @param string $provider The provider name
     * @return array<int, string> Array of certificates
     */
    protected function getCertificatesOfProvider(string $provider): array
    {
        // TODO: Based on name, load certificates from config via a service
        return [
            file_get_contents(storage_path('app/testprovider.example/cert.pem')),
        ];
    }
}
