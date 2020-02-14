<?php

namespace Philip0514\Ark\Middleware;

use Closure;
use Auth;

class ElfinderPermission
{
    public function handle($request, Closure $next)
    {
        if(!Auth::guard('admin')->check()){
            return response()->json([
                'error'   =>  'Login First.',
            ], 500);
            exit;
        }

        return $next($request);
    }
}
