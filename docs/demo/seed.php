<?php

// Fixtures used only by record.mjs, never by the application's normal seeder.
declare(strict_types=1);
use App\Models\User;
use App\Notifications\CommentOnReviewNotification;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

require dirname(__DIR__, 2).'/vendor/autoload.php';
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$temporaryDirectory = getenv('PIXELRANK_DEMO_DIR');
$database = config('database.connections.sqlite.database');
if (config('database.default') !== 'sqlite'
    || ! $temporaryDirectory
    || realpath(dirname($database)) !== realpath($temporaryDirectory)
    || basename($database) !== 'demo.sqlite') {
    throw new RuntimeException('Recording requires its own temporary SQLite database.');
}

Artisan::call('migrate', ['--force' => true]);
$input = json_decode(stream_get_contents(STDIN), true, flags: JSON_THROW_ON_ERROR);
$users = [];
foreach ([['Marina Costa', 'marinaplays'], ['Rafael Lima', 'rafalima'], ['Lia Santos', 'liagames']] as $index => [$name, $nickname]) {
    $users[] = User::factory()->create([
        'name' => $name,
        'nickname' => $nickname,
        'email' => $index === 0 ? 'marina@example.test' : 'player'.$index.'@example.test',
        'password' => Hash::make($input['password']),
        'bio' => 'Jogos, boas histórias e descobertas para compartilhar.',
        'theme_preference' => 'dark',
        'hide_spoilers_default' => false,
    ]);
}

$games = [
    [119133, 'Elden Ring', 'co4jni', 'Uma jornada que recompensa a curiosidade', 9.5, 'essential'],
    [7346, 'The Legend of Zelda: Breath of the Wild', 'co3p2d', 'A liberdade de descobrir o próprio caminho', 10, 'essential'],
    [1942, 'The Witcher 3: Wild Hunt', 'co1wyy', 'Histórias que continuam depois dos créditos', 9, 'recommended'],
    [25076, 'Red Dead Redemption 2', 'co1q1f', 'Um mundo para explorar sem pressa', 9.5, 'essential'],
    [1877, 'Cyberpunk 2077', 'co7497', 'Night City entre luzes, escolhas e consequências', 8.5, 'recommended'],
    [26192, 'Hollow Knight', 'co1rgi', 'Um pequeno cavaleiro, uma grande descoberta', 9, 'recommended'],
    [113112, 'Hades', 'co39vc', 'Só mais uma tentativa de fugir do submundo', 9.5, 'essential'],
    [151665, 'Resident Evil 4', 'co6bo0', 'Uma releitura que encontra seu próprio ritmo', 8.5, 'mixed'],
];

$body = "Explorar este mundo foi uma experiência marcada por descobertas. O jogo confia na curiosidade de quem joga e faz cada novo caminho parecer uma escolha pessoal. A direção de arte e a trilha sonora dão personalidade aos cenários, enquanto os desafios incentivam a experimentar novas estratégias.\n\nO ritmo funciona melhor quando há tempo para observar os detalhes. Nem todos os encontros têm o mesmo equilíbrio, mas os melhores momentos compensam as pequenas repetições. Para quem gosta de aprender com cada tentativa e compartilhar descobertas com a comunidade, é uma jornada que vale conhecer.";
$reviews = [];
foreach ($games as $index => [$gameId, $gameName, $cover, $title, $rating, $recommendation]) {
    $review = $users[$index % 3]->reviews()->create([
        'game_id' => $gameId, 'game_name' => $gameName, 'game_cover' => $cover,
        'title' => $title, 'body' => $body, 'rating' => $rating,
        'recommendation' => $recommendation, 'contains_spoilers' => $index === 7,
        'platform_playable' => 'PC',
    ]);
    $review->forceFill(['views' => 180 + $index * 37, 'created_at' => now()->subDays($index + 1)])->save();
    $review->pros_cons()->createMany([
        ['type' => 'pros', 'content' => 'Exploração cheia de descobertas'],
        ['type' => 'pros', 'content' => 'Direção de arte e trilha sonora marcantes'],
        ['type' => 'cons', 'content' => 'Alguns desafios têm dificuldade irregular'],
    ]);
    $reviews[] = $review;
    for ($commentIndex = 0; $commentIndex < max(1, 6 - $index); $commentIndex++) {
        $comment = $review->comments()->make([
            'body' => [
                'Também gostei de explorar sem seguir uma lista de tarefas. Sempre aparecia algo interessante pelo caminho!',
                'A trilha sonora muda completamente o clima de cada região. Foi um dos meus pontos favoritos.',
                'Ótima análise! Voltei para descobrir lugares que tinham passado despercebidos na primeira vez.',
            ][$commentIndex % 3],
        ]);
        $comment->user()->associate($users[($commentIndex + 1) % 3]);
        $comment->save();
        $comment->likes()->attach($users[($commentIndex + 2) % 3]->id);
    }
}
$parent = $reviews[0]->comments()->first();
$reply = $reviews[0]->comments()->make(['body' => 'Concordo! A melhor parte foi encontrar caminhos diferentes e comparar as descobertas depois.']);
$reply->user()->associate($users[0]);
$reply->replyTo()->associate($parent);
$reply->save();
$users[0]->notify(new CommentOnReviewNotification($reviews[0], $parent));

echo json_encode(['userId' => $users[0]->id, 'reviewId' => $reviews[0]->id, 'parentId' => $parent->id], JSON_THROW_ON_ERROR);
