<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class CustomerOrderRateLimit
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'customer-order:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 10)) {
            abort(429, 'Terlalu banyak percobaan order. Mohon tunggu beberapa menit sebelum mencoba lagi.');
        }

        RateLimiter::hit($key, 120);

        return $next($request);
    }
}