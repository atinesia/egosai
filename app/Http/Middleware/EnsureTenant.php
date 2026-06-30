<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pastikan user yang login punya tenant aktif sebelum mengakses dashboard.
 */
class EnsureTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->tenant_id) {
            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda belum terhubung dengan tenant manapun.',
            ]);
        }

        if ($user->tenant && ! $user->tenant->is_active) {
            abort(403, 'Akun bisnis Anda sedang tidak aktif. Silakan hubungi admin.');
        }

        return $next($request);
    }
}
