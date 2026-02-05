<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class OverviewController extends Controller
{
    public function index(): Factory|View
    {
        $userId = auth()->id();
        $stats = Event::getContentTypeStats($userId)->get();

        return view('overview.index', compact('stats'));
    }
}
