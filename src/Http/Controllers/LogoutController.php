<?php

namespace Devdojo\Auth\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class LogoutController
{
    public function __invoke(): RedirectResponse
    {
        Auth::logout();

        return redirect()->route('home');
    }

    public function getLogout(){
        Auth::logout();
        Session()->flush();

        return redirect('/');
    }
}
