<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use MinVWS\Crypto\Laravel\SignatureCryptoInterface;

class SignResponseMiddleware
{
    public function __construct(protected SignatureCryptoInterface $signatureService)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $payload = $response->getContent();

        // TODO: Throw custom exception for JsonException
        $response->setContent(json_encode([
            'payload' => base64_encode($payload),
            'signature' => $this->signatureService->sign($payload, true),
        ], JSON_THROW_ON_ERROR));

        return $response;
    }
}
