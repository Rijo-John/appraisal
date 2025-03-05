<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        
        if (Auth::check() && Auth::user()->role == 1) {
            return $next($request);
        }

        return response()->view('errors.unauthorized', ['email' => Auth::user()->username], 403);
    }
}
