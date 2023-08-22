<?php

namespace App\Models;

use App\Models\Contracts\canBeAttended;
use App\Models\Traits\Attendable;
use App\Models\Traits\HasManySeats;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Examination extends Model implements canBeAttended
{
    use HasFactory, Attendable;

    protected $guarded = [];

    protected $casts = [
        'start_time' => 'datetime:Y-m-d h:i:s',
        'end_time' => 'datetime:Y-m-d h:i:s'
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class);
    }
}
