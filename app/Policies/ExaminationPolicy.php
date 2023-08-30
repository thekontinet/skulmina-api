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
        return $user->id === $examination->user_id || $user->isInvitedTo($examination);
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
        return $examination->user_id == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Examination $examination): bool
    {
        return $examination->user_id == $user->id;
    }

    /**
     * Determine whether the user can submit the model.
     */
    public function submit(User $user, Examination $examination): Response
    {
        if(!$examination->hasInvitationFor($user)) return Response::deny('Not permitted to take this examination');
        if($examination->isSubmittedBy($user)) return Response::deny('You already attempt this examination');
        return Response::allow();
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
