<?php

namespace App\Policies;

use App\Models\Classroom;
use App\Models\User;

class ClassroomPolicy
{
    /**
     * Admin & Superuser boleh melakukan semua hal.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperuser()) {
            return true;
        }
        return null;
    }

    /**
     * Boleh melihat daftar kelas?
     */
    public function viewAny(User $user): bool
    {
        return $user->isTeacher() || $user->isAdministrator();
    }

    /**
     * Boleh melihat detail kelas tertentu?
     */
    public function view(User $user, Classroom $classroom): bool
    {
        if ($user->isAdministrator() && $classroom->created_by_id === $user->id) {
            return true;
        }
        return $classroom->teachers()->where('users.id', $user->id)->exists();
    }

    /**
     * Boleh membuat kelas?
     * HANYA Administrator & Superuser yang boleh (di-handle di before).
     * Teacher secara eksplisit tidak boleh.
     */
    public function create(User $user): bool
    {
        return $user->isAdministrator();
    }

    /**
     * Boleh mengedit kelas?
     */
    public function update(User $user, Classroom $classroom): bool
    {
        if ($user->isAdministrator() && $classroom->created_by_id === $user->id) {
            return true;
        }
        return $classroom->teachers()->where('users.id', $user->id)->exists();
    }

    /**
     * Boleh menghapus kelas?
     */
    public function delete(User $user, Classroom $classroom): bool
    {
        if ($user->isAdministrator() && $classroom->created_by_id === $user->id) {
            return true;
        }
        return $classroom->teachers()->where('users.id', $user->id)->exists();
    }
}
