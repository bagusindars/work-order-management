<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Mary\Exceptions\ToastException;
use Symfony\Component\HttpFoundation\Response;

class EnsureProductionManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->is_production_manager) {
            return $next($request);
        }

        return redirect()->route('home')->with('error', 'You dont have the permission');
    }
}
