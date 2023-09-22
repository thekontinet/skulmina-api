<?php

namespace Tests\Feature\Api\V1;

use App\Enums\RoleEnum;
use App\Models\Examination;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExaminationProcessControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testCanSubmitExam(): void
    {
        $this->loginAs(RoleEnum::STUDENT);
        $exam = Examination::factory()->create();
        $exam->students()->attach($this->user);

        $response = $this->post(route('exam.submit', $exam), [
            'answers' => [
                '1' => '10',
            ],
        ]);

        $response->assertStatus(201);
    }
}
