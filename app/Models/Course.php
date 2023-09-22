<?php

namespace App\Models;

use App\Enums\RoleEnum;
use App\Models\Scopes\OwnerScope;
use App\Models\Scopes\RelatedScope;
use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Course extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function boot(): void
    {
        parent::boot();

        /** @var User */
        $user = Auth::user();

        if ($user->hasRole(RoleEnum::TEACHER->value)) {
            static::addGlobalScope(new OwnerScope($user, 'teacher_id'));
        }

        if ($user->hasRole(RoleEnum::STUDENT->value)) {
            static::addGlobalScope(new RelatedScope('students', $user));
        }

        static::addGlobalScope(new Searchable(['code', 'title']));
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'course_student', null, 'student_id');
    }

    public function examinations()
    {
        return $this->morphToMany(Examination::class, 'modulable');
    }
}
