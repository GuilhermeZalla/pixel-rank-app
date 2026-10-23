<?php

namespace Database\Factories;

use App\Models\Review;
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
        $games = [
            'The Legend of Zelda: Breath of the Wild',
            'Elden Ring',
            'God of War Ragnarök',
            'Red Dead Redemption 2',
            'Cyberpunk 2077',
            'Horizon Forbidden West',
            "Marvel's Spider-Man 2",
            'Final Fantasy XVI',
            'Resident Evil 4',
            'Starfield',
        ];

        $gameName = $games[array_rand($games)];

        return [
            'title' => "Review: {$gameName}",
            'body' => fake()->sentence(100),
            'rating' => fake()->randomFloat(1, 1, 10),
            'recommendation' => fake()->randomElement(['essential', 'recommended', 'mixed', 'not_recommended']),
            'contains_spoilers' => fake()->boolean(),
            'user_id' => 1,
            'game_id' => 1,
            'game_name' => $gameName,
            'game_cover' => null,
            'platform_playable' => 'PC',
        ];
    }
}
