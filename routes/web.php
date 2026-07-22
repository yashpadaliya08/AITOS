<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| These routes use the 'web' middleware group (sessions, CSRF, cookies).
| All AI JSON API endpoints have been moved to routes/api.php.
|
*/

Route::get('/', fn() => view('landing'));
Route::get('/wizard', fn() => view('wizard'));
Route::get('/requirements', fn() => view('requirements'));
Route::get('/blueprint', fn() => view('blueprint'));
Route::get('/team', fn() => view('team'));
Route::get('/compiler', fn() => view('compiler'));
Route::get('/preview', fn() => view('preview'));
Route::get('/export', fn() => view('export'));
Route::get('/dashboard', fn() => view('dashboard'));
Route::get('/settings', fn() => view('settings'));

Route::post('/export/download',         [ExportController::class, 'download']);
Route::post('/export/brief',            [ExportController::class, 'downloadBrief']);
Route::post('/export/preview-prompts',  [ExportController::class, 'previewPrompts']);
Route::post('/import',                  [ExportController::class, 'import']);


