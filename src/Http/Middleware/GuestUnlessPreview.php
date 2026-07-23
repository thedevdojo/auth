<?php

namespace Devdojo\Auth\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestUnlessPreview
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->boolean('preview') && app()->isLocal()) {
            return $next($request);
        }

        return app(RedirectIfAuthenticated::class)->handle($request, $next);
    }
}
