<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureApproved
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Superuser selalu diperbolehkan
        if ($user && $user->isSuperuser()) {
            return $next($request);
        }

        // Cek approval
        if ($user && !$user->is_approved) {
            Auth::logout();
            $request->session()->invalidate();
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum disetujui oleh administrator.');
        }

        return $next($request);
    }
}
