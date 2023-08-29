<?php

namespace App\Models\Traits;

use App\Exceptions\CustomeException;
use App\Models\Contracts\canBeAttended;
use App\Models\Examination;
use App\Models\Seat;

trait HasAttendance
{
    public function inviteTo(Examination $examination): Seat
    {
        $seat = $examination->seats()->save($this->seats()->make());
        return $seat;
    }

    public function reject(Examination $examination): void
    {
       $examination->seats()->where('user_id', $this->id)->delete();
    }

    public function isInvitedTo(Examination $examination)
    {
        return $examination->seats()
            ->where('user_id', $this->id)
            ->exists();
    }

    public function attend(Examination $examination)
    {
        return $examination->seats()
            ->where('user_id', $this->id)
            ->update(['start_at' => now()]);
    }

    public function leave(Examination $examination)
    {
        $seat = $examination->seats()
        ->where('user_id', $this->id)
        ->where('start_at', '<>', null)
        ->first();

        if(!$seat) throw new CustomeException('You havent attend this examination');

        return $seat->update(['end_at' => now()]);
    }

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }
}
