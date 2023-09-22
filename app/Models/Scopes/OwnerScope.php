<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class OwnerScope implements Scope
{
    public function __construct(private Model|null $model = null, private string $ownerKey = 'user_id')
    {
        if (!$model) {
            $this->model = auth()->user();
        }
    }

    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where($model->getTable().".{$this->ownerKey}", $this->model->id);
    }
}
