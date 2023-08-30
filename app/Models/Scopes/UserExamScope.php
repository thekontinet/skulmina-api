<?php

namespace App\Models\Scopes;

use App\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UserExamScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Assuming you have the authenticated user's ID
        $user = auth()->user();

        // if($user->hasRole(RoleEnum::STUDENT->value)){

        // }

        $builder->whereHas('seats', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->orWhere(function($query) use($user){
            $query->where('user_id', $user->id);
        });
    }
}
