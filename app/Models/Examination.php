<?php

namespace App\Models;

use App\Enums\RoleEnum;
use App\Models\Scopes\OwnerScope;
use App\Models\Scopes\RelatedScope;
use App\Models\Scopes\Searchable;
use App\Observers\ExaminationObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class Examination extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'code',
        'time_limit',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function generateUniqueCode()
    {
        $code = \Illuminate\Support\Str::random(8); // Adjust the length as needed

        // Check if the generated code already exists in the database
        if ($this->where('code', $code)->exists()) {
            // If it does, recursively call the function to generate a new code
            return $this->generateUniqueCode();
        }

        // If the code is unique, set it on the model
        $this->code = $code;
    }

    protected static function boot(): void
    {
        parent::boot();
        /** @var User */
        $user = Auth::user();

        if ($user->hasRole(RoleEnum::TEACHER->value)) {
            static::addGlobalScope(new OwnerScope($user));
        }

        if ($user->hasRole(RoleEnum::STUDENT->value)) {
            static::addGlobalScope(new RelatedScope('students', $user));
        }

        static::addGlobalScope(new Searchable(['code', 'description', 'title']));
        static::observe(ExaminationObserver::class);
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public function courses()
    {
        return $this->morphToMany(Course::class, 'modulable', 'course_modules');
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
