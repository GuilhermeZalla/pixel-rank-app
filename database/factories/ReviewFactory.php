<?php

namespace Database\Factories;

use App\Models\Review;
use App\ReviewRecommendation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->name(),
            'body' => fake()->sentence(100),
            'rating' => fake()->randomFloat(1, 1, 10),
            'recommendation' => ReviewRecommendation::class,
            'contains_spoilers' => fake()->boolean(),
            'user_id' => 1,
            'game_id' => 1
        ];
    }
}
