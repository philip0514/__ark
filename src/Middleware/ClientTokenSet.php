<?php

namespace Philip0514\Ark\Middleware;

use Closure;
use Cookie;

class ClientTokenSet
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
    	if(!$request->session()->exists('client_token')) return $response;
    	$client_token = $request->session()->pull('client_token');
    	$expires_in = $request->session()->pull('expires');
		return $response->withCookie('client_token', $client_token, $expires_in);
    }
}