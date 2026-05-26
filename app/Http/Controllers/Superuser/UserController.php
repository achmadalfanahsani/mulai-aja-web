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
    public function index(Request $request)
    {
        $query = User::where('id', '!=', auth()->id());

        // Filter by name or email
        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('email', 'like', '%' . $request->q . '%');
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $isApproved = $request->status === 'approved';
            $query->where('is_approved', $isApproved);
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('superuser.users.index', compact('users'));
    }

    /**
     * Update user role
     */
    public function updateRole(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat mengubah role diri sendiri.');
        }

        if ($user->isSuperuser()) {
            return back()->with('error', 'Tidak dapat mengubah role superuser lain.');
        }

        $request->validate([
            'role' => 'required|in:student,teacher,administrator',
        ]);

        $user->update(['role' => $request->role]);

        return back()->with('success', "Role user {$user->name} berhasil diubah menjadi {$request->role}.");
    }

    /**
     * Approve user
     */
    public function approve(User $user)
    {
        $user->update(['is_approved' => true]);

        return back()->with('success', "Akun {$user->name} telah di-approve.");
    }

    /**
     * Reject/Unapprove user
     */
    public function reject(User $user)
    {
        $user->update(['is_approved' => false]);

        return back()->with('success', "Status approval {$user->name} telah dicabut.");
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request, User $user)
    {
        if ($user->isSuperuser() && $user->id !== auth()->id()) {
            return back()->with('error', 'Tidak dapat mengubah password superuser lain.');
        }

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
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        if ($user->isSuperuser()) {
            return back()->with('error', 'Tidak dapat menghapus akun superuser.');
        }

        $userName = $user->name;
        $user->delete();

        return back()->with('success', "User {$userName} berhasil dihapus.");
    }
}
