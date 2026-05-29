<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the form for changing the user's password.
     */
    public function showChangePasswordForm()
    {
        return view('profile.change-password');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    /**
     * Update the user's theme preferences.
     */
    public function updateTheme(Request $request)
    {
        $validated = $request->validate([
            'theme_color' => ['sometimes', 'string'],
            'theme_mode' => ['sometimes', 'string', 'in:on,off,system'],
        ]);

        $request->user()->update($validated);

        return response()->json(['message' => 'Theme updated successfully']);
    }
}
