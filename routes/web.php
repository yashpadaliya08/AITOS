<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ExportController;

Route::get('/', function () {
    return view('landing');
});

Route::get('/wizard', function () {
    return view('wizard');
});

Route::get('/requirements', function () {
    return view('requirements');
});

Route::get('/blueprint', function () {
    return view('blueprint');
});

Route::get('/team', function () {
    return view('team');
});

Route::get('/compiler', function () {
    return view('compiler');
});

Route::get('/preview', function () {
    return view('preview');
});

Route::get('/export', function () {
    return view('export');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/settings', function () {
    return view('settings');
});

Route::post('/export/download', [ExportController::class, 'download']);

Route::post('/api/analyze', [App\Http\Controllers\AIController::class, 'analyze']);


