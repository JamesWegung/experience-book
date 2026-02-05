<?php

namespace App\Imports;

use App\Models\Content;
use App\Models\ContentType;
use App\Models\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class EventsImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, SkipsOnFailure
{
    use Importable;

    public array $failures = [];

    public function model(array $row): ?Model
    {
        return DB::transaction(function () use ($row) {
            $contentType = ContentType::where('slug', $row['type'])->first();

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
            ]);
        });
    }


    public function rules(): array
    {
        return [
            'type' => 'required|exists:content_types,slug',
            'content_name' => 'required|string|max:255',
            'rating' => 'nullable|numeric|min:0|max:10',
            'review' => 'nullable|string',
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
}
