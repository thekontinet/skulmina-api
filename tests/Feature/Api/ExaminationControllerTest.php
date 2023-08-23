<?php

namespace Tests\Feature\Api;

use App\Enums\RoleEnum;
use App\Models\Examination;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExaminationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_examinations()
    {
        $this->loginAs(RoleEnum::TEACHER);
        $examinations = Examination::factory(2)->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->get(route('examinations.index'));

        $response->assertSuccessful();
        $response->assertSeeInOrder($examinations->pluck('title')->toArray());
    }

    public function test_student_can_get_all_examinations_available_for_them()
    {
        $this->loginAs(RoleEnum::STUDENT);
        $examinations = Examination::factory(2)->create([
            'user_id' => $this->user->id
        ]);

        $this->user->reserveSeatFor($examinations[0]);

        $response = $this->get(route('examinations.index'));

        $response->assertSuccessful();
        $response->assertSee($examinations->first()->title);
        $response->assertDontSee($examinations->last()->title);
    }


    public function test_can_create_new_examinations(): void
    {
        $this->loginAs(RoleEnum::TEACHER);

        $response = $this->post(
            route('examinations.store'),
            Examination::factory()->make()->toArray()
        );

        $response->assertCreated();
        $this->assertDatabaseCount(Examination::class, 1);
    }

    public function test_cannot_create_new_examinations_for_other_teachers(): void
    {
        $this->loginAs(RoleEnum::TEACHER);
        $data = Examination::factory()->make(['user_id' => 2])->toArray();

        $response = $this->post(
            route('examinations.store'),
            $data
        );

        $response->assertCreated();
        $this->assertDatabaseCount(Examination::class, 1);
        $this->assertDatabaseMissing(Examination::class, ['user_id' => 2]);
        $this->assertDatabaseHas(Examination::class, ['user_id' => auth()->id()]);
    }

    public function test_students_cannot_create_new_examinations(): void
    {
        $this->loginAs(RoleEnum::STUDENT);

        $response = $this->post(
            route('examinations.store'),
            Examination::factory()->make()->toArray()
        );

        $response->assertForbidden();
        $this->assertDatabaseEmpty(Examination::class);
    }

    public function test_can_update_examinations(): void
    {
        $this->loginAs(RoleEnum::TEACHER);

        $examination = Examination::factory()->create([
            'user_id' => $this->user->id
        ]);
        $newExamination = (array) Examination::factory()->make([
            'user_id' => $this->user->id
        ])->toArray();
        $response = $this->put(
            route('examinations.update', $examination),
            $newExamination
        );

        $response->assertSuccessful();
        $this->assertDatabaseHas(Examination::class, $newExamination);
    }

    public function test_that_author_cannot_update_another_authors_examinations(): void
    {
        $this->loginAs(RoleEnum::TEACHER);

        $examination = Examination::factory()->create();
        $newExamination = (array) Examination::factory()->make()->toArray();

        $response = $this->put(
            route('examinations.update', $examination),
            $newExamination
        );

        $response->assertForbidden();
    }

    public function test_can_get_examination_data()
    {
        $this->loginAs(RoleEnum::TEACHER);
        $examination = Examination::factory()->create();

        $response = $this->get(route('examinations.show', $examination) );

        $response->assertSuccessful();
        $response->assertJsonFragment($examination->pluck('title')->toArray());
    }

    public function test_that_exam_data_is_available_for_seated_students()
    {
        $examination = Examination::factory()->create();
        $this->loginAs(RoleEnum::ADMIN);
        $this->user->reserveSeatFor($examination);

        $response = $this->get(route('examinations.show', $examination));
        $response->assertSuccessful();
    }

    public function test_that_exam_data_is_unavailable_for_non_seated_students()
    {
        $this->loginAs(RoleEnum::STUDENT);
        $examination = Examination::factory()->create();

        $response = $this->get(route('examinations.show', $examination) );
        $response->assertForbidden();
    }

    public function test_can_delete_examination()
    {
        $this->loginAs(RoleEnum::TEACHER);
        $examination = Examination::factory()->create([
            'user_id' => $this->user->id
        ]);
        $response = $this->delete(route('examinations.destroy', $examination));

        $response->assertSuccessful();
        $this->assertDatabaseMissing(Examination::class, $examination->toArray());
    }

    public function test_that_author_cannot_delete_other_authors_examination()
    {
        $this->loginAs(RoleEnum::TEACHER);
        $examination = Examination::factory()->create();

        $response = $this->delete(route('examinations.destroy', $examination));
        $response->assertForbidden();
        $this->assertDatabaseHas(Examination::class, (array) $examination->only(['title']));
    }
}
