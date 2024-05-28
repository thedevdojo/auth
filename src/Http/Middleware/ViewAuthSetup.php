<?php

namespace Devdojo\Auth\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Gate;

class ViewAuthSetup
{
    public function handle($request, Closure $next)
    {
        if (! app()->isLocal() && ! Gate::allows('viewAuthSetup')) {
            return redirect('auth/login');
        }

        if (app()->isLocal() || Gate::allows('viewAuthSetup')) {
            return $next($request);
        }

        abort(403);
    }
}
