<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class OverviewController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $stats = Event::getContentTypeStats($user->id)->get();

        return view('overview.index', compact('stats'));
    }
}
