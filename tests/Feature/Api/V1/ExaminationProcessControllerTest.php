<?php

namespace Tests\Feature\Api\V1;

use App\Enums\RoleEnum;
use App\Models\Examination;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExaminationProcessControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_submit_exam(): void
    {
        $this->loginAs(RoleEnum::STUDENT);
        $exam = Examination::factory()->create();

        $response = $this->post(route('exam.submit', $exam), [
            'answers' => [
                '1' => '10'
            ]
        ]);

        $response->assertStatus(201);
    }

}
