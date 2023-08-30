<?php

namespace Tests\Unit\App\Models;

use App\Enums\RoleEnum;
use App\Models\Examination;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExaminationTest extends TestCase
{
    use RefreshDatabase;

    public function test_model_only_returns_exam_created_by_authors(): void
    {
        $this->loginAs(RoleEnum::TEACHER);
        $authorsExams = Examination::factory(2)->create([
            'user_id' => $this->user->id
        ]);
        $exams = Examination::factory(2)->create();

        $allExams = Examination::all();

        $this->assertSame($allExams->pluck('id')->sort()->toArray(), $authorsExams->pluck('id')->sort()->toArray());
        $this->assertNotSame($allExams->pluck('id')->sort()->toArray(), $exams->pluck('id')->sort()->toArray());
    }

    public function test_model_only_returns_exams_to_student_that_has_reserved_seat(): void
    {
        $this->loginAs(RoleEnum::STUDENT);
        $exams = Examination::factory(5)->create();

        $this->user->inviteTo($exams[0]);

        $allExams = Examination::all();

        $this->assertSame($allExams->pluck('id')->sort()->toArray(), [$exams[0]->id]);
    }
}
