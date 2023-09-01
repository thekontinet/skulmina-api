<?php

namespace Tests\Feature\Api\V1;

use App\Enums\RoleEnum;
use App\Models\Examination;
use App\Models\Seat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExaminationEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_assign_student_to_exam(): void
    {
        $this->loginAs(RoleEnum::TEACHER);
        $student = User::factory(10)->create();
        $examination= Examination::factory()->create([
            'user_id' => $this->user->id
        ]);


        $response = $this->post(route('exam.enroll', $examination), [
            'student_ids' => $student->pluck('id')->toArray()
        ]);

        $response->assertStatus(201);
        $this->assertEquals($examination->students()->count(), 10);
    }
}
