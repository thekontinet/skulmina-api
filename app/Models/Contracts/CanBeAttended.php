<?php

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface canBeAttended
{
    public function seats(): MorphMany;
}
