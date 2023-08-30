<?php

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface CanBeAttended
{
    public function seats(): MorphMany;
}
