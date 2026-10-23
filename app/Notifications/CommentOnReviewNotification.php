<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CommentOnReviewNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Review $review,
        public Comment $comment,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'comment_on_review',
            'review_id' => $this->review->id,
            'review_title' => $this->review->title,
            'comment_id' => $this->comment->id,
            'comment_body' => $this->comment->body,
            'from_user_id' => $this->comment->user_id,
            'from_user_nickname' => $this->comment->user->nickname,
            'url' => '/reviews/'.$this->review->id,
        ];
    }
}
