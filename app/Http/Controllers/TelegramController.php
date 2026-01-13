<?php

namespace App\Http\Controllers;

use JetBrains\PhpStorm\NoReturn;

class TelegramController extends Controller
{

    #[NoReturn] public function panel(): void
    {
        dd('hi');
    }
}
