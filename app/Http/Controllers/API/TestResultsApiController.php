<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TestResultsRequest;
use Illuminate\Http\JsonResponse;

class TestResultsApiController extends Controller
{
    public function __invoke(TestResultsRequest $request): JsonResponse
    {
        return response()->json([
            'success' => true
        ]);
    }
}
