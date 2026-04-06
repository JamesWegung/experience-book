<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\Content;
use App\Models\ContentType;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([RoleSeeder::class]);
        $this->createBasicContentTypes();
        $this->createFirstAttributes();
        $this->createTestUser();
    }

    private function createBasicContentTypes(): void
    {
        $data = [
            [
                'name' => 'Книга',
                'slug' => 'book'
            ],
            [
                'name' => 'Фильм',
                'slug' => 'movie'
            ],
            [
                'name' => 'Сериал',
                'slug' => 'tv-series'
            ],
            [
                'name' => 'Игра на ПК',
                'slug' => 'pc-game'
            ],
            [
                'name' => 'Мероприятие',
                'slug' => 'event'
            ],
        ];

        ContentType::insert($data);
    }

    private function createFirstAttributes(): void
    {
        $data = [
            [
                'name' => 'Автор',
                'slug' => 'author',
                'content_type_id' => 1, // для книг
            ],
        ];

        Attribute::insert($data);
    }

    private function createTestUser(): void
    {
        $user = User::factory()->create([
            'name' => env('TEST_USER_LOGIN'),
            'email' => env('TEST_USER_EMAIL'),
            'password' => env('TEST_USER_PASSWORD')
        ]);

        $user->assignRole('admin');

        Event::factory(10)->for($user)->create();
    }
}
