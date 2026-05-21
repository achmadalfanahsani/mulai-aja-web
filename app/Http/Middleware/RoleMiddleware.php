<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user's role is in the allowed roles
        if (in_array($user->role, $roles)) {
            
            // Check for administrator approval
            if ($user->role === 'administrator' && !$user->is_approved) {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Akun Administrator Anda belum di-approve oleh Superuser.');
            }

            return $next($request);
        }

        abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
    }
}
