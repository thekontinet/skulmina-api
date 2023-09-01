<?php

namespace App\Models;

use App\Models\Scopes\UserExamQuestionScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Tags\HasTags;

class Question extends Model
{
    use HasFactory, HasTags;

    protected $guarded = [];

    protected $attributes = [
        'type' => 'multiple'
    ];

    protected static function booted(): void
    {
        parent::boot();
        // static::addGlobalScope(new UserExamQuestionScope);
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
