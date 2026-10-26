<?php

use App\Models\Comment;
use App\Models\Review;
use App\Models\User;
use App\Services\GameApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

function mockCommentPageGameApi(): void
{
    test()->mock(GameApiService::class, function ($mock) {
        $mock->shouldReceive('getGame')->andReturn([[
            'platforms' => [],
            'first_release_date' => null,
            'summary' => null,
            'cover' => null,
        ]]);
        $mock->shouldReceive('isConfigured')->andReturn(false);
    });
}

function commentPageReview(User $author): Review
{
    return Review::factory()->for($author)->create();
}

it('shows visitors a sign-in prompt instead of the comment form', function () {
    mockCommentPageGameApi();
    $review = commentPageReview(User::factory()->create());

    $this->get('/reviews/'.$review->id)
        ->assertOk()
        ->assertSee(__('reviews.comment_login_prompt'))
        ->assertSee(__('reviews.comment_login_link'))
        ->assertDontSee('id="comment-form"', false);
});

it('guides signed-in users who have not verified their email', function () {
    mockCommentPageGameApi();
    $review = commentPageReview(User::factory()->create());
    $user = User::factory()->create(['email_verified_at' => null, 'theme_preference' => 'system']);

    $this->actingAs($user)->get('/reviews/'.$review->id)
        ->assertOk()
        ->assertSee(__('reviews.comment_verify_prompt'))
        ->assertSee(route('verification.notice'))
        ->assertDontSee('id="comment-form"', false);
});

it('shows a labeled form and character counter to verified users', function () {
    mockCommentPageGameApi();
    $review = commentPageReview(User::factory()->create());
    $user = User::factory()->create(['email_verified_at' => now(), 'theme_preference' => 'system']);

    $this->actingAs($user)->get('/reviews/'.$review->id)
        ->assertOk()
        ->assertSee('for="comment"', false)
        ->assertSee('id="comment-character-count"', false)
        ->assertSee('maxlength="2000"', false)
        ->assertSee(__('reviews.comment_keyboard_hint'))
        ->assertSee(__('reviews.comments_empty'));
});

it('shows reply and like controls to verified users and reply context in the thread', function () {
    mockCommentPageGameApi();
    $reviewOwner = User::factory()->create();
    $commenter = User::factory()->create(['email_verified_at' => now()]);
    $review = commentPageReview($reviewOwner);
    $parent = $reviewOwner->comments()->create([
        'body' => 'Original comment for a reply',
        'review_id' => $review->id,
    ]);
    $reply = $commenter->comments()->create([
        'body' => 'Reply shown in the conversation',
        'review_id' => $review->id,
    ]);
    $reply->replyTo()->associate($parent);
    $reply->save();

    $this->actingAs($commenter)->get('/reviews/'.$review->id)
        ->assertOk()
        ->assertSee('class="reply-comment', false)
        ->assertSee('class="comment-like', false)
        ->assertSee(__('reviews.comment_replying_to', ['nickname' => $reviewOwner->nickname]));
});

it('marks only the review author comment with the author badge', function () {
    mockCommentPageGameApi();
    $author = User::factory()->create();
    $otherUser = User::factory()->create();
    $review = commentPageReview($author);
    $author->comments()->create(['body' => 'Comment from review author', 'review_id' => $review->id]);
    $otherUser->comments()->create(['body' => 'Comment from another user', 'review_id' => $review->id]);

    $this->withSession(['locale' => 'pt_BR'])->get('/reviews/'.$review->id)
        ->assertOk()
        ->assertSee(__('reviews.review_author_badge'))
        ->assertSee('Comment from another user');
});

