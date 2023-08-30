<?php

namespace Tests\Feature\Api;

use App\Enums\RoleEnum;
use App\Models\Examination;
use App\Models\Option;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_exam_questions()
    {
        $this->loginAs(RoleEnum::TEACHER);
        $examination = Examination::factory()->create([
            'user_id' => $this->user->id
        ]);
        $questions = Question::factory(10)->create();

        $response = $this->get(route('questions.index', $examination));

        $response->assertSuccessful();
        $response->assertSeeInOrder($questions->pluck('title')->toArray());
    }

    public function test_student_cannot_get_exam_questions()
    {
        $this->loginAs(RoleEnum::STUDENT);
        $examination = Examination::factory()->create();

        $response = $this->get(route('questions.index', $examination));

        $response->assertStatus(404);
    }

    public function test_can_add_new_question_to_examination()
    {
        $examination = Examination::factory()->create([
            'user_id' => $this->user->id
        ]);
        $data = Question::factory()->make()->toArray();

        $this->loginAs(RoleEnum::TEACHER)->post(route('questions.store', $examination), $data)
            ->assertSuccessful();

        $this->assertTrue($examination->questions()->exists());
    }

    public function test_cannot_add_question_if_not_examination_author()
    {
        $examination = Examination::factory()->create();
        $data = Question::factory()->make()->toArray();

        $this->loginAs(RoleEnum::TEACHER)->post(route('questions.store', $examination), $data)
            ->assertStatus(404);
        $this->assertFalse($examination->questions()->exists());
    }

    public function test_can_update_question()
    {
        $examination = Examination::factory()->create([
            'user_id' => $this->user->id
        ]);
        $question = $examination->questions()->create(Question::factory()->make()->toArray());
        $data = [
            'description' => 'New test question'
        ];

        $this->loginAs(RoleEnum::TEACHER)->put(route('questions.update',[$examination, $question]), $data)
            ->assertSuccessful();
        $this->assertDatabaseHas(Question::class, $data);
    }

    public function test_cannot_update_question_if_not_examination_author()
    {
        $this->loginAs(RoleEnum::TEACHER);
        $examination = Examination::factory()->create();
        $question = $examination->questions()->create(Question::factory()->make()->toArray());
        $data = [
            'description' => 'New test question'
        ];

        $this->loginAs(RoleEnum::TEACHER)->put(route('questions.update',[$examination, $question]), $data)
            ->assertStatus(404);
        $this->assertDatabaseMissing(Question::class, $data);
    }

    public function test_can_delete_question()
    {
        $examination = Examination::factory()->create([
            'user_id' => $this->user->id
        ]);
        $question = $examination->questions()->create(Question::factory()->make()->toArray());

        $this->loginAs(RoleEnum::TEACHER)->delete(route('questions.destroy',[$examination, $question]))
            ->assertSuccessful();
        $this->assertDatabaseEmpty(Question::class);
    }

    public function test_cannot_delete_question_if_not_examination_author()
    {
        $this->loginAs(RoleEnum::TEACHER);
        $examination = Examination::factory()->create();
        $question = $examination->questions()->create(Question::factory()->make()->toArray());

        $this->loginAs(RoleEnum::TEACHER)->delete(route('questions.destroy',[$examination, $question]))
            ->assertStatus(404);
    }
}
