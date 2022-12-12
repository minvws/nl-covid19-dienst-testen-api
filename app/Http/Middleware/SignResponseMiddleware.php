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

        $content = $response->getContent() ?: '';

        // Return the response if the content already contains a payload and signature
        // This is possible on exceptions for example ValidationException
        if ($this->contentIsSigned($content)) {
            return $response;
        }

        $response->setContent(json_encode([
            'payload' => base64_encode($content),
            'signature' => $this->signatureService->sign($content, true),
        ], JSON_THROW_ON_ERROR));

        return $response;
    }

    /**
     * Check if the content is already signed
     * By using json_decode and checking if the payload and signature are not empty
     * @param string $content Response content in string format
     * @return bool True when payload and signature are not empty
     */
    protected function contentIsSigned(string $content): bool
    {
        try {
            $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
            return is_array($decoded) && !empty($decoded['payload']) && !empty($decoded['signature']);
        } catch (JsonException) {
        }

        return false;
    }
}
