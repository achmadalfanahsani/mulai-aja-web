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
        if ($user->isSuperuser()) {
            return true;
        }
        return null; // Lanjut ke method spesifik
    }

    private function checkAccess(User $user, QuestionPackage $package): bool
    {
        if ($user->id === $package->user_id) {
            return true;
        }
        if ($user->isAdministrator() && $package->user && $package->user->created_by_id === $user->id) {
            return true;
        }
        return false;
    }

    /**
     * Boleh melihat daftar paket soal?
     */
    public function viewAny(User $user): bool
    {
        return $user->isTeacher() || $user->isAdministrator();
    }

    /**
     * Boleh melihat detail paket soal tertentu?
     */
    public function view(User $user, QuestionPackage $package): bool
    {
        return $this->checkAccess($user, $package);
    }

    /**
     * Boleh membuat paket soal?
     */
    public function create(User $user): bool
    {
        return $user->isTeacher() || $user->isAdministrator();
    }

    /**
     * Boleh mengedit paket soal tertentu?
     */
    public function update(User $user, QuestionPackage $package): bool
    {
        return $this->checkAccess($user, $package);
    }

    /**
     * Boleh menghapus paket soal tertentu?
     */
    public function delete(User $user, QuestionPackage $package): bool
    {
        return $this->checkAccess($user, $package);
    }

    /**
     * Boleh toggle publish?
     */
    public function togglePublish(User $user, QuestionPackage $package): bool
    {
        return $this->checkAccess($user, $package);
    }

    /**
     * Boleh melihat hasil pengerjaan siswa?
     */
    public function viewResults(User $user, QuestionPackage $package): bool
    {
        return $this->checkAccess($user, $package);
    }
}