it('publishes a normal comment and persists its owner and review', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $review = commentPageReview($user);

    $this->actingAs($user)->postJson('/comments', [
        'comment' => 'A valid comment for this review',
        'review' => $review->id,
    ])->assertOk()
        ->assertJsonPath('comment.body', 'A valid comment for this review')
        ->assertJsonPath('comment.user.id', $user->id);

    $this->assertDatabaseHas('comments', [
        'body' => 'A valid comment for this review',
        'review_id' => $review->id,
        'user_id' => $user->id,
        'reply_to_id' => null,
    ]);
});

it('returns JSON validation errors for comments outside the allowed length', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $review = commentPageReview($user);

    foreach (['four', str_repeat('a', 2001)] as $body) {
        $this->actingAs($user)->postJson('/comments', [
            'comment' => $body,
            'review' => $review->id,
        ])->assertUnprocessable()->assertJsonValidationErrors('comment');
    }

    expect(Comment::count())->toBe(0);
});

it('requires authentication and verified email to publish comments', function () {
    $review = commentPageReview(User::factory()->create());
    $payload = ['comment' => 'A valid comment body', 'review' => $review->id];

    $this->postJson('/comments', $payload)->assertUnauthorized();

    $unverified = User::factory()->create(['email_verified_at' => null]);
    $this->actingAs($unverified)->postJson('/comments', $payload)->assertForbidden();

    expect(Comment::count())->toBe(0);
});

it('rejects invalid and cross-review reply references', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $review = commentPageReview($user);
    $otherReview = Review::factory()->create();
    $otherReviewComment = $otherReview->user->comments()->create([
        'body' => 'This comment belongs to a different review',
        'review_id' => $otherReview->id,
    ]);

    foreach ([999999, $otherReviewComment->id] as $replyTo) {
        $this->actingAs($user)->postJson('/comments', [
            'comment' => 'A valid comment body',
            'review' => $review->id,
            'reply_to' => $replyTo,
        ])->assertUnprocessable()->assertJsonValidationErrors('reply_to');
    }

    expect(Comment::count())->toBe(1);
});

it('allows a reply reference only within the same review', function () {
    $owner = User::factory()->create(['email_verified_at' => now()]);
    $review = commentPageReview($owner);
    $parent = $owner->comments()->create([
        'body' => 'Original comment on this review',
        'review_id' => $review->id,
    ]);
    $replier = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($replier)->postJson('/comments', [
        'comment' => 'A valid reply on the same review',
        'review' => $review->id,
        'reply_to' => $parent->id,
    ])->assertOk()
        ->assertJsonPath('comment.reply_to.id', $parent->id)
        ->assertJsonPath('comment.reply_to.user.nickname', $owner->nickname);

    $this->assertDatabaseHas('comments', [
        'body' => 'A valid reply on the same review',
        'review_id' => $review->id,
        'user_id' => $replier->id,
        'reply_to_id' => $parent->id,
    ]);
});

it('stores comment likes once and allows the owner to unlike them', function () {
    $owner = User::factory()->create(['email_verified_at' => now()]);
    $review = commentPageReview($owner);
    $comment = $owner->comments()->create([
        'body' => 'Comment that can receive likes',
        'review_id' => $review->id,
    ]);
    $liker = User::factory()->create(['email_verified_at' => now()]);

    $this->putJson('/comments/'.$comment->id.'/like')->assertUnauthorized();
    $unverified = User::factory()->create(['email_verified_at' => null]);
    $this->actingAs($unverified)->putJson('/comments/'.$comment->id.'/like')->assertForbidden();
    expect(DB::table('comment_likes')->count())->toBe(0);

    $this->actingAs($liker)
        ->putJson('/comments/'.$comment->id.'/like')
        ->assertOk()
        ->assertJsonPath('likes_count', 1)
        ->assertJsonPath('liked_by_current_user', true);

    $this->putJson('/comments/'.$comment->id.'/like')
        ->assertOk()
        ->assertJsonPath('likes_count', 1);
    $this->assertDatabaseHas('comment_likes', ['comment_id' => $comment->id, 'user_id' => $liker->id]);

    $this->getJson(route('reviews.comments', $review))
        ->assertOk()
        ->assertJsonPath('comments.0.likes_count', 1)
        ->assertJsonPath('comments.0.liked_by_current_user', true);

    $this->deleteJson('/comments/'.$comment->id.'/like')
        ->assertOk()
        ->assertJsonPath('likes_count', 0)
        ->assertJsonPath('liked_by_current_user', false);
    $this->deleteJson('/comments/'.$comment->id.'/like')->assertOk()->assertJsonPath('likes_count', 0);
    $this->assertDatabaseMissing('comment_likes', ['comment_id' => $comment->id, 'user_id' => $liker->id]);
});

