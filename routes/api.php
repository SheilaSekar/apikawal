<?php

use App\Http\Controllers\Api\V1\PublicApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:public-api')->prefix('v1')->group(function () {
    Route::get('/reports', [PublicApiController::class, 'reports']);
    Route::get('/reports/{report:code}', [PublicApiController::class, 'report']);
    Route::get('/risk-areas', [PublicApiController::class, 'riskAreas']);
    Route::get('/stats', [PublicApiController::class, 'stats']);
});
Route::get('/health', [PublicApiController::class, 'health'])->middleware('throttle:public-api');
