<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'is_correct' => 'boolean'
    ];

    protected function scopeOnlyCorrectOptions($query)
    {
        return $query->where('is_correct', true);
    }
}
