<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Examination;
use App\Services\ExaminationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExaminationProcessControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_can_take_exam(): void
    {
        $this->loginAs(RoleEnum::STUDENT);
        $exam = Examination::factory()->create();
        $this->user->inviteTo($exam);
        $response = $this->post(route('exam.start', $exam));

        $response->assertStatus(200);
    }

    public function test_cannot_take_exam_more_than_once(): void
    {
        $this->loginAs(RoleEnum::STUDENT);
        $exam = Examination::factory()->create();
        $examService = ExaminationService::forUser($this->user);
        $examService->inviteTo($exam);
        $examService->start($exam);
        $examService->handleSubmit($exam, []);

        $response = $this->post(route('exam.start', $exam));

        $response->assertStatus(403);
    }

    public function test_can_submit_exam(): void
    {
        $this->loginAs(RoleEnum::STUDENT);
        $exam = Examination::factory()->create();
        $examService = ExaminationService::forUser($this->user);
        $examService->inviteTo($exam);
        $examService->start($exam);

        $response = $this->post(route('exam.submit', $exam), [
            'answers' => [
                '1' => '10'
            ]
        ]);

        $response->assertStatus(201);
    }

    public function test_cannot_submit_exam_more_than_once(): void
    {
        $this->loginAs(RoleEnum::STUDENT);
        $exam = Examination::factory()->create();
        $examService = ExaminationService::forUser($this->user);
        $examService->inviteTo($exam);
        $examService->start($exam);
        $examService->handleSubmit($exam, []);

        $response = $this->post(route('exam.submit', $exam), [
            'answers' => [
                '1' => '10'
            ]
        ]);

        $response->assertStatus(403);
    }
}
