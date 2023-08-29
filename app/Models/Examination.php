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

    public function hasInvitationFor(User $user): bool
    {
        return $this->seats()->where('user_id', $user->id)->exists();
    }

    public function attendedBy(User $user): bool
    {
        return $this->seats()
            ->where('user_id', $user->id)
            ->where('start_at', '<>', null)
            ->exists();
    }

    public function isSubmittedBy(User $user)
    {
        return $this->submissions()->where('user_id', $user->id)->exists();
    }

    public function hasBeenAttemptedBy(User $user): bool
    {
        return $this->submissions()->where('user_id', $user->id)->exists();
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
