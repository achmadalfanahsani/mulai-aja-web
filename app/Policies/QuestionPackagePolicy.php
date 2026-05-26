<?php

namespace App\Policies;

use App\Models\QuestionPackage;
use App\Models\User;

class QuestionPackagePolicy
{
    /**
     * Superuser dan Administrator boleh melakukan semua hal.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperuser() || $user->isAdministrator()) {
            return true;
        }
        return null; // Lanjut ke method spesifik
    }

    /**
     * Boleh melihat daftar paket soal?
     */
    public function viewAny(User $user): bool
    {
        return $user->isTeacher();
    }

    /**
     * Boleh melihat detail paket soal tertentu?
     */
    public function view(User $user, QuestionPackage $package): bool
    {
        return $user->id === $package->user_id;
    }

    /**
     * Boleh membuat paket soal?
     */
    public function create(User $user): bool
    {
        return $user->isTeacher();
    }

    /**
     * Boleh mengedit paket soal tertentu?
     */
    public function update(User $user, QuestionPackage $package): bool
    {
        return $user->id === $package->user_id;
    }

    /**
     * Boleh menghapus paket soal tertentu?
     */
    public function delete(User $user, QuestionPackage $package): bool
    {
        return $user->id === $package->user_id;
    }

    /**
     * Boleh toggle publish?
     */
    public function togglePublish(User $user, QuestionPackage $package): bool
    {
        return $user->id === $package->user_id;
    }

    /**
     * Boleh melihat hasil pengerjaan siswa?
     */
    public function viewResults(User $user, QuestionPackage $package): bool
    {
        return $user->id === $package->user_id;
    }
}
