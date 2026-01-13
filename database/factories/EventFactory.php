<?php

namespace Database\Factories;

use App\Helpers\ProbabilityHelper;
use App\Models\Content;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $definition = [
            'content_id' => Content::factory(),
            'rating' => random_int(7, 10),
        ];

        if (ProbabilityHelper::chance(70)) {
            $definition['completed_at'] = $this->faker->dateTimeBetween('-1 year');
        }

        if (ProbabilityHelper::chance(50)) {
            $definition['review'] = $this->faker->sentence();
        }

        return $definition;
    }
}
