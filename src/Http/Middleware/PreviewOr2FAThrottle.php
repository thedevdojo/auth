<?php

namespace Devdojo\Auth\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Routing\Pipeline;
use Symfony\Component\HttpFoundation\Response;

class PreviewOr2FAThrottle
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
        // Local preview skips the protections.
        if (app()->isLocal() && $request->boolean('preview')) {
            return $next($request);
        }

        // Otherwise execute the middleware you would have attached.
        return app(Pipeline::class)
            ->send($request)
            ->through([
                'two-factor-challenged',
                'throttle:5,1',
            ])
            ->then(fn ($request) => $next($request));
    }
}
