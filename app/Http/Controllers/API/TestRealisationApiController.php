<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Enums\EndpointType;
use App\Exceptions\InvalidCmsSignatureException;
use App\Http\Controllers\Controller;
use App\Http\Requests\TestRealisationRequest;
use App\Services\ResultProvidersFileService;
use Illuminate\Http\JsonResponse;

class TestRealisationApiController extends Controller
{
    /**
     * @throws InvalidCmsSignatureException
     */
    public function __invoke(TestRealisationRequest $request, ResultProvidersFileService $fileService): JsonResponse
    {
        $fileService->storeProviderData(
            provider: $request->getProvider(),
            data: $request->getSafePayload(),
            endpointType: EndpointType::TestRealisation
        );

        return response()->json([
            'success' => true
        ]);
    }
}
