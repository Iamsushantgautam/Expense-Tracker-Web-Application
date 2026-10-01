<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Auth::user()->is_admin || strtolower(trim(Auth::user()->email)) !== 'sushantgautamlk6393@gmail.com') {
            abort(403, 'Unauthorized access. Admin privileges are restricted to sushantgautamlk6393@gmail.com.');
        }

        return $next($request);
    }
}
