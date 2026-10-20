<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    protected function buildMailMessage($url): MailMessage
    {
        $expiration = config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);

        return (new MailMessage())
            ->subject(__('mail.reset_subject'))
            ->line(__('mail.reset_line'))
            ->action(__('mail.reset_action'), $url)
            ->line(__('mail.reset_expiration', ['count' => $expiration]))
            ->line(__('mail.reset_fallback'));
    }
}
