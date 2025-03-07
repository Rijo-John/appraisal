<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOrSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        
        if (Auth::check() && (Auth::user()->role == 1 || Auth::user()->role == 2)) {
            return $next($request);
        }

        return response()->view('errors.unauthorized', ['email' => Auth::user()->username], 403);
    }
}
