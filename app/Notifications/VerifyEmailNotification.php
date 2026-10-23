<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail
{
    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage())
            ->markdown('vendor.mail.html.message')
            ->subject(__('mail.verification_subject'))
            ->line(__('mail.verification_line'))
            ->action(__('mail.verification_action'), $url)
            ->line(__('mail.verification_fallback'));
    }
}
