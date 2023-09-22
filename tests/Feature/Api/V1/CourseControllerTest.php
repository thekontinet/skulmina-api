<?php

namespace Tests\Feature\Api\V1;

use App\Enums\RoleEnum;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testCanGetAllCourses()
    {
        $courses = Course::factory(5)->create([
            'teacher_id' => $this->user->id,
        ]);

        $this->loginAs(RoleEnum::TEACHER)->get(route('courses.index'))
            ->assertSuccessful()
            ->assertSeeInOrder($courses->pluck('title')->toArray());
    }

    public function testCanCreateCourse()
    {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::TEACHER->value);

        $this->loginAs(RoleEnum::ADMIN)->post(route('courses.store', [
            'title' => 'test title',
            'code' => 'test code',
            'teacher_id' => $user->id,
        ]))
        ->assertSuccessful();
        $this->assertDatabaseHas(Course::class, [
            'title' => 'test title',
            'code' => 'test code',
            'teacher_id' => $user->id,
        ]);
    }

    public function testCannotCreateCourseWithoutAValidTeacherId()
    {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::STUDENT->value);

        $this->loginAs(RoleEnum::ADMIN)->post(route('courses.store', [
            'title' => 'test title',
            'code' => 'xxxx',
            'teacher_id' => $user->id,
        ]))
        ->assertInvalid('teacher_id');
    }

    public function testCanShowCourse()
    {
        $course = Course::factory()->create([
            'teacher_id' => $this->user->id,
        ]);

        $this->loginAs(RoleEnum::TEACHER)->get(route('courses.show', $course))
            ->assertSuccessful()
            ->assertSeeInOrder([$course->title]);
    }

    public function testCanUpdateCourse()
    {
        $course = Course::factory()->create([
            'teacher_id' => $this->user->id,
        ]);

        $user = User::factory()->create();
        $user->assignRole(RoleEnum::TEACHER->value);

        $this->loginAs(RoleEnum::ADMIN)->put(route('courses.update', $course), [
            'title' => 'test title',
            'code' => 'test code',
            'teacher_id' => $user->id,
        ])
        ->assertSuccessful()
        ->assertSeeText(['test title', 'test code', $user->id]);
        $this->assertDatabaseHas(Course::class, [
            'title' => 'test title',
            'code' => 'test code',
            'teacher_id' => $user->id,
        ]);
    }

    public function testCannotUpdateCourseWithInvalidTeacherId()
    {
        $course = Course::factory()->create([
            'teacher_id' => $this->user->id,
        ]);

        $user = User::factory()->create();
        $user->assignRole(RoleEnum::STUDENT->value);

        $this->loginAs(RoleEnum::ADMIN)->put(route('courses.update', $course), [
            'title' => 'test title',
            'code' => 'test code',
            'teacher_id' => $user->id,
        ])
        ->assertInvalid('teacher_id');
    }

    public function testCanDeleteCourse()
    {
        $course = Course::factory()->create([
            'teacher_id' => $this->user->id,
        ]);

        $this->loginAs(RoleEnum::ADMIN)->delete(route('courses.destroy', $course))
        ->assertSuccessful();

        $this->assertModelMissing($course);
    }

    public function testCanEnrollStudentsToCourse()
    {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::STUDENT->value);
        $course = Course::factory()->create([
            'teacher_id' => $this->user->id,
        ]);

        $this->loginAs(RoleEnum::ADMIN)->post(route('courses.enroll', $course), [
            'student_id' => $user->id,
        ])
        ->assertSuccessful();

        $this->assertTrue($course->students()->exists());
    }
}
