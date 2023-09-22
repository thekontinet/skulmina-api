<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Tags\HasTags;

class Question extends Model
{
    use HasFactory;
    use HasTags;

    protected $guarded = [];

    protected $attributes = [
        'type' => 'multiple',
    ];

    protected static function booted(): void
    {
        parent::boot();
        static::addGlobalScope(function (Builder $query) {
            $user = auth()->user();
            $query->whereHas('examinations', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        });
        static::addGlobalScope(new Searchable(['description']));
    }

    public function examinations(): BelongsToMany
    {
        return $this->belongsToMany(Examination::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }
}
