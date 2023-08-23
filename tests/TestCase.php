<?php

namespace Tests;

use App\Enums\RoleEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected User $user;

    public function setUp() :void
    {
        parent::setUp();
        foreach (RoleEnum::cases() as $case) {
            Role::create(['name' => $case->value]);
        }
    }

    public function login()
    {
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function loginAs(RoleEnum $role)
    {
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        $this->user->assignRole($role->value);
    }
}
