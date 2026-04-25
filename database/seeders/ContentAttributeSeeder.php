<?php

namespace Database\Seeders;

use App\Models\ContentAttribute;
use Illuminate\Database\Seeder;

class ContentAttributeSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'name' => 'Автор',
                'slug' => 'author',
                'content_type_id' => 1, // для книг
            ],
        ];

        ContentAttribute::insert($data);
    }
}
