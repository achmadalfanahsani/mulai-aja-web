<?php

namespace App\Policies;

use App\Models\QuestionAttempt;
use App\Models\User;

class QuestionAttemptPolicy
{
    /**
     * Hanya pemilik attempt yang boleh mengakses.
     */
    public function view(User $user, QuestionAttempt $attempt): bool
    {
        return $user->id === $attempt->user_id ||
               $user->id === $attempt->questionPackage->user_id ||
               $user->isSuperuser() ||
               $user->isAdministrator();
    }

    public function submit(User $user, QuestionAttempt $attempt): bool
    {
        return $user->id === $attempt->user_id;
    }

    public function saveResponse(User $user, QuestionAttempt $attempt): bool
    {
        return $user->id === $attempt->user_id;
    }
}
