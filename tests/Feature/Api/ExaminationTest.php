<?php

namespace Tests\Feature\Api;

use App\Enums\RoleEnum;
use App\Models\Examination;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExaminationTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    public function setUp() :void
    {
        parent::setUp();
        foreach (RoleEnum::cases() as $case) {
            Role::create(['name' => $case->value]);
        }

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_can_get_all_examinations()
    {
        $this->user->assignRole(RoleEnum::TEACHER->value);
        $examinations = Examination::factory(2)->create([
            'user_id' => $this->user->id
        ]);

        $this->actingAs($this->user);

        $response = $this->get(route('examinations.index'));

        $response->assertSuccessful();
        $response->assertSeeInOrder($examinations->pluck('title')->toArray());
    }

    public function test_student_can_get_all_examinations_available_for_them()
    {
        $this->user->assignRole(RoleEnum::STUDENT->value);
        $examinations = Examination::factory(2)->create([
            'user_id' => $this->user->id
        ]);

        $this->user->reserveSeatFor($examinations->first());

        $this->actingAs($this->user);

        $response = $this->get(route('examinations.index'));

        $response->assertSuccessful();
        $response->assertSee($examinations->first()->title);
        $response->assertDontSee($examinations->last()->title);
    }


    public function test_can_create_new_examinations(): void
    {
        $this->user->assignRole(RoleEnum::TEACHER->value);

        $response = $this->post(
            route('examinations.store'),
            Examination::factory()->make()->toArray()
        );

        $response->assertCreated();
        $this->assertDatabaseCount(Examination::class, 1);
    }

    public function test_cannot_create_new_examinations_for_other_teachers(): void
    {
        $this->user->assignRole(RoleEnum::TEACHER->value);
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
        $this->user->assignRole(RoleEnum::STUDENT->value);

        $response = $this->post(
            route('examinations.store'),
            Examination::factory()->make()->toArray()
        );

        $response->assertForbidden();
        $this->assertDatabaseEmpty(Examination::class);
    }

    public function test_can_update_examinations(): void
    {
        $this->user->assignRole(RoleEnum::TEACHER->value);

        $examination = Examination::factory()->create([
            'user_id' => $this->user->id
        ]);
        $newExamination = (array) Examination::factory()->make([
            'user_id' => $this->user->id
        ])->toArray();

        $this->actingAs($this->user);
        $response = $this->put(
            route('examinations.update', $examination),
            $newExamination
        );

        $response->assertSuccessful();
        $this->assertDatabaseHas(Examination::class, $newExamination);
    }

    public function test_that_author_cannot_update_another_authors_examinations(): void
    {
        $this->user->assignRole(RoleEnum::TEACHER->value);

        $examination = Examination::factory()->create();
        $newExamination = (array) Examination::factory()->make()->toArray();

        $this->actingAs($this->user);

        $response = $this->put(
            route('examinations.update', $examination),
            $newExamination
        );

        $response->assertForbidden();
    }

    public function test_can_get_examination_data()
    {
        $this->user->assignRole(RoleEnum::TEACHER->value);
        $examination = Examination::factory()->create();

        $this->actingAs($this->user);

        $response = $this->get(route('examinations.show', $examination) );

        $response->assertSuccessful();
        $response->assertJsonFragment($examination->pluck('title')->toArray());
    }

    public function test_that_exam_data_is_available_for_seated_students()
    {
        $examination = Examination::factory()->create();
        $this->user->assignRole(RoleEnum::ADMIN->value);
        $this->user->reserveSeatFor($examination);

        $this->actingAs($this->user);

        $response = $this->get(route('examinations.show', $examination));
        $response->assertSuccessful();
    }

    public function test_that_exam_data_is_unavailable_for_non_seated_students()
    {
        $this->user->assignRole(RoleEnum::STUDENT->value);
        $examination = Examination::factory()->create();

        $this->actingAs($this->user);

        $response = $this->get(route('examinations.show', $examination) );
        $response->assertForbidden();
    }

    public function test_can_delete_examination()
    {
        $this->user->assignRole(RoleEnum::TEACHER->value);
        $examination = Examination::factory()->create([
            'user_id' => $this->user->id
        ]);

        $this->actingAs($this->user);
        $response = $this->delete(route('examinations.destroy', $examination));

        $response->assertSuccessful();
        $this->assertDatabaseMissing(Examination::class, $examination->toArray());
    }

    public function test_that_author_cannot_delete_other_authors_examination()
    {
        $this->user->assignRole(RoleEnum::TEACHER->value);
        $examination = Examination::factory()->create();

        $this->actingAs($this->user);

        $response = $this->delete(route('examinations.destroy', $examination));
        $response->assertForbidden();
        $this->assertDatabaseHas(Examination::class, (array) $examination->only(['title']));
    }
}
