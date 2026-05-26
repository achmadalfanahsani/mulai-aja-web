<?php

namespace App\Policies;

use App\Models\Question;
use App\Models\QuestionPackage;
use App\Models\User;

class QuestionPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperuser() || $user->isAdministrator()) {
            return true;
        }
        return null;
    }

    /**
     * Boleh melihat daftar soal dalam paket?
     */
    public function viewAny(User $user, QuestionPackage $package): bool
    {
        return $user->id === $package->user_id;
    }

    /**
     * Boleh membuat soal baru dalam paket?
     */
    public function create(User $user, QuestionPackage $package): bool
    {
        return $user->id === $package->user_id;
    }

    /**
     * Boleh mengedit soal?
     */
    public function update(User $user, Question $question): bool
    {
        return $user->id === $question->questionPackage->user_id;
    }

    /**
     * Boleh menghapus soal?
     */
    public function delete(User $user, Question $question): bool
    {
        return $user->id === $question->questionPackage->user_id;
    }
}