it('renders inline editing only for the comment owner', function () {
    mockCommentPageGameApi();
    $reviewOwner = User::factory()->create();
    $commentOwner = User::factory()->create();
    $otherUser = User::factory()->create();
    $review = commentPageReview($reviewOwner);
    $commentOwner->comments()->create(['body' => 'Owned comment text', 'review_id' => $review->id]);

    $this->actingAs($commentOwner)->get('/reviews/'.$review->id)
        ->assertOk()
        ->assertSee('class="edit-comment', false)
        ->assertSee('class="comment-edit-form', false);

    $this->actingAs($otherUser)->get('/reviews/'.$review->id)
        ->assertOk()
        ->assertDontSee('class="edit-comment', false);
});

it('returns stable 15-comment pages with sorting metadata and no private user fields', function () {
    $author = User::factory()->create();
    $review = commentPageReview($author);
    $commenter = User::factory()->create();
    $items = collect(range(1, 16))->map(fn ($number) => $commenter->comments()->create([
        'body' => "Comment number {$number}",
        'review_id' => $review->id,
    ]));

    $this->getJson(route('reviews.comments', ['review' => $review->id, 'sort' => 'oldest', 'offset' => 0]))
        ->assertOk()
        ->assertJsonPath('total', 16)
        ->assertJsonPath('next_offset', 15)
        ->assertJsonPath('has_more', true)
        ->assertJsonPath('comments.0.id', $items->first()->id)
        ->assertJsonPath('comments.0.likes_count', 0)
        ->assertJsonPath('comments.0.liked_by_current_user', false)
        ->assertJsonMissingPath('comments.0.user.email');

    $this->getJson(route('reviews.comments', ['review' => $review->id, 'sort' => 'newest', 'offset' => 15]))
        ->assertOk()
        ->assertJsonPath('total', 16)
        ->assertJsonPath('next_offset', 16)
        ->assertJsonPath('has_more', false)
        ->assertJsonPath('comments.0.id', $items->first()->id);
});

it('allows only the comment owner to delete through the existing endpoint', function () {
    $owner = User::factory()->create(['email_verified_at' => now()]);
    $otherUser = User::factory()->create(['email_verified_at' => now()]);
    $review = commentPageReview($owner);
    $comment = $owner->comments()->create(['body' => 'Comment to delete securely', 'review_id' => $review->id]);

    $this->actingAs($otherUser)->deleteJson('/comments/'.$comment->id)->assertForbidden();
    $this->actingAs($owner)->deleteJson('/comments/'.$comment->id)->assertOk();

    $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
});

it('keeps the dynamic renderer text-only and guards duplicate submissions', function () {
    $script = file_get_contents(resource_path('js/comment-section.js'));

    expect($script)
        ->toContain('element.textContent = text;')
        ->toContain('comment-card group flex min-w-0')
        ->toContain('if (submitButton.disabled) return;')
        ->toContain("Accept: 'application/json'")
        ->toContain('form.reset()')
        ->toContain('reviewAuthorId')
        ->toContain("'PATCH'")
        ->toContain("method: wasLiked ? 'DELETE' : 'PUT'")
        ->toContain("reply_to: replyForm.dataset.id")
        ->toContain('function renderReplyForm(comment)')
        ->toContain('sortSelect.addEventListener');
});
