<?php

namespace Tests;

use App\Enums\RoleEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected User | null $user;

    public function setUp() :void
    {
        parent::setUp();
        foreach (RoleEnum::cases() as $case) {
            Role::create(['name' => $case->value]);
        }
        $this->login();
    }

    public function login()
    {
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        return $this;
    }

    public function loginAs(RoleEnum $role)
    {
        /** @var User */
        $user = $this->user ?? User::factory()->create();
        $this->actingAs($user);
        $user->assignRole($role->value);
        return $this;
    }
}
