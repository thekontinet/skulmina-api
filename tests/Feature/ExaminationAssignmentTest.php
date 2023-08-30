<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Examination;
use App\Models\Seat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExaminationAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_assign_student_to_examination(): void
    {
        $this->loginAs(RoleEnum::TEACHER);
        $student = User::factory()->create();
        $student->assignRole(RoleEnum::STUDENT->value);
        $examination= Examination::factory()->create([
            'user_id' => $this->user->id
        ]);


        $response = $this->post(route('assignments.store', $examination), [
            'student_ids' => [$student->id]
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas(Seat::class, ['user_id' => $student->id]);
    }

    public function test_teacher_cannot_assign_student_to_examination_of_other_teachers(): void
    {
        $this->loginAs(RoleEnum::TEACHER);
        $student = User::factory()->create();
        $student->assignRole(RoleEnum::STUDENT->value);
        $examination= Examination::factory()->create();


        $response = $this->post(route('assignments.store', $examination), [
            'student_ids' => [$student->id]
        ]);

        $response->assertStatus(404);
        $this->assertDatabaseMissing(Seat::class, ['user_id' => $student->id]);
    }

    public function test_teacher_can_unassign_student_from_examination(): void
    {
        $this->loginAs(RoleEnum::TEACHER);
        $student = User::factory()->create();
        $student->assignRole(RoleEnum::STUDENT->value);
        $examination= Examination::factory()->create([
            'user_id' => $this->user->id
        ]);
        $student->inviteTo($examination);


        $response = $this->delete(route('assignments.destroy', [$examination]), [
            'student_ids' => [$student->id]
        ]);

        $response->assertStatus(204);
        $this->assertDatabaseMissing(Seat::class, ['user_id' => $student->id]);
    }

    public function test_teacher_cannot_unassign_student_from_examination_of_other_teachers(): void
    {
        $this->loginAs(RoleEnum::TEACHER);
        $student = User::factory()->create();
        $student->assignRole(RoleEnum::STUDENT->value);
        $examination= Examination::factory()->create();
        $student->inviteTo($examination);


        $response = $this->delete(route('assignments.destroy', [$examination]), [
            'student_ids' => [$student->id]
        ]);

        $response->assertStatus(404);
        $this->assertDatabaseHas(Seat::class, ['user_id' => $student->id]);
    }
}
