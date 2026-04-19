<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class ShowGuestPageController extends Controller
{
    public function __invoke(): View
    {
        return view('guest.index');
    }
}
