<?php

namespace Philip0514\Ark\Middleware;

use Closure;
use Auth;

class Permission
{
    public function handle($request, Closure $next)
    {
        if(!Auth::guard('admin')->check()){
            return redirect()->route('login');
        }

        $admin = Auth::guard('admin')->user();
        $rows1 = $admin->getAllPermissions()->toArray();
        $permission = [];
        for($i=0; $i<sizeof($rows1); $i++){
            $permission[] = $rows1[$i]['name'];
        }
        session()->put( config('ark.permission') , $permission);

        return $next($request);
    }
}
