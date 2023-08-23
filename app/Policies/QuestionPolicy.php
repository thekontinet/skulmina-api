<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Examination;
use App\Models\User;
use App\Models\question;
use Illuminate\Auth\Access\Response;

class QuestionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        $examination = request()->route()->parameter('examination');

        if(!$examination) return false;

        if($user->hasRole(RoleEnum::STUDENT->value)){
            return $user->hasReservedSeatFor($examination);
        }

        return $examination->user_id === $user->id;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, question $question): bool
    {
       if(!$user->hasRole(RoleEnum::STUDENT->value)) return true;
       return $user->hasReservedSeatFor($question->examination);
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
    public function update(User $user, question $question): bool
    {
        return !$user->hasRole(RoleEnum::STUDENT->value) &&
        $question->examination->user_id == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, question $question): bool
    {
       return !$user->hasRole(RoleEnum::STUDENT->value) &&
       $question->examination->user_id == $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, question $question): bool
    {
       return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, question $question): bool
    {
       return false;
    }
}
