<?php

namespace Devdojo\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
    public function redirect(Request $request, $driver)
    {
        return Socialite::driver($driver)->redirect();
    }

    public function callback(Request $request, $driver)
    {
        $user = Socialite::driver($driver)->user();
        dd($user);
    }
}
