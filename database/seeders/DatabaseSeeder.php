<?php

namespace Database\Seeders;

use App\Models\ContentAttribute;
use App\Models\ContentType;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
        $this->call([ContentAttributeSeeder::class]);
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
