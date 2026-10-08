<?php

namespace Database\Seeders;

use App\Enums\Type;
use Illuminate\Database\Seeder;
use App\Models\User; // Import the User model
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@phoenixcoded.com',
            'phone_number' => '7894561230',
            'status' => 1,
            'password' => 12345678,
            'type' => Type::ADMIN
        ]);

        // Generate 10 fake users
        // User::factory()->count(100)->create();
    }
}
