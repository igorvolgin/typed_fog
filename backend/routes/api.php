<?php

use App\Http\Controllers\CountriesController;
use App\Http\Middleware\Auth0Middleware;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json(['status' => 'ok']));

Route::middleware(Auth0Middleware::class)->group(function () {
    Route::get('/countries', [CountriesController::class, 'index']);
    Route::get('/countries/{code}', [CountriesController::class, 'show']);
});
