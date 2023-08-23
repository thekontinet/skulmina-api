<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Enums\RoleEnum;
use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        Role::create(['name' => RoleEnum::ADMIN->value]);
        \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'test@example.com'
        ])->assignRole(RoleEnum::ADMIN->value);
    }
}
