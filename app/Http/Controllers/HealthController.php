<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\CoronaCheck\ValueSetsInterface;
use App\Services\ResultProvidersFileService;
use App\Services\ResultProvidersInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HealthController extends Controller
{
    /**
     * List dependencies so there will be a 500 error when the service is not bound.
     **/
    public function __construct(
        protected ValueSetsInterface $valueSetsService,
        protected ResultProvidersInterface $resultProvidersService,
        protected ResultProvidersFileService $resultProvidersFileService,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        $externals = [
            'value_sets' => $this->valueSetsService->isHealthy(),
        ];

        $healthy = $this->isHealthy($externals);

        return response()->json([
            'healthy' => $healthy,
            'externals' => $externals,
        ], $healthy ? Response::HTTP_OK : Response::HTTP_SERVICE_UNAVAILABLE);
    }

    /**
     * @param array<string, bool> $externals
     * @return bool
     */
    protected function isHealthy(array $externals): bool
    {
        foreach ($externals as $external) {
            if (!$external) {
                return false;
            }
        }

        return true;
    }
}
