<?php

namespace Tests\Feature\Api\V1;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    public function test_create_new_user(): void
    {
        $this->loginAs(RoleEnum::ADMIN)->post(route('users.store'), [
            'name' => 'test name',
            'email' => fake()->email,
            'password' => 'password',
            'role' => 'teacher'
        ])->assertSuccessful();

        $user = User::where(['name' => 'test name'])->first();

        $this->assertDatabaseHas(User::class, ['name' => 'test name']);
        $this->assertTrue($user->hasRole('teacher'));
    }

    public function test_only_admin_can_create_new_user(): void
    {
        $this->loginAs(RoleEnum::TEACHER)->post(route('users.store'), [
            'name' => 'test name',
            'email' => fake()->email,
            'password' => 'password',
            'role' => 'teacher'
        ])->assertForbidden();
    }

    public function test_can_update_existing_user(): void
    {
        $user = User::factory()->create();

        $this->loginAs(RoleEnum::ADMIN)->put(route('users.update', $user), [
            'name' => 'test name',
            'email' => 'test@email.com',
            'password' => 'password',
            'role' => 'teacher'
        ])->assertSuccessful();

        $user = User::where([
            'name' => 'test name',
            'email' => 'test@email.com',
        ])->first();

        $this->assertDatabaseHas(User::class, ['name' => 'test name']);
        $this->assertTrue($user->hasRole('teacher'));
    }

    public function test_can_show_existing_user(): void
    {
        $user = User::factory()->create();

        $this->loginAs(RoleEnum::STUDENT)->get(route('users.show', $this->user))
            ->assertSuccessful();
    }

    public function test_can_delete_existing_user(): void
    {
        $user = User::factory()->create();

        $this->loginAs(RoleEnum::ADMIN)->delete(route('users.destroy', $user))
            ->assertSuccessful();

        $this->assertDatabaseMissing(User::class, [
            'id' => $user->id
        ]);
    }

    public function test_cannot_delete_existing_user_if_not_admin(): void
    {
        $user = User::factory()->create();

        $this->loginAs(RoleEnum::TEACHER)->delete(route('users.destroy', $user))
            ->assertForbidden();

        $this->assertDatabaseHas(User::class, [
            'id' => $user->id
        ]);
    }
}
