<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use JetBrains\PhpStorm\NoReturn;

class TelegramController extends Controller
{

    #[NoReturn] public function panel(): void
    {
        dd('hi');
    }
}
