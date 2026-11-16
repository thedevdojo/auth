<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    $this->user = User::factory()->create();
});

afterEach(function () {
    $this->user->delete();
});

it('logs out user via POST request', function () {
    $this->actingAs($this->user);
    expect(Auth::check())->toBeTrue();

    $response = $this->post('/auth/logout');

    $response->assertRedirect('/');
    expect(Auth::check())->toBeFalse();
});

it('logs out user via GET request', function () {
    $this->actingAs($this->user);
    expect(Auth::check())->toBeTrue();

    $response = $this->get('/auth/logout');

    $response->assertRedirect('/');
    expect(Auth::check())->toBeFalse();
});

it('redirects to configured URL after logout', function () {
    config()->set('devdojo.auth.settings.redirect_after_logout', '/goodbye');

    $this->actingAs($this->user);

    $response = $this->post('/auth/logout');

    $response->assertRedirect('/goodbye');
});

it('invalidates session after logout', function () {
    $this->actingAs($this->user);

    // Store something in session
    session()->put('test_key', 'test_value');
    expect(session()->get('test_key'))->toBe('test_value');

    $this->post('/auth/logout');

    // Session should be invalidated
    expect(session()->get('test_key'))->toBeNull();
});

it('both POST and GET logout use same redirect config', function () {
    config()->set('devdojo.auth.settings.redirect_after_logout', '/custom-redirect');

    // Test POST
    $this->actingAs($this->user);
    $postResponse = $this->post('/auth/logout');
    $postResponse->assertRedirect('/custom-redirect');

    // Test GET
    $this->actingAs($this->user);
    $getResponse = $this->get('/auth/logout');
    $getResponse->assertRedirect('/custom-redirect');
});

it('defaults to root URL when no logout redirect configured', function () {
    // Ensure config is not set
    config()->set('devdojo.auth.settings.redirect_after_logout', null);

    $this->actingAs($this->user);

    $response = $this->post('/auth/logout');

    $response->assertRedirect('/');
});
