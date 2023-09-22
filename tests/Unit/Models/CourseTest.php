<?php

namespace Tests\Unit\Models;

use App\Enums\RoleEnum;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    public function testTeacherCanOnlyAccessCoursesAssignedToThem(): void
    {
        $this->loginAs(RoleEnum::TEACHER);
        Course::factory(1)->create([
            'title' => 'test title',
        ]);
        $courses = Course::factory(3)->create([
            'teacher_id' => $this->user->id,
        ]);
        $this->assertEquals(Course::all()->toArray(), $courses->toArray());
    }

    public function testStudentsCanOnlyAccessCoursesTheyEnrolledFor(): void
    {
        $this->loginAs(RoleEnum::STUDENT);
        Course::factory()->create();
        $courses = Course::factory(3)->create([
            'teacher_id' => User::factory(),
        ]);

        $this->user->courses()->attach($courses);
        $this->assertEquals(Course::all()->toArray(), $courses->toArray());
    }

    public function testAdminCanOnlyAccessAllCourse(): void
    {
        $this->loginAs(RoleEnum::ADMIN);
        $courses = Course::factory(3)->create([
            'teacher_id' => User::factory(),
        ]);
        $this->assertEquals(Course::all()->toArray(), $courses->toArray());
    }
}
