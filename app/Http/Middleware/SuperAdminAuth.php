<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SuperAdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::get('super_admin_authenticated')) {
            return redirect()->route('super-admin.login');
        }

        return $next($request);
    }
}
