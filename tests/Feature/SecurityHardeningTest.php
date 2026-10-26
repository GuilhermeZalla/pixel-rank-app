<?php

declare(strict_types=1);

use App\Models\Comment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\MassAssignmentException;

it('keeps Eloquent mass assignment guarded after the application boots', function () {
    expect(Model::isUnguarded())->toBeFalse();
});

it('allows intended user fields but ignores privilege and ownership fields', function () {
    $user = new User;
    $user->fill([
        'name' => 'Reviewer',
        'nickname' => 'pixel-reviewer',
        'email' => 'reviewer@example.test',
    ]);

    expect($user->name)->toBe('Reviewer')
        ->and($user->nickname)->toBe('pixel-reviewer')
        ->and($user->is_admin)->toBeNull()
        ->and($user->role)->toBeNull();

    expect(fn () => $user->fill(['is_admin' => true, 'role' => 'admin']))
        ->toThrow(MassAssignmentException::class);
});

it('does not allow a mass-assigned review or comment to choose its owner', function () {
    $review = new Review;
    $review->fill(['title' => 'A thoughtful review']);

    $comment = new Comment;
    $comment->fill(['body' => 'A useful comment', 'review_id' => 7]);

    expect(fn () => $review->fill(['user_id' => 42]))->toThrow(MassAssignmentException::class)
        ->and(fn () => $comment->fill(['user_id' => 42]))->toThrow(MassAssignmentException::class);

    expect($review->title)->toBe('A thoughtful review')
        ->and($review->user_id)->toBeNull()
        ->and($comment->body)->toBe('A useful comment')
        ->and($comment->user_id)->toBeNull();
});

it('renders comment data through text nodes instead of an HTML string', function () {
    $view = file_get_contents(resource_path('views/reviews/show.blade.php'));
    $script = file_get_contents(resource_path('js/comment-section.js'));

    expect($view)->not->toContain('insertAdjacentHTML')
        ->and($script)->toContain('element.textContent = text;')
        ->and($script)->not->toContain('.innerHTML');
});

it('serializes the dashboard menu value as JavaScript data', function () {
    $view = file_get_contents(resource_path('views/users/edit.blade.php'));

    expect($view)->toContain('activeMenu: @js($menu)')
        ->and($view)->not->toContain("activeMenu: '{{ ");
});
