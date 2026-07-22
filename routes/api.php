<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AIController;

use App\Http\Controllers\ContextSyncController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes use the 'api' middleware group (stateless, no CSRF, with
| rate limiting). Prefix "/api" is applied automatically by RouteServiceProvider.
|
*/

Route::post('/analyze',             [AIController::class, 'analyze']);
Route::post('/refine-blueprint',    [AIController::class, 'refineBlueprint']);
Route::post('/generate-blueprints', [AIController::class, 'generateBlueprints']);
Route::post('/context/sync',        [ContextSyncController::class, 'sync']);

