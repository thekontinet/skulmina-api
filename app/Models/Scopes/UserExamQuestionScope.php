<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UserExamQuestionScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Get the authenticated user's ID
        $user_id = auth()->user()->id;

        // Apply a condition to the builder to fetch questions related to the user's exams
        $builder->where(function ($query) use ($user_id) {
            // Questions related to exams the user has seats for
            $query->whereHas('examination.seats', function ($subQuery) use ($user_id) {
                $subQuery->where(['user_id'=> $user_id]);
            });

            // Questions related to exams the user has created
            $query->orWhereHas('examination', function ($subQuery) use ($user_id) {
                $subQuery->where('user_id', $user_id);
            });
        });
    }
}
