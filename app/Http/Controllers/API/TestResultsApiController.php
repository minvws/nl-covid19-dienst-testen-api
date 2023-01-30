<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Enums\EndpointType;
use App\Exceptions\InvalidCmsSignatureException;
use App\Http\Controllers\Controller;
use App\Http\Requests\TestResultsRequest;
use App\Services\ResultProvidersFileService;
use Illuminate\Http\JsonResponse;

class TestResultsApiController extends Controller
{
    /**
     * @throws InvalidCmsSignatureException
     */
    public function __invoke(TestResultsRequest $request, ResultProvidersFileService $fileService): JsonResponse
    {
        $fileService->storeProviderData(
            provider: $request->getProvider(),
            data: $request->getSafePayload(),
            endpointType: EndpointType::TestResults
        );

        return response()->json([
            'success' => true
        ]);
    }
}
