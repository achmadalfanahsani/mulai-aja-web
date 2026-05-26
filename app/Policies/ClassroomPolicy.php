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
        if ($user->isSuperuser() || $user->isAdministrator()) {
            return true;
        }
        return null;
    }

    /**
     * Boleh melihat daftar kelas?
     */
    public function viewAny(User $user): bool
    {
        return $user->isTeacher();
    }

    /**
     * Boleh melihat detail kelas tertentu?
     */
    public function view(User $user, Classroom $classroom): bool
    {
        return $user->id === $classroom->teacher_id;
    }

    /**
     * Boleh membuat kelas?
     */
    public function create(User $user): bool
    {
        return $user->isTeacher();
    }

    /**
     * Boleh mengedit kelas?
     */
    public function update(User $user, Classroom $classroom): bool
    {
        return $user->id === $classroom->teacher_id;
    }

    /**
     * Boleh menghapus kelas?
     */
    public function delete(User $user, Classroom $classroom): bool
    {
        return $user->id === $classroom->teacher_id;
    }
}
