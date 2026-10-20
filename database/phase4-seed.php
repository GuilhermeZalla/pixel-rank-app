<?php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::factory()->create([
    'name' => 'Responsive Check',
    'nickname' => 'phase4check',
    'email' => 'phase4@example.test',
    'password' => Illuminate\Support\Facades\Hash::make('password'),
]);

App\Models\Review::factory()->create([
    'user_id' => $user->id,
    'title' => 'Responsive test review',
]);

echo "Sample data created.\n";
