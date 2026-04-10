<?php

namespace Tests\Feature\Events;

use App\Models\Content;
use App\Models\ContentAttribute;
use App\Models\ContentAttributeValue;
use App\Models\ContentType;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExcelExportTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_download_events_export()
    {
        $user = User::factory()->create();
        $type = ContentType::create(['name' => 'Книга']);
        $contentAttribute = ContentAttribute::create([
            'name' => 'Автор',
            'slug' => 'author',
            'content_type_id' => $type->id, // для книг
        ]);


        $content = Content::create([
            'name' => 'Денискины рассказы',
            'content_type_id' => $type->id,
        ]);

        ContentAttributeValue::insert([
            'content_id' => $content->id,
            'content_attribute_id' => $contentAttribute->id,
            'value' => 'Автор книги',
        ]);

        Event::factory()->insert([
            'user_id' => $user->id,
            'content_id' => $content->id,
            'rating' => 9,
            'review' => 'Review text',
        ]);

        $response = $this->actingAs($user)->get('events/export-by-xlsx');


        $response->assertStatus(200);

        $response->assertHeader('content-disposition', 'attachment; filename=events_' . now()->format('Y-m-d') . '.xlsx');

        $this->assertEquals(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('Content-Type')
        );

    }
}
