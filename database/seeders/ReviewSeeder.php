<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar 5 usuários para associar reviews e comentários
        $users = User::factory(5)->create();

        // Criar 30 reviews distribuídos entre os usuários
        foreach ($users as $user) {
            $reviewsCount = fake()->numberBetween(4, 8);
            Review::factory($reviewsCount)
                ->for($user)
                ->create()
                ->each(function ($review) use ($users) {
                    // Criar 10 comentários em cada review
                    Comment::factory(10)
                        ->for($review)
                        ->for($users->random())
                        ->create();
                });
        }
    }
}
