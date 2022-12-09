<?php

declare(strict_types=1);

use App\Http\Controllers\API\LeadTimeApiController;
use App\Http\Controllers\API\TestRealisationApiController;
use App\Http\Controllers\API\TestResultsApiController;
use App\Http\Middleware\ValidateCmsSignature;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware([ValidateCmsSignature::class])->group(function () {
    Route::post('/test-realisation', TestRealisationApiController::class)
        ->name('test-realisation');
    Route::post('/test-results', TestResultsApiController::class)
        ->name('test-results');
    Route::post('/lead-time', LeadTimeApiController::class)
        ->name('lead-time');
});
