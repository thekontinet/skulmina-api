<?php

namespace Tests\Feature\Api\V1;

use App\Enums\RoleEnum;
use App\Models\Course;
use App\Models\Examination;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExaminationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testCanGetAllExaminations()
    {
        $examinations = Examination::factory(5)->create([
            'user_id' => $this->user->id,
        ]);

        $this->loginAs(RoleEnum::TEACHER)->get(route('examinations.index'))
            ->assertSuccessful()
            ->assertSeeInOrder($examinations->pluck('title')->toArray());
    }

    public function testCanShowExamination()
    {
        $examination = Examination::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->loginAs(RoleEnum::TEACHER)->get(route('examinations.show', $examination))
            ->assertSuccessful()
            ->assertSeeInOrder([$examination->title]);
    }

    public function testCanCreateNewExaminations(): void
    {
        $exam = Examination::factory()->make([
            'user_id' => $this->user->id,
        ]);
        $data = collect($exam)->merge(['course_id' => Course::factory()->create()->id])->all();

        $this->loginAs(RoleEnum::TEACHER)
            ->post(
                route('examinations.store'),
                $data
            )
            ->assertSuccessful();

        $this->assertDatabaseHas(Examination::class, (array) $exam->only(['description', 'title']));
    }

    public function testCanUpdateExaminations(): void
    {
        $examination = Examination::factory()->create([
            'user_id' => $this->user->id,
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

    public function testCanAttachQuestionsToExaminations(): void
    {
        $examination = Examination::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $data = (array) Examination::factory()
            ->make()->toArray();

        $data['question_ids'] = Question::factory(5)->create()->pluck('id')->toArray();

        $this->loginAs(RoleEnum::TEACHER)
            ->put(route('examinations.update', $examination), $data)
            ->assertSuccessful();
        $this->assertCount(5, $examination->questions()->pluck('questions.id'));
    }

    public function testCanDeleteExam()
    {
        $examination = Examination::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->loginAs(RoleEnum::TEACHER)
            ->delete(route('examinations.destroy', $examination))
            ->assertSuccessful();
    }
}
