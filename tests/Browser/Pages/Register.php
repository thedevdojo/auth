<?php

namespace Devdojo\Auth\Tests\Browser\Pages;

use Laravel\Dusk\Page;
use Laravel\Dusk\Browser;
use Illuminate\Support\Facades\Config;
use Illuminate\Notifications\Messages\MailMessage;
use ProtoneMedia\LaravelDuskFakes\Mails\PersistentMails;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;
use Illuminate\Support\Facades\Mail;

class Register extends Page
{
    use PersistentMails;
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/auth/register';
    }

    public function registerAsJohnDoe(Browser $browser){
        $redirectExpectedToBe = '/';
        if(class_exists(\Devdojo\Genesis\Genesis::class)){
            $redirectExpectedToBe = '/dashboard';
        }
        $browser
            ->type('@email-input', 'johndoe@gmail.com')
            ->type('@password-input', 'password')
            ->clickAndWaitForReload('@submit-button');
        
        return $browser;

    }

    public function assertUserReceivedEmail(){
        // Mail::fake();
        $user = \App\Models\User::where('email', 'johndoe@gmail.com')->first();
        Mail::assertSent(MailMessage::class, function($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }
}
