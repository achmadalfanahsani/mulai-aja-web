<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class ResetPasswordController extends Controller
{
    /**
     * Show the password reset form for the given token.
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password')->with([
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Reset the given user's password.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Password Anda berhasil diatur ulang! Silakan masuk dengan password baru.');
        }

        // Handle specific error status
        $message = 'Gagal mengatur ulang password. Silakan coba lagi.';
        if ($status === Password::INVALID_USER) {
            $message = 'User dengan email tersebut tidak ditemukan.';
        } elseif ($status === Password::INVALID_PASSWORD) {
            $message = 'Password tidak valid atau konfirmasi password tidak cocok.';
        } elseif ($status === Password::INVALID_TOKEN) {
            $message = 'Token reset password tidak valid atau sudah kedaluwarsa.';
        }

        return back()->withErrors(['email' => $message]);
    }
}
