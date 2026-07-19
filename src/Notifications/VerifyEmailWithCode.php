<?php

namespace Devdojo\Auth\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * The package's default verification email: a large 6-digit code for the
 * inline flow plus the classic signed link for one-click verification.
 */
class VerifyEmailWithCode extends VerifyEmail
{
    public function __construct(public string $code) {}

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Your verification code: :code', ['code' => $this->code]))
            ->line(__('Enter this code to verify your email address:'))
            ->line('**'.$this->code.'**')
            ->line(__('This code expires in :minutes minutes.', ['minutes' => (int) config('devdojo.auth.settings.verification_code_expires_in', 15)]))
            ->action(__('Or verify with one click'), $this->verificationUrl($notifiable));
    }
}
