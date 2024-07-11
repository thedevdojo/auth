<?php

namespace Devdojo\Auth\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController
{
    public function __invoke(Request $request): RedirectResponse
    {
        Auth::logout();

        $this->clearTraces($request);

        return redirect()->route('home');
    }

    public function getLogout(Request $request)
    {
        Auth::logout();

        $this->clearTraces($request);

        return redirect('/');
    }

    private function clearTraces(Request $request): void
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
