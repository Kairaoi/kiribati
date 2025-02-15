<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'ministry_id' => 1, // Assign to ministry with ID 1
                'name' => 'User',
                'email' => 'user@example.com',
                'password' => bcrypt('password'), // Use bcrypt to hash password
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'ministry_id' => 1, // Assign to ministry with ID 1
                'name' => 'Test User',
                'email' => 'testuser@example.com',
                'password' => bcrypt('password'), // Use bcrypt to hash password
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'ministry_id' => 1, // Assign to ministry with ID 1
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'ministry_id' => 2, // Assign to ministry with ID 2
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'ministry_id' => 2, // Assign to ministry with ID 2
                'name' => 'Alice Johnson',
                'email' => 'alice.johnson@example.com',
                'password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'ministry_id' => 3, // Assign to ministry with ID 3
                'name' => 'Bob Brown',
                'email' => 'bob.brown@example.com',
                'password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
