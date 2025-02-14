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
                'id' => 2,
                'ministry_id' => 1, // Assign to ministry with ID 1
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => 'testuser@example.com',
                'password' => bcrypt('password'), // Use bcrypt to hash password
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'ministry_id' => 1, // Assign to ministry with ID 1
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'ministry_id' => 2, // Assign to ministry with ID 2
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'email' => 'jane.smith@example.com',
                'password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'ministry_id' => 2, // Assign to ministry with ID 2
                'first_name' => 'Alice',
                'last_name' => 'Johnson',
                'email' => 'alice.johnson@example.com',
                'password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'ministry_id' => 3, // Assign to ministry with ID 3
                'first_name' => 'Bob',
                'last_name' => 'Brown',
                'email' => 'bob.brown@example.com',
                'password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
