<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $isLoggedIn = session()->has('accessToken');
        if ($isLoggedIn && $request->routeIs('login')) {
            return redirect()->route('dashboard');
        }

        if (!$isLoggedIn && !$request->routeIs('login') && !$request->routeIs('login.process')) {
            return redirect()
                ->route('login')
                ->with('error', 'Sesi telah habis, silakan login kembali.');
        }

        return $next($request);
    }
}
