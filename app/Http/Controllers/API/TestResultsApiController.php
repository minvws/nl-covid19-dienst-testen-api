<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Enums\EndpointType;
use App\Http\Controllers\Controller;
use App\Http\Requests\TestResultsRequest;
use App\Services\ResultProvidersFileService;
use DateTimeImmutable;
use Illuminate\Http\JsonResponse;

class TestResultsApiController extends Controller
{
    public function __invoke(TestResultsRequest $request, ResultProvidersFileService $fileService): JsonResponse
    {
        $provider = $request->validated('Aanbieder');

        // @phpstan-ignore-next-line - just for now
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $request->validated('Datum'));

        // @phpstan-ignore-next-line - just for now
        $fileService->storeProviderData($provider, $date, $request->safe()->toArray(), EndpointType::TestResults);

        return response()->json([
            'success' => true
        ]);
    }
}
