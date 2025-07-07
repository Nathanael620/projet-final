<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = [
            [
                'name' => 'Alexandre Moreau',
                'email' => 'alexandre.moreau@example.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'skills' => ['Mathématiques', 'Informatique'],
                'level' => 'beginner',
                'bio' => 'Étudiant en première année d\'informatique. Passionné par la programmation.',
                'phone' => '0123456794',
            ],
            [
                'name' => 'Julie Leroy',
                'email' => 'julie.leroy@example.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'skills' => ['Langues', 'Littérature'],
                'level' => 'intermediate',
                'bio' => 'Étudiante en licence de langues étrangères. Intéressée par la traduction.',
                'phone' => '0123456795',
            ],
            [
                'name' => 'Maxime Simon',
                'email' => 'maxime.simon@example.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'skills' => ['Physique', 'Chimie'],
                'level' => 'beginner',
                'bio' => 'Étudiant en sciences. Souhaite améliorer ses compétences en laboratoire.',
                'phone' => '0123456796',
            ],
        ];

        foreach ($students as $student) {
            User::create($student);
        }
    }
}
