<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Enums\EndpointType;
use App\Http\Controllers\Controller;
use App\Http\Requests\LeadTimeRequest;
use App\Services\ResultProvidersFileService;
use DateTimeImmutable;
use Illuminate\Http\JsonResponse;

class LeadTimeApiController extends Controller
{
    public function __invoke(LeadTimeRequest $request, ResultProvidersFileService $fileService): JsonResponse
    {
        $provider = $request->validated('Aanbieder');

        // @phpstan-ignore-next-line - just for now
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $request->validated('Datum'));

        // @phpstan-ignore-next-line - just for now
        $fileService->storeProviderData($provider, $date, $request->safe()->toArray(), EndpointType::LeadTime);

        return response()->json([
            'success' => true
        ]);
    }
}
