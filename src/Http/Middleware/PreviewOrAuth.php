<?php

namespace Devdojo\Auth\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreviewOrAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     *
     * @throws AuthenticationException
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->isLocal() && $request->boolean('preview')) {
            return $next($request);
        }

        return app(Authenticate::class)->handle($request, $next);
    }
}
