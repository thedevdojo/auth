<?php

use Devdojo\Auth\Livewire\Setup\Logo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    config()->set('devdojo.auth.appearance.logo', [
        'type' => 'text',
        'image_src' => '',
        'svg_string' => '',
        'height' => '40',
    ]);
});

it('loads initial logo settings from config', function () {
    Livewire::test(Logo::class)
        ->assertSet('logo_type', 'text')
        ->assertSet('logo_image_src', '')
        ->assertSet('logo_svg_string', '')
        ->assertSet('logo_height', '40')
        ->assertSet('logo_image', false);
});

it('updates logo type', function () {
    Livewire::test(Logo::class)
        ->set('logo_type', 'image')
        ->assertSet('logo_type', 'image');

    expect(config('devdojo.auth.appearance.logo.type'))->toBe('image');
});

it('handles logo image upload and storage', function () {
    $file = UploadedFile::fake()->image('logo.png');

    Livewire::test(Logo::class)
        ->set('logo_image', $file)
        ->assertSet('logo_image_src', '/storage/auth/logo.png');

    Storage::disk('public')->assertExists('auth/logo.png');
    expect(config('devdojo.auth.appearance.logo.image_src'))->toBe('/storage/auth/logo.png');
});

it('updates logo height', function () {
    Livewire::test(Logo::class)
        ->set('logo_height', '60')
        ->assertSet('logo_height', '60');

    expect(config('devdojo.auth.appearance.logo.height'))->toBe('60');
});

it('updates svg string', function () {
    $svgString = '<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="40"/></svg>';

    Livewire::test(Logo::class)
        ->call('updateSvg', $svgString);

    expect(config('devdojo.auth.appearance.logo.svg_string'))->toBe($svgString);
});

it('handles different image file types during upload', function () {
    $file = UploadedFile::fake()->image('logo.jpg');

    Livewire::test(Logo::class)
        ->set('logo_image', $file)
        ->assertSet('logo_image_src', '/storage/auth/logo.jpg');

    Storage::disk('public')->assertExists('auth/logo.jpg');
});

it('sets logo_image to true when logo_image_src exists on mount', function () {
    config()->set('devdojo.auth.appearance.logo.image_src', '/storage/auth/existing-logo.png');

    Livewire::test(Logo::class)
        ->assertSet('logo_image', true);
});
