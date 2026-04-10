<?php

namespace Tests\Feature\Events;

use App\Models\ContentAttribute;
use App\Models\ContentType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExcelImportTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_imports_events_from_excel_file()
    {
        $user = User::factory()->create();

        ContentType::create(['name' => 'Книга']);
        ContentAttribute::insert([
            [
                'name' => 'Автор',
                'slug' => 'author',
                'content_type_id' => 1, // для книг
            ],
        ]);

        $filePath = base_path('tests/fixtures/events_to_import.xlsx');

        $file = new UploadedFile(
            $filePath,
            'events.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true // Важно: тестовый режим
        );


        $response = $this->actingAs($user)->post('events/import-by-xlsx', [
            'file' => $file,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('events', [
            'user_id' => $user->id,
            'rating' => 8,
        ]);

        $this->assertDatabaseHas('contents', [
            'name' => 'Тестовая книга',
        ]);
    }
}
