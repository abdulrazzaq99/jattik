<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('customer')->check()) {
            return redirect()->route('customer.login')->with('error', 'Please login to access this page.');
        }

        $customer = Auth::guard('customer')->user();

        // Check if customer is active
        if (!$customer->isActive()) {
            Auth::guard('customer')->logout();
            return redirect()->route('customer.login')->with('error', 'Your account is inactive. Please contact support.');
        }

        return $next($request);
    }
}
