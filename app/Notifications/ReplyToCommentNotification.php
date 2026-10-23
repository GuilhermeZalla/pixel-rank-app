<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReplyToCommentNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Comment $comment,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'reply_to_comment',
            'comment_id' => $this->comment->id,
            'comment_body' => $this->comment->body,
            'from_user_id' => $this->comment->user_id,
            'from_user_nickname' => $this->comment->user->nickname,
            'review_id' => $this->comment->review_id,
            'url' => '/reviews/'.$this->comment->review_id,
        ];
    }
}
