<?php

namespace Devdojo\Auth\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController
{
    /**
     * Handle logout via POST request (recommended).
     */
    public function __invoke(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(config('devdojo.auth.settings.redirect_after_logout') ?? '/');
    }

    /**
     * Handle logout via GET request (kept for backwards compatibility).
     *
     * @deprecated Use POST /auth/logout instead for better security.
     */
    public function getLogout(Request $request): RedirectResponse
    {
        return $this->__invoke($request);
    }
}
