<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\StoreEventRequest;
use App\Models\AttributeValue;
use App\Models\Content;
use App\Models\ContentType;
use App\Models\Event;
use App\Services\Event\EventsImport;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EventController extends Controller
{

    public function index(Request $request): Factory|View
    {
        $events = \auth()->user()
            ->events()
            ->with([
                'content.contentType',
                'content.attributeValues.attribute',
            ])
            ->filter($request)
            ->paginate(15)
            ->withQueryString();

        return view('events.index', [
            'events' => $events,
            'contentTypes' => ContentType::all(),
        ]);
    }

    public function create(): View
    {
        $contentTypes = ContentType::take(10)->get();
        $contentTypeOptions = [];
        foreach ($contentTypes as $contentType) {
            $contentTypeOptions[] = (object)['name' => $contentType->name, 'value' => $contentType->id];
        }

        return view('events.edit', compact('contentTypeOptions'));
    }

    public function store(StoreEventRequest $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                $contentData = $request->safe()->only(['name', 'description', 'content_type_id']);
                $content = Content::firstOrCreate($contentData);


                $customAttributes = $request->input('custom_attributes', []);

                foreach ($customAttributes as $attributeId => $value) {
                    if (!is_null($value) && $value !== '') {
                        AttributeValue::updateOrCreate([
                            'content_id' => $content->id,
                            'attribute_id' => $attributeId,
                        ],
                        ['value' => $value]
                        );
                    }
                }


                Event::create([
                    'user_id' => auth()->id(),
                    'content_id' => $content->id,
                    'rating' => $request->rating,
                    'review' => $request->review,
                    'completed_at' => $request->completed_at,
                ]);
            });

            return redirect()->route('events.index')->with('success', 'Запись добавлена!');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Ошибка при создании записи: ' . $e->getMessage()]);
        }

    }


    public function edit(string $id): Factory|View
    {
        $contentTypes = ContentType::take(10)->get();
        $contentTypeOptions = [];
        foreach ($contentTypes as $contentType) {
            $contentTypeOptions[] = (object)['name' => $contentType->name, 'value' => $contentType->id];
        }

        $event = Event::with([
            'content.contentType'
        ])->find($id);

        return view('events.edit', compact('event', 'contentTypeOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreEventRequest $request, Event $event): RedirectResponse
    {
        try {
            DB::transaction(function () use ($event, $request) {
                $contentData = $request->safe()->only(['name', 'description', 'content_type_id']);
                $content = Content::firstOrCreate($contentData);

                $event->update([
                    'content_id'   => $content->id,
                    'rating'       => $request->rating,
                    'review'       => $request->review,
                    'completed_at' => $request->completed_at,
                ]);

                $customAttributes = $request->input('custom_attributes', []);

                foreach ($customAttributes as $attributeId => $value) {
                    if (!is_null($value) && $value !== '') {
                        AttributeValue::updateOrCreate(
                            [
                                'content_id'   => $content->id,
                                'attribute_id' => $attributeId,
                            ],
                            ['value' => $value]
                        );
                    } else {
                        AttributeValue::where('content_id', $content->id)
                            ->where('attribute_id', $attributeId)
                            ->delete();
                    }
                }
            });

            return redirect()->route('events.index');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Ошибка при создании записи: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
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


    public function importByXlsx(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:5120',
                'mimes:xlsx,xls'
            ],
            ], [
            'file.required' => 'Выберите файл для загрузки.',
            'file.mimes'    => 'Файл должен быть в формате .xlsx или .xls',
            'file.max'      => 'Файл слишком большой (макс. 5 Мб).',
        ]);

        try {
            $import = new EventsImport;
            $import->import($request->file('file'));

            if (count($import->failures) > 0) {
                return back()
                    ->with('import_failures', $import->failures)
                    ->with('warning', 'Импорт завершен, но в некоторых строках были ошибки.');
            }

            return back()->with('success', 'Все мероприятия успешно импортированы');
        } catch (Exception $e) {
            return back()->withErrors(['file' => 'Произошла ошибка при чтении файла: ' . $e->getMessage()]);
        }
    }

    public function downloadTemplate(): StreamedResponse
    {
        $path = 'templates/events_template.xlsx';

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'Файл шаблона не найден');
        }

        return Storage::disk('public')->download($path, 'events_import_template.xlsx');
    }



}
