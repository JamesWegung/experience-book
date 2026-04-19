<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\StoreEventRequest;
use App\Models\ContentAttributeValue;
use App\Models\Content;
use App\Models\ContentType;
use App\Models\Event;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index(Request $request): Factory|View
    {
        $events = \auth()->user()
            ->events()
            ->with([
                'content.contentType',
                'content.contentAttributeValues.contentAttribute',
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

                $contentAttributes = $request->input('custom_attributes', []);

                foreach ($contentAttributes as $attributeId => $value) {
                    if (!is_null($value) && $value !== '') {
                        ContentAttributeValue::updateOrCreate([
                            'content_id' => $content->id,
                            'content_attribute_id' => $attributeId,
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
        $event = Event::with([
            'content.contentType',
            'content.contentAttributeValues.contentAttribute',
        ])->findOrFail($id);

        $contentTypeOptions = ContentType::take(10)->get(['id as value', 'name']);

        $availableAttributes = $event->content->contentType->contentAttributes;
        $currentValues = $event->content->contentAttributeValues->keyBy('content_attribute_id');

        return view('events.edit', compact('event', 'contentTypeOptions', 'availableAttributes', 'currentValues'));
    }

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

                $contentAttributes = $request->input('custom_attributes', []);

                foreach ($contentAttributes as $attributeId => $value) {
                    if (!is_null($value) && $value !== '') {
                        ContentAttributeValue::updateOrCreate(
                            [
                                'content_id'   => $content->id,
                                'content_attribute_id' => $attributeId,
                            ],
                            ['value' => $value]
                        );
                    } else {
                        ContentAttributeValue::where('content_id', $content->id)
                            ->where('content_attribute_id', $attributeId)
                            ->delete();
                    }
                }
            });

            return redirect()->route('events.index');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Ошибка при создании записи: ' . $e->getMessage()]);
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        $event = Event::find($id);

        if ($event){
            $event->delete();
        }

        return redirect()->route('events.index');
    }

}
