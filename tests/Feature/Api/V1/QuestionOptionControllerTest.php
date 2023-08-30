<?php

namespace Tests\Feature\Api\V1;

use App\Enums\RoleEnum;
use App\Models\Examination;
use App\Models\Option;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class QuestionOptionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_add_option_to_question(): void
    {
        $question = Question::factory()->create([
            'examination_id' => Examination::factory()->create([
                'user_id' => $this->user->id
            ])
        ]);

        $data = ['value' => 'test value 1', 'is_correct' => false];

        $this->loginAs(RoleEnum::TEACHER)->post(route('options.store', [$question]), $data)
            ->assertStatus(200);

        $this->assertDatabaseHas(Option::class, $data);
    }

    public function test_can_delete_option()
    {
        $question = Question::factory()->create([
            'examination_id' => Examination::factory()->create([
                'user_id' => $this->user->id
            ])
        ]);

        $options = $question->options()->createMany(Option::factory(4)->make()->toArray());

        $this->loginAs(RoleEnum::TEACHER)
            ->delete(
                route('options.destroy',[$question, $question->options()->first()]
            ))
            ->assertSuccessful();

        $this->assertDatabaseMissing(Option::class, $options->first()->toArray());
    }
}
