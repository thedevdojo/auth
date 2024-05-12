<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    // Ensure each test starts with a clean slate
    User::query()->delete();
});

test('Two factor challenge page redirects to login for guest user', function(){
    $this->get('auth/two-factor-challenge')
        ->assertRedirect('auth/login');
});

test('Two factor challenge page redirects if user is logged in and they don\'t have the login.id session', function(){
    withANewUser()->get('auth/two-factor-challenge')
        ->assertRedirect('auth/login');
});

test('when user logs in and two factor auth is active, they will have the login.id session created', function(){
    $user = User::factory()->create(['name' => 'Homer Simpson', 'email' => 'homer@springfield.com', 'password' => \Hash::make('DuffBeer123')]);
    // $this->get('auth/login')
    //     ->seeInField('email', 'homer@springfield.com')
    //     ->click('.auth-component-button')
    //     ->assertSee('Password');
    // dd($user->two_factor_secret);
    //dd(\Schema::getColumnListing('users'));
    //dd(env('DB_CONNECTION'));
    //dd($user);
})->todo();

it('user can view two factor challenge page after they login', function(){

    // Livewire::test('auth.register')
    //     ->set('email', 'user@example.com')
    //     ->set('password', 'secret1234')
    //     ->set('name', 'John Doe')
    //     ->call('register')

    withANewUser()->get('auth/two-factor-challenge')->asertOK();
    // $user = loginAsUser(null);
    // Livewire::test('auth.two-factor-challenge');
        // ->assertSee('When you enabled 2FA');
})->todo();

test('when authenticated, user can view /user/two-factor-authentication page', function(){
    
})->todo();

test('when authenticated, user can view /user/two-factor-authentication page and they can click enable and add auth code', function(){
    
})->todo();

// scenarios when 2FA is disabled by application admin
test('if two factor auth is disabled, user can login with name and password and they will not be redirected to 2fa page, even if they have the correct two_factor table columns filled', function(){
    
})->todo();
