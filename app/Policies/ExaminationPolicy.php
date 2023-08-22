<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Examination;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExaminationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Examination $examination): bool
    {
        if(!$user->hasRole(RoleEnum::STUDENT->value)) return true;
        return $user->hasReservedSeatFor($examination);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return !$user->hasRole(RoleEnum::STUDENT->value);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Examination $examination): bool
    {
        return !$user->hasRole(RoleEnum::STUDENT->value) &&
                $examination->user_id == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Examination $examination): bool
    {
        return !$user->hasRole(RoleEnum::STUDENT->value) &&
        $examination->user_id == $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Examination $examination): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Examination $examination): bool
    {
        return false;
    }
}
