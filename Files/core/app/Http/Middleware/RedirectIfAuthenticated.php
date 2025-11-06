<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            $user = auth()->user();
            if ($user->user_type == "manager") {
                return redirect()->route('manager.dashboard');
            } elseif ($user->user_type == "staff") {
                return redirect()->route('staff.dashboard');
            }
        }
        return $next($request);
    }
}
