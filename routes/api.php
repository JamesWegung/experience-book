<?php

use App\Http\Controllers\Event\EventController;
use App\Http\Controllers\TelegramLinkController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/events/autocomplete', [EventController::class, 'autocomplete']);




    Route::post('/telegram/generate-token', [TelegramLinkController::class, 'generateToken']);

});
