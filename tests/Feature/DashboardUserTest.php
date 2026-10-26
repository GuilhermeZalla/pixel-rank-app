<?php

declare(strict_types=1);

use App\Models\Comment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

it('redirects visitors trying to access dashboard tabs menu3 or menu4', function () {
    $user = User::factory()->create();

    $this->get('/users/'.$user->id.'/menu3/edit')->assertRedirect(route('login'));
    $this->get('/users/'.$user->id.'/menu4/edit')->assertRedirect(route('login'));
});

it('forbids other users from accessing another user dashboard tabs menu3 or menu4', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($other)->get('/users/'.$owner->id.'/menu3/edit')->assertForbidden();
    $this->actingAs($other)->get('/users/'.$owner->id.'/menu4/edit')->assertForbidden();
});

it('allows the owner to access dashboard tabs menu3 and menu4', function () {
    $owner = User::factory()->create(['email_verified_at' => now(), 'bio' => null, 'avatar' => null]);

    $this->actingAs($owner)
        ->get('/users/'.$owner->id.'/menu3/edit')
        ->assertOk()
        ->assertSee("activeMenu: 'menu3'", false);

    $this->actingAs($owner)
        ->get('/users/'.$owner->id.'/menu4/edit')
        ->assertOk()
        ->assertSee("activeMenu: 'menu4'", false);
});

it('renders reviews and comments when data is present', function () {
    $owner = User::factory()->create(['email_verified_at' => now(), 'bio' => null, 'avatar' => null]);
    $review = Review::factory()->for($owner)->create(['title' => 'My Great Review Title']);
    $owner->comments()->create(['body' => 'My Great Comment Body', 'review_id' => $review->id]);

    $this->actingAs($owner)
        ->get('/users/'.$owner->id.'/menu2/edit')
        ->assertOk()
        ->assertSee('My Great Review Title');

    $this->actingAs($owner)
        ->get('/users/'.$owner->id.'/menu3/edit')
        ->assertOk()
        ->assertSee('My Great Comment Body');
});

it('renders empty states when the user has no reviews or comments', function () {
    $owner = User::factory()->create(['email_verified_at' => now(), 'bio' => null, 'avatar' => null]);

    $this->actingAs($owner)
        ->get('/users/'.$owner->id.'/menu2/edit')
        ->assertOk()
        ->assertSee(__('dashboard.no_reviews'));

    $this->actingAs($owner)
        ->get('/users/'.$owner->id.'/menu3/edit')
        ->assertOk()
        ->assertSee(__('dashboard.no_comments'));
});

it('renders a comment with a null review without throwing an error', function () {
    $owner = User::factory()->create(['email_verified_at' => now(), 'bio' => null, 'avatar' => null]);

    $comment = new Comment;
    $comment->id = 99;
    $comment->body = 'Comment on detached review';
    $comment->user_id = $owner->id;
    $comment->created_at = now();
    $comment->setRelation('user', $owner);
    $comment->setRelation('review', null);

    $rendered = (string) $this->blade('<x-comment :comment="$comment" :dashboard="true" />', [
        'comment' => $comment,
    ]);

    expect($rendered)
        ->toContain(__('dashboard.review_removed'))
        ->toContain('Comment on detached review')
        ->not->toContain('/reviews/');
});

it('rejects an avatar upload larger than 1MB', function () {
    $owner = User::factory()->create(['email_verified_at' => now()]);
    $largeAvatar = UploadedFile::fake()->create('avatar.jpg', 1500, 'image/jpeg');

    $this->actingAs($owner)
        ->put('/users/'.$owner->id, [
            'name' => $owner->name,
            'nickname' => $owner->nickname,
            'email' => $owner->email,
            'avatar' => $largeAvatar,
        ])
        ->assertSessionHasErrors('avatar');
});

it('rejects an avatar upload with an invalid format', function () {
    $owner = User::factory()->create(['email_verified_at' => now()]);
    $invalidAvatar = UploadedFile::fake()->create('document.pdf', 200, 'application/pdf');

    $this->actingAs($owner)
        ->put('/users/'.$owner->id, [
            'name' => $owner->name,
            'nickname' => $owner->nickname,
            'email' => $owner->email,
            'avatar' => $invalidAvatar,
        ])
        ->assertSessionHasErrors('avatar');
});
