<?php

use Devdojo\Auth\Livewire\Setup\Background;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    config()->set('devdojo.auth.appearance.background', [
        'color' => '#ffffff',
        'image' => '',
        'image_overlay_color' => '#000000',
        'image_overlay_opacity' => '0.5',
    ]);
});

it('loads initial background settings from config', function () {
    Livewire::test(Background::class)
        ->assertSet('color', '#ffffff')
        ->assertSet('image', '')
        ->assertSet('image_overlay_color', '#000000')
        ->assertSet('image_overlay_opacity', 50);
});

it('updates background color and triggers config update', function () {
    Livewire::test(Background::class)
        ->set('color', '#ff0000')
        ->assertSet('color', '#ff0000');

    expect(config('devdojo.auth.appearance.background.color'))->toBe('#ff0000');
});

it('handles background image upload and storage', function () {
    $file = UploadedFile::fake()->image('background.jpg');

    Livewire::test(Background::class)
        ->set('image', $file)
        ->assertSet('image', '/storage/auth/background.jpg');

    Storage::disk('public')->assertExists('auth/background.jpg');
    expect(config('devdojo.auth.appearance.background.image'))->toBe('/storage/auth/background.jpg');
});

it('updates image overlay opacity and converts to decimal', function () {
    Livewire::test(Background::class)
        ->set('image_overlay_opacity', 75)
        ->assertSet('image_overlay_opacity', 75);

    expect(config('devdojo.auth.appearance.background.image_overlay_opacity'))->toBe('0.75');
});

it('updates image overlay color', function () {
    Livewire::test(Background::class)
        ->set('image_overlay_color', '#333333')
        ->assertSet('image_overlay_color', '#333333');

    expect(config('devdojo.auth.appearance.background.image_overlay_color'))->toBe('#333333');
});

it('handles different image file types during upload', function () {
    $file = UploadedFile::fake()->image('background.png');

    Livewire::test(Background::class)
        ->set('image', $file)
        ->assertSet('image', '/storage/auth/background.png');

    Storage::disk('public')->assertExists('auth/background.png');
});
