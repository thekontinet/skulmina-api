<?php

namespace App\Models;

use App\Models\Scopes\OwnerScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Examination extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'start_time' => 'datetime:Y-m-d h:i:s',
        'end_time' => 'datetime:Y-m-d h:i:s'
    ];

    protected static function boot(): void
    {
        parent::boot();
        // static::addGlobalScope(new OwnerScope);
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
