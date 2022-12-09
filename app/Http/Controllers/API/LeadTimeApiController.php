<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeadTimeRequest;
use Illuminate\Http\JsonResponse;

class LeadTimeApiController extends Controller
{
    public function __invoke(LeadTimeRequest $request): JsonResponse
    {
        $aanbieder = $request->validated('Aanbieder');
        dump($aanbieder);

        return response()->json([
            'success' => true
        ]);
    }
}
