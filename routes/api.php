<?php

use App\Http\Controllers\ContentType\GetAttributesController as ContentTypeGetAttributes;
use App\Http\Controllers\Event\GetHintsController as EventGetHints;
use App\Http\Controllers\Telegram\TelegramLinkController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/events/autocomplete', EventGetHints::class);

    Route::get('/content-types/{content_type}/attributes', ContentTypeGetAttributes::class);



    Route::post('/telegram/generate-token', [TelegramLinkController::class, 'generateToken']);

});
