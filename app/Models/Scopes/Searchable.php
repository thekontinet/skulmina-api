<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class Searchable implements Scope
{
    public function __construct(private array $fields)
    {
        $searchFields = $fields;

        if (request()->input('fields')) {
            $userFields = explode(',', request()->input('fields'));
            $filteredFields = $this->filterFields($userFields);
            if (count($filteredFields) > 0) {
                $searchFields = $filteredFields;
            }
        }

        $this->fields = $searchFields;
    }

    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if ($query = request()->input('q')) {
            $builder->where(function (Builder $q) use ($query) {
                foreach ($this->fields as $field) {
                    $q->orWhere($field, 'like', "%$query%");
                }
            });
        }

        if ($query = $this->filterFields(request()->input('sort_by'))[0] ?? null) {
            $direction = $this->parseSortDirection(request()->input('order'));

            $builder->orderBy($query, $direction);
        }
    }

    private function parseSortDirection($value)
    {
        return match ($value) {
            'a' => 'asc',
            'd' => 'desc',
            default => 'asc'
        };
    }

    private function filterFields($fields)
    {
        if (!$fields) {
            return [];
        }

        if (is_string($fields)) {
            $fields = explode(',', $fields);
        }

        return array_filter($fields, fn ($field) => in_array($field, $this->fields));
    }
}
