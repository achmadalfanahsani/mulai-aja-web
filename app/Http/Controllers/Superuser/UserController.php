<?php

namespace App\Http\Controllers\Superuser;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $users = User::where('id', '!=', auth()->id())->get();
        return view('superuser.users.index', compact('users'));
    }

    /**
     * Update user role
     */
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:student,teacher,administrator,superuser',
        ]);

        $user->update(['role' => $request->role]);

        return back()->with('success', "Role user {$user->name} berhasil diubah menjadi {$request->role}.");
    }

    /**
     * Approve administrator
     */
    public function approve(User $user)
    {
        if ($user->role !== User::ROLE_ADMINISTRATOR) {
            return back()->with('error', 'User bukan merupakan administrator.');
        }

        $user->update(['is_approved' => true]);

        return back()->with('success', "Akun administrator {$user->name} telah di-approve.");
    }

    /**
     * Reject/Unapprove administrator
     */
    public function reject(User $user)
    {
        if ($user->role !== User::ROLE_ADMINISTRATOR) {
            return back()->with('error', 'User bukan merupakan administrator.');
        }

        $user->update(['is_approved' => false]);

        return back()->with('success', "Status approval {$user->name} telah dicabut.");
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', "Password user {$user->name} berhasil diperbarui.");
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        $userName = $user->name;
        $user->delete();

        return back()->with('success', "User {$userName} berhasil dihapus.");
    }
}
