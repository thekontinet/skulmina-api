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
        Role::create(['name' => RoleEnum::TEACHER->value]);
        Role::create(['name' => RoleEnum::STUDENT->value]);

        \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com'
        ])->assignRole(RoleEnum::ADMIN->value);
    }
}
