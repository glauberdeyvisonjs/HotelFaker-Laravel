<?php

namespace Database\Seeders;

use App\Models\User;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run(): void
    {
        for ($i = 0; $i < 50; $i++) {
            User::create([
                'name' => fake()->name(),
                'email' => fake()->safeEmail(),
                'cpf' => '123123' . random_int(10000, 99999),
                'email_verified_at' => now(),
                'password' => Hash::make(random_int(100000, 999999)),
            ]);
        }
    }
}
