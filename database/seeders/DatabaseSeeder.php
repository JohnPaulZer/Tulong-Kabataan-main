<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        DB::table('admin_accounts')->insert([
            [
                'admin_id' => 1,
                'username' => 'admin',
                'email' => 'admin@tulongkabataan.com',
                'password' => Hash::make('admin123'), // Bcrypt hashed password
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more admin accounts if needed
        ]);
    }
}
