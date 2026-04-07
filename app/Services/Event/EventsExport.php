<?php

namespace App\Services\Event;

use App\Models\ContentAttribute;
use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EventsExport implements FromQuery, WithMapping, WithHeadings
{
    protected $attributes;

    public function __construct()
    {
        $this->attributes = ContentAttribute::all();
    }

    public function query(): Builder
    {
        return Event::with(['content.contentType', 'content.contentAttributeValues']);
    }

    public function headings(): array
    {
        $staticHeaders = ['Тип', 'Название', 'Оценка', 'Отзыв', 'Дата завершения'];

        $dynamicHeaders = $this->attributes->pluck('slug')->toArray();

        return array_merge($staticHeaders, $dynamicHeaders);
    }

    public function map($event): array
    {
        $row = [
            $event->content->contentType->name,
            $event->content->name,
            $event->rating,
            $event->review,
            $event->completed_at?->format('d.m.Y H:i'),
        ];

        $values = $event->content->contentAttributeValues->keyBy('content_attribute_id');

        foreach ($this->attributes as $attribute) {
            $row[] = $values->has($attribute->id) ? $values->get($attribute->id)->value : '';
        }

        return $row;
    }

}
