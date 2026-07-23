<?php

namespace Devdojo\Auth\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreviewOrGuest
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     *
     * @throws AuthenticationException
     * @throws \Exception
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! app()->isLocal() || ! $request->boolean('preview')) {
            return app(RedirectIfAuthenticated::class)->handle($request, $next);
        }

        return $next($request);
    }
}
