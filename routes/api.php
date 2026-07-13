<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AIController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes use the 'api' middleware group (stateless, no CSRF, with
| rate limiting). Prefix "/api" is applied automatically by RouteServiceProvider.
|
*/

Route::post('/analyze',            [AIController::class, 'analyze']);
Route::post('/refine-blueprint',   [AIController::class, 'refineBlueprint']);
Route::post('/generate-blueprints', [AIController::class, 'generateBlueprints']);
