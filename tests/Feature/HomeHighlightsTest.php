<?php

use App\Actions\FetchReviews;
use App\Models\Comment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

function createHomeReview(User $author, array $attributes = []): Review
{
    return Review::factory()->for($author)->create($attributes);
}

function commentOnHomeReview(Review $review, User $author, array $attributes = []): Comment
{
    return Comment::factory()->for($review)->for($author)->create($attributes);
}

it('highlights reviews by recent comment count and excludes older comments', function () {
    $author = User::factory()->create();
    $commenter = User::factory()->create();
    $mostDiscussed = createHomeReview($author, ['title' => 'Most discussed']);
    $secondMostDiscussed = createHomeReview($author, ['title' => 'Second most discussed']);
    $oldDiscussion = createHomeReview($author, ['title' => 'Old discussion', 'views' => 1000]);

    foreach (range(1, 3) as $index) {
        commentOnHomeReview($mostDiscussed, $commenter);
    }
    commentOnHomeReview($secondMostDiscussed, $commenter);
    commentOnHomeReview($oldDiscussion, $commenter, ['created_at' => now()->subDays(16)]);

    $highlights = app(FetchReviews::class)->execute(Request::create('/'))['featuredReviews'];

    expect($highlights->modelKeys())->toBe([$mostDiscussed->id, $secondMostDiscussed->id]);

    $this->withSession(['locale' => 'pt_BR'])->get('/')
        ->assertOk()
        ->assertSee('Em conversa')
        ->assertSee('Most discussed')
        ->assertSee('Second most discussed');
});

it('falls back to the most viewed reviews when there are no recent comments', function () {
    $author = User::factory()->create();
    $commenter = User::factory()->create();
    $mostViewed = createHomeReview($author, ['title' => 'Most viewed', 'views' => 50]);
    $lessViewed = createHomeReview($author, ['title' => 'Less viewed', 'views' => 10]);
    $oldDiscussion = createHomeReview($author, ['title' => 'Old discussion', 'views' => 5]);
    commentOnHomeReview($oldDiscussion, $commenter, ['created_at' => now()->subDays(16)]);

    $this->withSession(['locale' => 'pt_BR'])->get('/')
        ->assertOk()
        ->assertSee('Em destaque por visualizações')
        ->assertSee('Most viewed')
        ->assertSee('Less viewed')
        ->assertDontSee('3 comentários recentes');

    $highlights = app(FetchReviews::class)->execute(Request::create('/'))['featuredReviews'];
    expect($highlights->modelKeys())->toBe([$mostViewed->id, $lessViewed->id, $oldDiscussion->id]);
});

it('hides community highlights when no reviews exist and keeps the empty state', function () {
    $this->withSession(['locale' => 'pt_BR'])->get('/')
        ->assertOk()
        ->assertDontSee('Em conversa')
        ->assertSee('Parece que está tudo tranquilo por aqui..')
        ->assertSee('Seja a primeira pessoa a compartilhar sua opinião sobre um jogo');
});

it('keeps highlights independent from the selected feed filter and places publishing after the feed', function () {
    $author = User::factory()->create();
    $commenter = User::factory()->create();
    $featuredReview = createHomeReview($author, ['title' => 'Featured regardless of filter', 'views' => 1]);
    commentOnHomeReview($featuredReview, $commenter);
    createHomeReview($author, ['title' => 'Highest views', 'views' => 100, 'rating' => 4]);

    $response = $this->withSession(['locale' => 'pt_BR'])->get('/popular')->assertOk();
    $content = $response->getContent();

    expect(strpos($content, 'Featured regardless of filter'))
        ->toBeLessThan(strpos($content, 'Highest views'))
        ->and(strpos($content, 'Highest views'))
        ->toBeLessThan(strrpos($content, 'Escreva sua avaliação!'));
});
