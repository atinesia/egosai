<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TenantSubscriptionCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        // Jika user memiliki tenant dan masa aktifnya sudah lewat dari waktu sekarang
        if ($user && $user->tenant && $user->tenant->expired_at && now()->gt($user->tenant->expired_at)) {

            // Izinkan mereka tetap membuka halaman billing untuk bayar, tapi tendang dari rute dashboard lain
            if (!$request->routeIs('dashboard.billing') && !$request->routeIs('livewire.update')) {
                session()->flash('error', 'Masa uji coba gratis 1 minggu Anda telah habis. Silakan lakukan aktivasi paket untuk melanjutkan.');
                return redirect()->route('dashboard.billing');
            }
        }
        return $next($request);
    }
}
