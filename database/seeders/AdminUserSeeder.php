<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@soutiens-moi.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'level' => 'advanced',
            'is_available' => true,
            'email_verified_at' => now(),
        ]);
    }
}
