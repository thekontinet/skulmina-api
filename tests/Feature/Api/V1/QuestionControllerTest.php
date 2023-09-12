<?php

namespace Tests\Feature\Api\V1;

use App\Enums\RoleEnum;
use App\Models\Examination;
use App\Models\Option;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_questions()
    {
        $this->loginAs(RoleEnum::TEACHER);
        $exam = Examination::factory()->create([
            'user_id' => $this->user->id
        ]);
        $questions = Question::factory(8)->create();
        $exam->questions()->attach($questions);

        $response = $this->get(route('questions.index'));

        $response->assertSuccessful();
        $response->assertSeeInOrder($questions->pluck('description')->toArray());
    }

    public function test_can_show_question()
    {
        $this->loginAs(RoleEnum::TEACHER);
        $exam = Examination::factory()->create([
            'user_id' => $this->user->id
        ]);
        $question = Question::factory()->create();
        $exam->questions()->attach($question);

        $response = $this->get(route('questions.show', $question));

        $response->assertSuccessful();
        $response->assertSeeInOrder([$question->description]);
    }

    public function test_can_add_new_question()
    {
        $exam = Examination::factory()->create([
            'user_id' => $this->user->id
        ]);
        $data = ['description' => 'test question'];
        $data['examination_id'] = $exam->id;
        $data['options'] = ['opt1', 'opt2'];
        $data['answers'] = ['opt3', 'opt4'];

        $this->loginAs(RoleEnum::TEACHER)->post(route('questions.store'), $data)
            ->assertSuccessful();

        $this->assertDatabaseHas(Question::class, ['description' => 'test question']);
        $this->assertDatabaseHas(Option::class, ['value' => 'opt3', 'is_correct' => 1]);
        $this->assertDatabaseHas(Option::class, ['value' => 'opt1', 'is_correct' => 0]);
    }

    public function test_can_add_new_question_with_examination()
    {
        $exam = Examination::factory()->create([
            'user_id' => $this->user->id
        ]);
        $data = ['description' => 'test question'];
        $data['examination_id'] = $exam->id;
        $data['options'] = ['opt1', 'opt2'];
        $data['answers'] = ['opt3', 'opt4'];

        $this->loginAs(RoleEnum::TEACHER)->post(route('questions.store'), $data)
            ->assertSuccessful();

        $this->assertTrue($exam->questions()->count() === 1);
    }

    public function test_can_update_existing_question()
    {
        $exam = Examination::factory()->create([
            'user_id' => $this->user->id
        ]);
        $question = Question::factory()->create();
        $question->examinations()->attach($exam);

        $data = ['description' => 'test question'];
        $data['options'] = ['opt1', 'opt2'];
        $data['answers'] = ['opt3', 'opt4'];

        $this->loginAs(RoleEnum::TEACHER)->put(route('questions.update', $question), $data)
            ->assertSuccessful();

        $this->assertDatabaseHas(Question::class, ['description' => 'test question']);
        $this->assertDatabaseHas(Option::class, ['value' => 'opt3', 'is_correct' => 1]);
        $this->assertDatabaseHas(Option::class, ['value' => 'opt1', 'is_correct' => 0]);
    }

    public function test_can_delete_question()
    {
        $exam = Examination::factory()->create([
            'user_id' => $this->user->id
        ]);
        $question = Question::factory()->create();
        $question->examinations()->attach($exam);

        $this->loginAs(RoleEnum::TEACHER)->delete(route('questions.destroy', $question))
            ->assertSuccessful();

        $this->assertDatabaseEmpty(Question::class);
    }
}
