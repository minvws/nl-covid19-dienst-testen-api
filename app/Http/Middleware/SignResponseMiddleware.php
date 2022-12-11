<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use JsonException;
use MinVWS\Crypto\Laravel\SignatureCryptoInterface;
use Symfony\Component\HttpFoundation\Response;

class SignResponseMiddleware
{
    public function __construct(protected SignatureCryptoInterface $signatureService)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response) $next
     * @return Response
     * @throws JsonException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $payload = $response->getContent() ?: '';

        // TODO: Throw custom exception for JsonException
        $response->setContent(json_encode([
            'payload' => base64_encode($payload),
            'signature' => $this->signatureService->sign($payload, true),
        ], JSON_THROW_ON_ERROR));

        return $response;
    }
}
