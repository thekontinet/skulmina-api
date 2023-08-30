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
        $examinations = Examination::factory(2)->create([
            'user_id' => $this->user->id
        ]);

        $this->loginAs(RoleEnum::TEACHER)->get(route('examinations.index'))
            ->assertSuccessful()
            ->assertSeeInOrder($examinations->pluck('title')->toArray());
    }

    public function test_can_only_see_invited_exams_if_role_is_student()
    {
        $examinations = Examination::factory(2)->create();
        $this->user->inviteTo($examinations[0]);

        $this->loginAs(RoleEnum::STUDENT)->get(route('examinations.index'))
            ->assertSuccessful()
            ->assertSee($examinations->first()->title)
            ->assertDontSee($examinations->last()->title);
    }


    public function test_can_create_new_examinations_if_role_is_teacher(): void
    {
        $this->loginAs(RoleEnum::TEACHER)->post(
            route('examinations.store'),
            Examination::factory()->make()->toArray()
        )->assertCreated();

        $this->assertDatabaseHas(Examination::class, [
            'user_id' => $this->user->id
        ]);
    }

    public function test_cannot_create_new_examination_if_role_is_student(): void
    {
        $this->loginAs(RoleEnum::STUDENT)->post(
            route('examinations.store'),
            Examination::factory()->make()->toArray()
        )->assertForbidden();
    }

    public function test_cannot_create_new_examinations_for_other_creators(): void
    {
        $this->loginAs(RoleEnum::TEACHER)->post(
            route('examinations.store'),
            Examination::factory()->make([
                'user_id' => 2
            ])->toArray()
        )->assertCreated();

        $this->assertDatabaseCount(Examination::class, 1)
        ->assertDatabaseHas(Examination::class, ['user_id' => $this->user->id]);
    }

    public function test_can_update_examinations(): void
    {
        $examination = Examination::factory()->create([
            'user_id' => $this->user->id
        ]);
        $newExamination = Examination::factory()->make();

        $this->loginAs(RoleEnum::TEACHER)->put(
            route('examinations.update', $examination),
            $newExamination->toArray()
        )->assertSuccessful();

        $this->assertDatabaseHas(
            Examination::class,
            collect($newExamination)->except(['user_id'])->toArray()
        );
    }

    public function test_cannot_update_examination_if_not_creator(): void
    {
        $examination = Examination::factory()->create();
        $newExamination = (array) Examination::factory()->make()->toArray();

        $this->loginAs(RoleEnum::TEACHER)->put(
            route('examinations.update', $examination),
            $newExamination
        )->assertNotFound();
    }

    public function test_can_show_single_examination()
    {
        $examination = Examination::factory()->create([
            'user_id' => $this->user->id
        ]);

        $this->loginAs(RoleEnum::TEACHER)->get(route('examinations.show', $examination))
                ->assertSuccessful()
                ->assertJsonFragment($examination->pluck('title')->toArray());
    }

    public function test_can_show_single_examination_to_student_only_if_invited()
    {
        $examination = Examination::factory(2)->create();
        $this->loginAs(RoleEnum::STUDENT);
        $this->user->inviteTo($examination->first());

        $this->get(route('examinations.show', $examination->first()))
            ->assertSuccessful();

        $this->get(route('examinations.show', $examination->last()))
            ->assertNotFound();
    }

    public function test_can_delete_examination_if_creator()
    {
        $examination = Examination::factory()->create([
            'user_id' => $this->user->id
        ]);

        $this->loginAs(RoleEnum::TEACHER)->delete(route('examinations.destroy', $examination))
            ->assertSuccessful();
        $this->assertDatabaseMissing(Examination::class, $examination->toArray());
    }

    public function test_cannot_delete_exam_if_not_creator()
    {
        $examination = Examination::factory()->create();

        $this->loginAs(RoleEnum::TEACHER)->delete(route('examinations.destroy', $examination))
            ->assertNotFound();
    }
}
