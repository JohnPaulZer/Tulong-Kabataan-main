<?php

namespace Database\Seeders;

use App\Models\AdminAccount;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        if (! AdminAccount::where('username', 'admin')
            ->orWhere('email', 'admin@tulongkabataan.com')
            ->exists()) {
            $password = env('ADMIN_SEED_PASSWORD') ?: Str::random(32);

            AdminAccount::create([
                'username' => 'admin',
                'email' => 'admin@tulongkabataan.com',
                'password' => Hash::make($password),
            ]);
        }
    }
}
