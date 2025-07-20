<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Feedbacks;
use App\Models\Session;
use App\Models\User;

class FeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les séances terminées
        $completedSessions = Session::where('status', 'completed')->get();
        
        if ($completedSessions->isEmpty()) {
            $this->command->info('Aucune séance terminée trouvée. Création de séances de test...');
            
            // Créer quelques séances terminées pour les tests
            $tutors = User::where('role', 'tutor')->take(3)->get();
            $students = User::where('role', 'student')->take(5)->get();
            
            foreach ($tutors as $tutor) {
                foreach ($students->take(2) as $student) {
                    $session = Session::create([
                        'student_id' => $student->id,
                        'tutor_id' => $tutor->id,
                        'title' => 'Séance de ' . $tutor->getSkillsString(),
                        'description' => 'Séance de soutien en ' . $tutor->getSkillsString(),
                        'subject' => $tutor->skills[0] ?? 'mathematics',
                        'level' => 'intermediate',
                        'type' => 'online',
                        'status' => 'completed',
                        'scheduled_at' => now()->subDays(rand(1, 30)),
                        'duration_minutes' => 60,
                        'price' => $tutor->hourly_rate ?? 20,
                    ]);
                    
                    $completedSessions->push($session);
                }
            }
        }
        
        $this->command->info('Création des feedbacks de test...');
        
        foreach ($completedSessions as $session) {
            // Feedback de l'étudiant vers le tuteur
            if (rand(1, 10) <= 8) { // 80% de chance d'avoir un feedback
                Feedbacks::create([
                    'session_id' => $session->id,
                    'reviewer_id' => $session->student_id,
                    'reviewed_id' => $session->tutor_id,
                    'rating' => rand(3, 5), // Généralement de bonnes notes
                    'comment' => $this->getRandomStudentComment(),
                    'type' => 'student_to_tutor',
                    'is_anonymous' => rand(1, 10) <= 2, // 20% de chance d'être anonyme
                    'created_at' => $session->scheduled_at->addHours(rand(1, 24)),
                ]);
            }
            
            // Feedback du tuteur vers l'étudiant
            if (rand(1, 10) <= 6) { // 60% de chance d'avoir un feedback
                Feedbacks::create([
                    'session_id' => $session->id,
                    'reviewer_id' => $session->tutor_id,
                    'reviewed_id' => $session->student_id,
                    'rating' => rand(3, 5), // Généralement de bonnes notes
                    'comment' => $this->getRandomTutorComment(),
                    'type' => 'tutor_to_student',
                    'is_anonymous' => rand(1, 10) <= 1, // 10% de chance d'être anonyme
                    'created_at' => $session->scheduled_at->addHours(rand(1, 24)),
                ]);
            }
        }
        
        // Mettre à jour les notes moyennes des utilisateurs
        $this->command->info('Mise à jour des notes moyennes...');
        
        $users = User::all();
        foreach ($users as $user) {
            $averageRating = $user->getAverageRating();
            $totalSessions = $user->receivedFeedbacks()->count();
            
            $user->update([
                'rating' => $averageRating,
                'total_sessions' => $totalSessions,
            ]);
        }
        
        $this->command->info('Feedbacks créés avec succès !');
    }
    
    /**
     * Obtenir un commentaire aléatoire d'étudiant
     */
    private function getRandomStudentComment(): string
    {
        $comments = [
            'Très bon tuteur, explications claires et patient.',
            'Séance très utile, j\'ai bien compris les concepts.',
            'Excellent pédagogue, je recommande vivement.',
            'Approche pédagogique adaptée à mon niveau.',
            'Très satisfait de cette séance de soutien.',
            'Le tuteur a su m\'aider efficacement.',
            'Explications détaillées et méthodiques.',
            'Bonne ambiance de travail, séance productive.',
            'Progression notable grâce à cette séance.',
            'Tuteur compétent et à l\'écoute.',
        ];
        
        return $comments[array_rand($comments)];
    }
    
    /**
     * Obtenir un commentaire aléatoire de tuteur
     */
    private function getRandomTutorComment(): string
    {
        $comments = [
            'Étudiant motivé et attentif.',
            'Bonne participation pendant la séance.',
            'Progrès notables, continuez ainsi.',
            'Étudiant sérieux et travailleur.',
            'Bonne compréhension des concepts.',
            'Participation active et constructive.',
            'Étudiant ponctuel et respectueux.',
            'Bonne progression dans l\'apprentissage.',
            'Étudiant curieux et impliqué.',
            'Séance productive et enrichissante.',
        ];
        
        return $comments[array_rand($comments)];
    }
} 