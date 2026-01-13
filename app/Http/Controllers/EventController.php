<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\ContentType;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sort = request('sort', 'created_at');
        $order = request('order', 'desc');
        $searchStr = request('search');
        $selectedCategories = is_array(request('categories'))
            ? request('categories')
            : (request('categories') ? explode(',', request('categories')) : []);

        $events = \auth()->user()
            ->events()
            ->with([
                'content.contentType',
            ])
            ->when(!empty($selectedCategories), function ($q) use ($selectedCategories) {
                return $q->whereHas('content.contentType', function ($query) use ($selectedCategories) {
                    $query->whereIn('id', $selectedCategories);
                });
            })
            ->when(!empty($searchStr), function ($q) use ($searchStr){
                return $q->where(function ($q) use ($searchStr) {
                    $q->whereHas('content', function ($q) use ($searchStr) {
                        $q->where('name', 'LIKE', "%$searchStr%")
                            ->orWhere('description', 'LIKE', "%$searchStr%");
                    })
                    ->orWhere('review', 'LIKE', "%$searchStr%");
                });
            })
            ->when($sort === 'rating', function ($q) use ($order) {
                return $q->orderBy('rating', $order);
            })
            ->when($sort === 'created_at', function ($q) use ($order) {
                return $q->orderBy('created_at', $order);
            })
            ->when($sort === 'completed_at', function ($q) use ($order) {
                if ($order === 'asc') {
                    return $q->orderByRaw('CASE WHEN completed_at IS NULL THEN 1 ELSE 0 END')
                        ->orderBy('completed_at', 'asc');
                } else {
                    return $q->orderByRaw('CASE WHEN completed_at IS NULL THEN 0 ELSE 1 END')
                        ->orderBy('completed_at', 'desc');
                }
            })
            ->when($sort === 'content_type', function ($q) use ($order) {
                return $q->join('contents', 'events.content_id', '=', 'contents.id')
                    ->join('content_types', 'contents.content_type_id', '=', 'content_types.id')
                    ->orderBy('content_types.name', $order)
                    ->select('events.*');
            })
            ->paginate(15);

        $contentTypes = ContentType::all();

        return view('my-exp', compact('events', 'contentTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $contentTypes = ContentType::take(10)->get();
        $contentTypeOptions = [];
        foreach ($contentTypes as $contentType) {
            $contentTypeOptions[] = (object)['name' => $contentType->name, 'value' => $contentType->id];
        }

        return view('event.edit', compact('contentTypeOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedDataForContent = $request->validate([
            'content_type_id' => 'required|numeric',
            'content_name' => 'required|string|max:255'
        ]);

        $contentData = [
            'name' => $validatedDataForContent['content_name'],
            'content_type_id' => $validatedDataForContent['content_type_id'],
            'user_id' => auth()->id()
        ];

        $validatedDataForEvent = $request->validate([
            'rating' => 'numeric|max:10',
            'review' => 'string|nullable',
            'completed_date' => 'date',
            'completed_time' => 'date_format:H:i'
        ]);

        try {
            DB::transaction(function () use ($validatedDataForEvent, $contentData) {
                $content = Content::where('name', $contentData['name'])->first();

                if (!$content) {
                    $content = Content::create($contentData);
                }

                $completedAt = Carbon::parse($validatedDataForEvent['completed_date']);

                $validatedDataForEvent['completed_at'] = $completedAt;
                $validatedDataForEvent['user_id'] = auth()->user()->id;
                $validatedDataForEvent['content_id'] = $content->id;

                Event::create($validatedDataForEvent);
            });

            return redirect()->route('events.index')->with('success', 'Запись добавлена!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Ошибка при создании записи: ' . $e->getMessage()]);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $contentTypes = ContentType::take(10)->get();
        $contentTypeOptions = [];
        foreach ($contentTypes as $contentType) {
            $contentTypeOptions[] = (object)['name' => $contentType->name, 'value' => $contentType->id];
        }

        $event = Event::with([
            'content.contentType'
        ])->find($id);

        return view('event.edit', compact('event', 'contentTypeOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $event = Event::find($id);

        $validatedDataForContent = $request->validate([
            'content_type_id' => 'required|numeric',
            'content_name' => 'required|string|max:255'
        ]);

        $contentData = [
            'name' => $validatedDataForContent['content_name'],
            'content_type_id' => $validatedDataForContent['content_type_id'],
            'user_id' => auth()->id()
        ];

        $validatedDataForEvent = $request->validate([
            'rating' => 'numeric|max:10',
            'review' => 'string|nullable',
            'completed_date' => 'date',
            'completed_time' => 'date_format:H:i'
        ]);

        try {
            DB::transaction(function () use ($validatedDataForEvent, $contentData, $event) {
                $content = Content::where(['name' => $contentData['name'], 'content_type_id' => $contentData['content_type_id']])->first();

                if (!$content) {
                    $content = Content::create($contentData);
                }

                $completedAt = Carbon::parse($validatedDataForEvent['completed_date']);

                $validatedDataForEvent['completed_at'] = $completedAt;
                $validatedDataForEvent['content_id'] = $content->id;

                $event->update($validatedDataForEvent);
            });

            return redirect()->route('events.index');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Ошибка при создании записи: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $event = Event::find($id);

        if ($event){
            $event->delete();
        }

        return redirect()->route('events.index');
    }

    public function autocomplete(Request $request): JsonResponse
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
