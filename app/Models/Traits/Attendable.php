<?php

namespace App\Models\Traits;

use App\Models\Seat;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Attendable
{
    public function seats(): MorphMany
    {
        return $this->morphMany(Seat::class, 'seatable');
    }

}
