<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

beforeEach(function () {
    // Ensure each test starts with a clean slate
    User::query()->delete();
});

test('user can register, login, and logout', function () {
    $email = 'test@example.com';
    $password = 'password';

    // Test user registration
    $response = $this->post('/auth/register', [
        'email' => $email,
        'password' => Hash::make($password),
    ]);

    $response->assertRedirect('/'); // Assuming '/' is the homepage

    // Check if user is created
    $user = User::where('email', $email)->first();
    expect($user)->not->toBeNull();

    // Test logout
    Auth::login($user);
    $response = $this->post('/logout');
    $response->assertRedirect('/'); // Assuming '/' is the homepage after logout

    // Test login
    $response = $this->post('/auth/login', [
        'email' => $email,
        'password' => $password,
    ]);

    $response->assertRedirect('/'); // Assuming '/' is the homepage
});