<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = User::where('role', 'student')->get();
        $tutors = User::where('role', 'tutor')->get();

        // Messages entre étudiants et tuteurs
        foreach ($students as $student) {
            foreach ($tutors->take(2) as $tutor) {
                $skill = $student->skills && count($student->skills) > 0 ? $student->skills[0] : 'mathématiques';
                
                // Messages de l'étudiant vers le tuteur
                Message::create([
                    'sender_id' => $student->id,
                    'receiver_id' => $tutor->id,
                    'content' => "Bonjour {$tutor->name}, j'aimerais avoir de l'aide en {$skill}. Êtes-vous disponible ?",
                    'type' => 'text',
                    'is_read' => true,
                    'read_at' => now()->subHours(2),
                    'created_at' => now()->subHours(3),
                ]);

                // Réponse du tuteur
                Message::create([
                    'sender_id' => $tutor->id,
                    'receiver_id' => $student->id,
                    'content' => "Bonjour {$student->name} ! Oui, je peux vous aider. Quel est votre niveau et sur quoi souhaitez-vous travailler exactement ?",
                    'type' => 'text',
                    'is_read' => true,
                    'read_at' => now()->subHours(1),
                    'created_at' => now()->subHours(2),
                ]);

                // Réponse de l'étudiant
                Message::create([
                    'sender_id' => $student->id,
                    'receiver_id' => $tutor->id,
                    'content' => "Je suis niveau {$student->level} et j'ai des difficultés avec les exercices de mon cours. Pouvez-vous m'expliquer ?",
                    'type' => 'text',
                    'is_read' => false,
                    'created_at' => now()->subMinutes(30),
                ]);
            }
        }

        // Messages entre étudiants
        if ($students->count() >= 2) {
            $student1 = $students->first();
            $student2 = $students->skip(1)->first();

            Message::create([
                'sender_id' => $student1->id,
                'receiver_id' => $student2->id,
                'content' => "Salut ! Comment ça va avec tes études ?",
                'type' => 'text',
                'is_read' => true,
                'read_at' => now()->subHours(1),
                'created_at' => now()->subHours(2),
            ]);

            Message::create([
                'sender_id' => $student2->id,
                'receiver_id' => $student1->id,
                'content' => "Ça va bien ! Et toi ? Tu as trouvé un bon tuteur ?",
                'type' => 'text',
                'is_read' => false,
                'created_at' => now()->subMinutes(45),
            ]);
        }

        // Messages entre tuteurs
        if ($tutors->count() >= 2) {
            $tutor1 = $tutors->first();
            $tutor2 = $tutors->skip(1)->first();

            Message::create([
                'sender_id' => $tutor1->id,
                'receiver_id' => $tutor2->id,
                'content' => "Bonjour ! Comment se passe l'enseignement pour toi ?",
                'type' => 'text',
                'is_read' => true,
                'read_at' => now()->subHours(3),
                'created_at' => now()->subHours(4),
            ]);

            Message::create([
                'sender_id' => $tutor2->id,
                'receiver_id' => $tutor1->id,
                'content' => "Très bien ! J'ai plusieurs étudiants motivés. Et toi ?",
                'type' => 'text',
                'is_read' => true,
                'read_at' => now()->subHours(2),
                'created_at' => now()->subHours(3),
            ]);

            Message::create([
                'sender_id' => $tutor1->id,
                'receiver_id' => $tutor2->id,
                'content' => "Pareil ! C'est une belle plateforme d'entraide.",
                'type' => 'text',
                'is_read' => false,
                'created_at' => now()->subMinutes(20),
            ]);
        }
    }
}
