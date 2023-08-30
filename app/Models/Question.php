<?php

namespace App\Models;

use App\Models\Scopes\UserExamQuestionScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $attributes = [
        'type' => 'multiple'
    ];

    protected static function booted(): void
    {
        parent::boot();
        static::addGlobalScope(new UserExamQuestionScope);
    }

    public function examination(): BelongsTo
    {
        return $this->belongsTo(Examination::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }
}
