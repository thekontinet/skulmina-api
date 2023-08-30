<?php

namespace Tests\Feature\Auth;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_can_register(): void
    {
        $this->loginAs(RoleEnum::ADMIN);
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'teacher',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertNoContent();
        $this->assertDatabaseHas(User::class, ['name' => 'Test User']);
    }
}
