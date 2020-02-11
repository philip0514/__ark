<?php

namespace Philip0514\Ark\Middleware;

use Closure;

class Url
{
    public function handle($request, Closure $next)
    {
        session()->put( config('ark.session_url'), url()->full());

        return $next($request);
    }
}
