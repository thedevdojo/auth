<?php

it('loads providers from configuration on mount', function () {
    config()->set('devdojo.auth.providers', [
        'google' => ['name' => 'Google', 'active' => true],
        'facebook' => ['name' => 'Facebook', 'active' => false],
    ]);

    Livewire::test('auth.setup.providers')
        ->assertSet('providers.google.active', true)
        ->assertSet('providers.facebook.active', false);
});

it('updates provider activation correctly and clears config cache', function () {
    Livewire::test('auth.setup.providers')
        ->call('update', 'google', true)
        ->assertSet('providers.google.active', true);
    assert(config('devdojo.auth.providers.google.active') === true);

    Livewire::test('auth.setup.providers')
        ->call('update', 'google', false)
        ->assertSet('providers.google.active', false);
    assert(config('devdojo.auth.providers.google.active') === false);
});
