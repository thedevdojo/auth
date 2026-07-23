<?php

use App\Models\User;
use Devdojo\Genesis\Genesis;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

function authSelector(string $selector): string
{
    if (str_starts_with($selector, '@')) {
        return '[data-auth="'.substr($selector, 1).'"]';
    }

    return $selector;
}

function createJohnDoe(): void
{
    $userModel = config('auth.providers.users.model');

    $userModel::factory()->create([
        'email' => 'johndoe@gmail.com',
        'password' => Hash::make('password'),
    ]);
}

function loginAsJohnDoe(): void
{
    test()->actingAs(User::where('email', 'johndoe@gmail.com')->first());
}

function enable2FAforJohnDoe(): void
{
    $johnDoe = User::where('email', 'johndoe@gmail.com')->first();
    $johnDoe->two_factor_confirmed_at = now();
    $johnDoe->save();
}

function expectedRedirectAfterAuthPath(): string
{
    return class_exists(Genesis::class) ? '/dashboard' : '/';
}

function assertRedirectAfterAuthUrlIsCorrect($page)
{
    return $page->assertPathIs(expectedRedirectAfterAuthPath());
}

function formLoginAsJohnDoe($page)
{
    return $page
        ->fill(authSelector('@email-input'), 'johndoe@gmail.com')
        ->click(authSelector('@submit-button'))
        ->fill(authSelector('@password-input'), 'password')
        ->click(authSelector('@submit-button'));
}

function registerAsJohnDoe($page)
{
    return $page
        ->fill(authSelector('@email-input'), 'johndoe@gmail.com')
        ->fill(authSelector('@password-input'), 'password')
        ->click(authSelector('@submit-button'));
}

function typeAndSubmit($page, ?string $selector, string $value)
{
    return $page
        ->fill(authSelector((string) $selector), $value)
        ->click(authSelector('@submit-button'));
}

function testValidationErrorOnSubmit($page, string $message = '')
{
    return $page
        ->click(authSelector('@submit-button'))
        ->assertSee($message);
}

function authAttributeChange($page, ?string $selector, string $attribute, string $value)
{
    $page->script("document.querySelector('$selector').setAttribute('$attribute', '$value');");

    return $page;
}

function authAttributeRemove($page, ?string $selector, string $attribute)
{
    $page->script("document.querySelector('$selector').removeAttribute('$attribute');");

    return $page;
}

function clearLogFile(): void
{
    file_put_contents(storage_path('logs/laravel.log'), '');
}

function getLogLineContaining(string $content, string $substring): ?string
{
    $lines = explode("\n", $content);

    $foundLine = current(array_filter($lines, fn ($line) => str_starts_with($line, $substring)));

    return $foundLine ?: null;
}

function setAuthConfig(string $key, mixed $value): void
{
    if (! isset($GLOBALS['auth_test_config_originals'])) {
        $GLOBALS['auth_test_config_originals'] = [];
    }

    if (! array_key_exists($key, $GLOBALS['auth_test_config_originals'])) {
        $GLOBALS['auth_test_config_originals'][$key] = config($key);
    }

    Config::set($key, $value);
}

function resetAuthConfig(): void
{
    foreach ($GLOBALS['auth_test_config_originals'] ?? [] as $key => $value) {
        Config::set($key, $value);
    }

    $GLOBALS['auth_test_config_originals'] = [];
}
