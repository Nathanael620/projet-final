<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TutorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tutors = [
            [
                'name' => 'Marie Dubois',
                'email' => 'marie.dubois@example.com',
                'password' => Hash::make('password'),
                'role' => 'tutor',
                'skills' => ['Mathématiques', 'Physique', 'Statistiques'],
                'level' => 'advanced',
                'bio' => 'Professeure de mathématiques avec 10 ans d\'expérience. Spécialisée en algèbre et géométrie.',
                'hourly_rate' => 25.00,
                'rating' => 4.8,
                'total_sessions' => 45,
                'is_available' => true,
                'phone' => '0123456789',
            ],
            [
                'name' => 'Thomas Martin',
                'email' => 'thomas.martin@example.com',
                'password' => Hash::make('password'),
                'role' => 'tutor',
                'skills' => ['Informatique', 'Java', 'Python', 'Web Development'],
                'level' => 'advanced',
                'bio' => 'Développeur senior passionné par l\'enseignement. Expert en programmation orientée objet.',
                'hourly_rate' => 30.00,
                'rating' => 4.9,
                'total_sessions' => 67,
                'is_available' => true,
                'phone' => '0123456790',
            ],
            [
                'name' => 'Sophie Bernard',
                'email' => 'sophie.bernard@example.com',
                'password' => Hash::make('password'),
                'role' => 'tutor',
                'skills' => ['Anglais', 'Espagnol', 'Littérature'],
                'level' => 'intermediate',
                'bio' => 'Professeure de langues vivantes. Native anglophone avec expérience en enseignement.',
                'hourly_rate' => 22.00,
                'rating' => 4.7,
                'total_sessions' => 32,
                'is_available' => true,
                'phone' => '0123456791',
            ],
            [
                'name' => 'Lucas Petit',
                'email' => 'lucas.petit@example.com',
                'password' => Hash::make('password'),
                'role' => 'tutor',
                'skills' => ['Chimie', 'Biologie', 'Sciences'],
                'level' => 'advanced',
                'bio' => 'Doctorant en chimie organique. Passionné par les sciences expérimentales.',
                'hourly_rate' => 28.00,
                'rating' => 4.6,
                'total_sessions' => 23,
                'is_available' => true,
                'phone' => '0123456792',
            ],
            [
                'name' => 'Emma Roux',
                'email' => 'emma.roux@example.com',
                'password' => Hash::make('password'),
                'role' => 'tutor',
                'skills' => ['Histoire', 'Géographie', 'Éducation civique'],
                'level' => 'intermediate',
                'bio' => 'Étudiante en master d\'histoire. Spécialisée en histoire contemporaine.',
                'hourly_rate' => 18.00,
                'rating' => 4.5,
                'total_sessions' => 18,
                'is_available' => true,
                'phone' => '0123456793',
            ],
        ];

        foreach ($tutors as $tutor) {
            User::create($tutor);
        }
    }
}
