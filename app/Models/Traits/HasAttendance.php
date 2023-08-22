<?php

namespace App\Models\Traits;

use App\Models\Contracts\canBeAttended;
use App\Models\Seat;

trait HasAttendance
{
    public function reserveSeatFor(canBeAttended $model)
    {
        $seat = $model->seats()->make();
        $this->seats()->save($seat);
         return $seat;
    }

    public function hasReservedSeatFor(canBeAttended $model)
    {
        return $model->seats()
            ->where('user_id', $this->id)
            ->exists();
    }

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }
}
