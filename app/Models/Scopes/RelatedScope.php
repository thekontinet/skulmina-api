<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class RelatedScope implements Scope
{
    public function __construct(private string|null $relationName = null, private Model|null $relatedModel = null, private string|null $relationKey = null)
    {
    }

    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $closure = null;

        if ($this->relatedModel) {
            $ralationKey = $this->relationKey ?? $this->relatedModel->getQualifiedKeyName();
            $closure = fn (Builder $builder) => $builder
                ->where($ralationKey, $this->relatedModel->getKey());
        }

        $builder->whereHas(
            $this->relationName,
            $closure
        );
    }
}
