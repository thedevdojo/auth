<?php

namespace Devdojo\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class LogoutController
{
    public function __invoke(): RedirectResponse
    {
        Auth::logout();
        return redirect()->route('home');
    }

}
