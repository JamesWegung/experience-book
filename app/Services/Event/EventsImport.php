<?php

namespace App\Services\Event;

use App\Models\Content;
use App\Models\ContentAttribute;
use App\Models\ContentType;
use App\Models\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class EventsImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, SkipsOnFailure, WithMapping
{
    use Importable;

    public array $failures = [];
    protected Collection $contentTypes;
    protected Collection $attributeMap;

    public function __construct()
    {
        $this->contentTypes = ContentType::all()->keyBy('name');

        $this->attributeMap = ContentAttribute::all()->mapWithKeys(function ($attr) {
            return [str($attr->name)->slug('_')->toString() => $attr->slug];
        });
    }

    public function model(array $row): ?Model
    {
        $contentType = $this->contentTypes->get($row['type']);

        return DB::transaction(function () use ($row, $contentType) {
            $content = Content::firstOrCreate(
                [
                    'name' => $row['content_name'],
                    'content_type_id' => $contentType->id
                ]
            );

            return new Event([
                'user_id' => auth()->id(),
                'content_id' => $content->id,
                'rating' => $row['rating'] ?? 0,
                'review' => $row['review'] ?? null,
                'completed_at' => now(),
            ]);
        });
    }

    public function map($row): array
    {
        $mappedRow = [];

        $staticMap = [
            'tip' => 'type',
            'nazvanie' => 'content_name',
            'ocenka' => 'rating',
            'otzyv' => 'review',
            'avtor' => 'author',
        ];

        foreach ($row as $key => $value) {
            if (isset($staticMap[$key])) {
                $mappedRow[$staticMap[$key]] = $value;
                continue;
            }

            if ($this->attributeMap->has($key)) {
                $realSlug = $this->attributeMap->get($key);
                $mappedRow[$realSlug] = $value;
            } else {
                $mappedRow[$key] = $value;
            }
        }

        return $mappedRow;
    }


    public function rules(): array
    {
        return [
            'type' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!$this->contentTypes->has($value)) {
                        $fail("Тип контента '$value' не существует.");
                    }
                },
                ],
            'content_name' => 'required|string|max:255',
            'rating' => 'nullable|numeric|min:0|max:10',
            'review' => 'nullable|string',
            'author' => 'nullable|string',
        ];
    }


    public function onFailure(Failure ...$failures): void
    {
        $this->failures = array_merge($this->failures, $failures);
    }


    public function batchSize(): int
    {
        return 100;
    }

    protected function saveContentAttributesValues(Content $content, ContentType $contentType, array $row): void
    {
        foreach ($contentType->contentAttributes as $attribute) {
            $rusName = mb_strtolower($attribute->name);
            $excelKey = str($rusName)->slug('_')->toString();

            if (!empty($row[$excelKey])) {
                $content->contentAttributeValues()->updateOrCreate(
                    [
                        'content_id' => $content->id,
                        'content_attribute_id' => $attribute->id
                    ],
                    ['value' => $row[$excelKey]]
                );
            }
        }
    }
}
