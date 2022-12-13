<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Http\Middleware\SignResponseMiddleware;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use MinVWS\Crypto\Laravel\SignatureCryptoInterface;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e): Response
    {
        $response = parent::render($request, $e);

        // Check if sign response middleware is enabled
        if (!$this->middlewareIsActiveOnRoute($request->route(), SignResponseMiddleware::class)) {
            return $response;
        }

        // If so, then sign the response
        $payload = $response->getContent() ?: '';
        $response->setContent(json_encode([
            'payload' => base64_encode($payload),
            'signature' => app(SignatureCryptoInterface::class)->sign($payload, true),
        ], JSON_THROW_ON_ERROR));

        return $response;
    }

    protected function middlewareIsActiveOnRoute(?Route $route, string $middlewareClass): bool
    {
        if (!$route) {
            return false;
        }

        return in_array($middlewareClass, $route->gatherMiddleware(), true);
    }
}
