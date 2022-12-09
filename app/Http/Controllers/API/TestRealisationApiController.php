<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TestRealisationRequest;
use Illuminate\Http\JsonResponse;

class TestRealisationApiController extends Controller
{
    public function __invoke(TestRealisationRequest $request): JsonResponse
    {
        return response()->json([
            'success' => true
        ]);
    }
}
