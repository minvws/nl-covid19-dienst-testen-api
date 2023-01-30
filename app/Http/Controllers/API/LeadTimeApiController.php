<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Enums\EndpointType;
use App\Exceptions\InvalidCmsSignatureException;
use App\Http\Controllers\Controller;
use App\Http\Requests\LeadTimeRequest;
use App\Services\ResultProvidersFileService;
use Illuminate\Http\JsonResponse;

class LeadTimeApiController extends Controller
{
    /**
     * @throws InvalidCmsSignatureException
     */
    public function __invoke(LeadTimeRequest $request, ResultProvidersFileService $fileService): JsonResponse
    {
        $fileService->storeProviderData(
            provider: $request->getProvider(),
            data: $request->getSafePayload(),
            endpointType: EndpointType::LeadTime
        );

        return response()->json([
            'success' => true
        ]);
    }
}
