<?php

namespace Database\Factories;

use App\Models\ContentType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Content>
 */
class ContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            //'description' => $this->faker->text(),
            'content_type_id' => random_int(1, 5),
            'is_common' => true
        ];
    }
}
