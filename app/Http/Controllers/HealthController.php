<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\CoronaCheck\ValueSetsService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HealthController extends Controller
{
    public function __construct(
        protected ValueSetsService $valueSetsService,
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
