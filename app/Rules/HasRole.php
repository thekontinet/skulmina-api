<?php

namespace App\Rules;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Builder;

class HasRole implements ValidationRule
{
    public function __construct(private RoleEnum $role)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (!User::where('id', $value)
                ->whereHas('roles', fn (Builder $builder) => $builder->where('name', $this->role->value))
                ->first()) {
            $fail("{$this->role->value} record not found");
        }
    }
}
