<?php

namespace Devdojo\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class LogoutController
{
    public function __invoke()
    {
        Auth::logout();
        $this->redirectUserAfterLogout();
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $this->redirectUserAfterLogout();
    }

    private function redirectUserAfterLogout() : RedirectResponse
    {
        return redirect()->route('home');
    }
}
