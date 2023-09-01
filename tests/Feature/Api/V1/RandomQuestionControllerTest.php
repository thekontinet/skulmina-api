<?php

namespace Tests\Feature\Api\v1;

use App\Enums\RoleEnum;
use App\Models\Examination;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RandomQuestionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_generate_random_questions(): void
    {
        $exam = Examination::factory()->create();
        $exam->questions()->sync(Question::factory()->create());

        $response = $this->loginAs(RoleEnum::STUDENT)
        ->get(route('questions.random', $exam));

        $response->assertStatus(200);
    }
}
