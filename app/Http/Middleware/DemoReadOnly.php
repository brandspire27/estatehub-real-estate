<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoReadOnly
{
    /**
     * Prevent writes while the app is running as a public demo.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            ! config('app.demo_read_only')
            || $request->isMethodSafe()
            || ($request->is('login') && $request->isMethod('POST'))
            || $request->routeIs('logout')
        ) {
            return $next($request);
        }

        abort(Response::HTTP_FORBIDDEN, 'This public demo is read-only.');
    }
}
