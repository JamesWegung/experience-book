<?php

use App\Http\Controllers\ContentType\GetAttributesController as ContentTypeGetAttributes;
use App\Http\Controllers\Event\EventController;
use App\Http\Controllers\Telegram\TelegramLinkController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/events/autocomplete', [EventController::class, 'autocomplete']);

    Route::get('/content-types/{content_type}/attributes', ContentTypeGetAttributes::class);



    Route::post('/telegram/generate-token', [TelegramLinkController::class, 'generateToken']);

});
