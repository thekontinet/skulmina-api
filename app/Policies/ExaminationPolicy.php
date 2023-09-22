<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Examination;
use App\Models\User;

class ExaminationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Examination $examination): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(RoleEnum::TEACHER->value);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Examination $examination): bool
    {
        return $examination->user_id == $user?->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Examination $examination): bool
    {
        return $examination->user_id == $user?->id;
    }
}
