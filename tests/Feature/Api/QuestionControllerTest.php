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

        $response = $this->get(route('questions.index', $examination));

        $response->assertSuccessful();
    }

    public function test_student_cannot_get_exam_questions()
    {
        $this->loginAs(RoleEnum::STUDENT);
        $examination = Examination::factory()->create();

        $response = $this->get(route('questions.index', $examination));

        $response->assertForbidden();
    }

    public function test_can_add_new_question_to_examination()
    {
        $this->loginAs(RoleEnum::TEACHER);
        $examination = Examination::factory()->create([
            'user_id' => $this->user->id
        ]);
        $data = Question::factory()->make()->toArray();
        $data['options'] = [
            ['value' => 'test value 1', 'correct' => false],
            ['value' => 'test value 2', 'correct' => false],
            ['value' => 'test value 3', 'correct' => true]
        ];

        $response = $this->post(route('questions.store', $examination), $data);

        $response->assertSuccessful();
        $this->assertTrue($examination->questions()->exists());
        $this->assertDatabaseHas(Option::class,  ['value' => 'test value 1', 'is_correct' => false]);
        $this->assertDatabaseHas(Option::class,  ['value' => 'test value 2', 'is_correct' => false]);
        $this->assertDatabaseHas(Option::class,  ['value' => 'test value 3', 'is_correct' => true]);
    }

    public function test_cannot_add_question_if_not_examination_author()
    {
        $this->loginAs(RoleEnum::TEACHER);
        $examination = Examination::factory()->create();
        $data = Question::factory()->make()->toArray();

        $response = $this->post(route('questions.store', $examination), $data);

        $response->assertForbidden();
        $this->assertFalse($examination->questions()->exists());
    }

    public function test_can_update_question()
    {
        $this->loginAs(RoleEnum::TEACHER);
        $examination = Examination::factory()->create([
            'user_id' => $this->user->id
        ]);
        $question = $examination->questions()->create(Question::factory()->make()->toArray());
        $data = [
            'description' => 'New test question'
        ];
        $options = [
            ['value' => 'test value 1', 'correct' => false],
            ['value' => 'test value 2', 'correct' => false],
            ['value' => 'test value 3', 'correct' => true]
        ];

        $response = $this->put(route('questions.update',[$examination, $question]), [...$data, 'options' => $options]);

        $response->assertSuccessful();
        $this->assertDatabaseHas(Question::class, $data);
        $this->assertDatabaseHas(Option::class,  ['value' => 'test value 1', 'is_correct' => false]);
        $this->assertDatabaseHas(Option::class,  ['value' => 'test value 2', 'is_correct' => false]);
        $this->assertDatabaseHas(Option::class,  ['value' => 'test value 3', 'is_correct' => true]);
    }

    public function test_cannot_update_question_if_not_examination_author()
    {
        $this->loginAs(RoleEnum::TEACHER);
        $examination = Examination::factory()->create();
        $question = $examination->questions()->create(Question::factory()->make()->toArray());
        $data = [
            'description' => 'New test question'
        ];

        $response = $this->put(route('questions.update',[$examination, $question]), $data);

        $response->assertForbidden();
        $this->assertDatabaseMissing(Question::class, $data);
    }

    public function test_can_delete_question()
    {
        $this->loginAs(RoleEnum::TEACHER);
        $examination = Examination::factory()->create([
            'user_id' => $this->user->id
        ]);
        $question = $examination->questions()->create(Question::factory()->make()->toArray());

        $response = $this->delete(route('questions.destroy',[$examination, $question]));

        $response->assertSuccessful();
        $this->assertDatabaseEmpty(Question::class);
    }

    public function test_cannot_delete_question_if_not_examination_author()
    {
        $this->loginAs(RoleEnum::TEACHER);
        $examination = Examination::factory()->create();
        $question = $examination->questions()->create(Question::factory()->make()->toArray());

        $response = $this->delete(route('questions.destroy',[$examination, $question]));

        $response->assertForbidden();
    }
}
