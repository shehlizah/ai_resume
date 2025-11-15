<?php

namespace App\Policies;

use App\Models\CoverLetter;
use App\Models\User;

class CoverLetterPolicy
{
    /**
     * Determine if the user can view the cover letter.
     */
    public function view(User $user, CoverLetter $coverLetter): bool
    {
        return $user->id === $coverLetter->user_id;
    }

    /**
     * Determine if the user can create cover letters.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update the cover letter.
     */
    public function update(User $user, CoverLetter $coverLetter): bool
    {
        return $user->id === $coverLetter->user_id;
    }

    /**
     * Determine if the user can delete the cover letter.
     */
    public function delete(User $user, CoverLetter $coverLetter): bool
    {
        return $user->id === $coverLetter->user_id;
    }
}
