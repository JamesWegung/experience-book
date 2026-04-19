<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GetHintsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $query = $request->get('query');
        $content_type_id = $request->get('content_type_id');
        $user_id = auth()->id();

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $cache_key = "autocomplete_" . md5("{$user_id}_{$query}_{$content_type_id}");

        $suggestions = Cache::remember($cache_key, 300, function () use ($query, $content_type_id, $user_id) {
            return Content::where('name', 'LIKE', $query . '%')
                ->where('content_type_id', $content_type_id)
                ->where(function ($q) use ($user_id) {
                    $q->where('user_id', $user_id)
                        ->orWhere('is_common', 1)
                        ->orWhereHas('events', function ($q) use ($user_id) {
                            $q->where('user_id', $user_id);
                        });
                })
                ->distinct('name')
                ->pluck('name')
                ->take(10);
        });

        return response()->json($suggestions);
    }
}
