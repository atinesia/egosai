<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboarded
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $request->user()?->tenant;

        if ($tenant && ! $tenant->isOnboarded()) {
            return redirect()->route('onboarding');
        }

        return $next($request);
    }
}
