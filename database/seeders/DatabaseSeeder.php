<?php

namespace Database\Seeders;

use App\Models\Lists;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Factories\ListsFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => 'password',
        ]);

        Lists::factory(5)
            ->for($user)
            ->hasTasks(3)
            ->create();



    }
}
