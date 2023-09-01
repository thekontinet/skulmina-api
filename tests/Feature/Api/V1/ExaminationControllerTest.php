<?php

namespace Tests\Feature\Api\V1;

use App\Enums\RoleEnum;
use App\Models\Examination;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExaminationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_examinations()
    {
        $examinations = Examination::factory(5)->create([
            'user_id' => $this->user->id
        ]);

        $this->loginAs(RoleEnum::TEACHER)->get(route('examinations.index'))
            ->assertSuccessful()
            ->assertSeeInOrder($examinations->pluck('title')->toArray());
    }

    public function test_can_get_all_enrolled_examinations()
    {
        $examinations = Examination::factory(5)->create();
        $this->user->examinations()->sync($examinations->only([1,2,3]));

        $this->loginAs(RoleEnum::STUDENT)->get(route('student.exams', $this->user))
            ->assertSuccessful()
            ->assertSeeInOrder($examinations->only([1,2,3])->pluck('title')->toArray());
    }

    public function test_can_create_new_examinations(): void
    {
        $exam = Examination::factory()->create([
            'user_id' => $this->user->id
        ]);

        $this->loginAs(RoleEnum::TEACHER)
            ->post(route('examinations.store'), $exam->toArray())
            ->assertSuccessful();

        $this->assertDatabaseHas(Examination::class, (array) $exam->only(['description', 'title']));
    }

    public function test_can_update_examinations(): void
    {
        $examination = Examination::factory()->create([
            'user_id' => $this->user->id
        ]);

        $data = (array) Examination::factory()
            ->make()->toArray();

        $this->loginAs(RoleEnum::TEACHER)->put(
            route('examinations.update', $examination),
            $data
        )->assertSuccessful();

        $this->assertDatabaseHas(
            Examination::class,
            collect($data)->only(['title', 'description'])->toArray()
        );
    }

    public function test_can_attach_questions_to_examinations(): void
    {
        $examination = Examination::factory()->create([
            'user_id' => $this->user->id
        ]);

        $data = (array) Examination::factory()
            ->make()->toArray();

        $data['question_ids'] = Question::factory(5)->create()->pluck('id')->toArray();

        $this->loginAs(RoleEnum::TEACHER)
            ->put(route('examinations.update', $examination), $data)
            ->assertSuccessful();
        $this->assertCount(5,  $examination->questions()->pluck('questions.id'));
    }

    public function test_can_delete_exam()
    {
        $examination = Examination::factory()->create([
            'user_id' => $this->user->id
        ]);

        $this->loginAs(RoleEnum::TEACHER)
            ->delete(route('examinations.destroy', $examination))
            ->assertSuccessful();
    }
}
