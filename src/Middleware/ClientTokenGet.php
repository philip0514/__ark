<?php

namespace Philip0514\Ark\Middleware;

use Philip0514\Ark\Services\ClientTokenService;
use Closure;
use Cookie;

class ClientTokenGet
{
    public function handle($request, Closure $next)
    {
    	if(Cookie::get('client_token') || Cookie::get('password_token')) return $next($request);
    	ClientTokenService::generate();
        return $next($request);
    }
}