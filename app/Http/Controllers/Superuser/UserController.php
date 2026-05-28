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

        // Administrator isolation logic
        if (auth()->user()->isAdministrator()) {
            // Only see users created by this administrator
            $query->where('created_by_id', auth()->id())
                  ->whereNotIn('role', [User::ROLE_SUPERUSER, User::ROLE_ADMINISTRATOR]);
        }

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

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $roles = [User::ROLE_STUDENT, User::ROLE_TEACHER];
        if (auth()->user()->isSuperuser()) {
            $roles[] = User::ROLE_ADMINISTRATOR;
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:' . implode(',', $roles)],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_approved' => true, // Created by admin = approved
            'created_by_id' => auth()->id(),
        ]);

        return redirect()->route('admin.users.index')->with('success', "User {$request->name} berhasil dibuat.");
    }

    /**
     * Check if the current user can manage the given user.
     */
    protected function authorizeUserManagement(User $user)
    {
        $currentUser = auth()->user();

        if ($currentUser->isSuperuser()) {
            return true;
        }

        if ($currentUser->isAdministrator()) {
            // Administrator can only manage users they created
            return $user->created_by_id === $currentUser->id;
        }

        return false;
    }

    /**
     * Update user role
     */
    public function updateRole(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat mengubah role diri sendiri.');
        }

        if (!$this->authorizeUserManagement($user)) {
            return back()->with('error', 'Anda tidak memiliki wewenang untuk mengelola user ini.');
        }

        $roles = [User::ROLE_STUDENT, User::ROLE_TEACHER];
        if (auth()->user()->isSuperuser()) {
            $roles[] = User::ROLE_ADMINISTRATOR;
        }

        $request->validate([
            'role' => 'required|in:' . implode(',', $roles),
        ]);

        $user->update(['role' => $request->role]);

        return back()->with('success', "Role user {$user->name} berhasil diubah menjadi {$request->role}.");
    }

    /**
     * Approve user
     */
    public function approve(User $user)
    {
        if (!$this->authorizeUserManagement($user)) {
            return back()->with('error', 'Anda tidak memiliki wewenang untuk mengelola user ini.');
        }

        $user->update(['is_approved' => true]);

        return back()->with('success', "Akun {$user->name} telah di-approve.");
    }

    /**
     * Reject/Unapprove user
     */
    public function reject(User $user)
    {
        if (!$this->authorizeUserManagement($user)) {
            return back()->with('error', 'Anda tidak memiliki wewenang untuk mengelola user ini.');
        }

        $user->update(['is_approved' => false]);

        return back()->with('success', "Status approval {$user->name} telah dicabut.");
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            // Self password update handled in ProfileController
        } else {
            if (!$this->authorizeUserManagement($user)) {
                return back()->with('error', 'Anda tidak memiliki wewenang untuk mengelola user ini.');
            }
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

        if (!$this->authorizeUserManagement($user)) {
            return back()->with('error', 'Anda tidak memiliki wewenang untuk mengelola user ini.');
        }

        $userName = $user->name;
        $user->delete();

        return back()->with('success', "User {$userName} berhasil dihapus.");
    }
}
