<?php

namespace Database\Seeders;

use App\Models\FAQ;
use App\Models\User;
use Illuminate\Database\Seeder;

class FAQSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->warn('Aucun utilisateur trouvé. Créez d\'abord des utilisateurs.');
            return;
        }

        $faqs = [
            [
                'question' => 'Comment fonctionne le système de paiement ?',
                'answer' => 'Les paiements se font via Mobile Money (MOMO) et Orange Money. Une commission de 10% est prélevée sur chaque transaction pour maintenir la plateforme. Le paiement est sécurisé et traité automatiquement.',
                'category' => 'payment',
                'is_public' => true,
            ],
            [
                'question' => 'Comment sont sélectionnés les tuteurs ?',
                'answer' => 'Les tuteurs sont des étudiants ou enseignants qui ont prouvé leurs compétences dans leurs domaines. Ils sont évalués par la communauté et doivent maintenir une note moyenne de 4.0/5 pour rester actifs.',
                'category' => 'general',
                'is_public' => true,
            ],
            [
                'question' => 'Puis-je annuler une séance ?',
                'answer' => 'Oui, vous pouvez annuler une séance jusqu\'à 24h avant le début sans frais. Entre 24h et 2h avant, une commission de 50% s\'applique. Moins de 2h avant, le paiement complet est dû.',
                'category' => 'sessions',
                'is_public' => true,
            ],
            [
                'question' => 'Comment se déroule une séance en visioconférence ?',
                'answer' => 'Un lien Zoom est généré automatiquement et envoyé aux participants 15 minutes avant le début. Vous recevez également une notification par email et dans l\'application.',
                'category' => 'sessions',
                'is_public' => true,
            ],
            [
                'question' => 'Comment modifier mon profil ?',
                'answer' => 'Allez dans "Mon profil" depuis le menu déroulant de votre nom. Vous pouvez modifier vos informations personnelles, compétences, disponibilités et tarif horaire.',
                'category' => 'account',
                'is_public' => true,
            ],
            [
                'question' => 'Comment contacter le support ?',
                'answer' => 'Vous pouvez nous contacter via le formulaire de contact sur la page d\'accueil, par email à support@soutiens-moi.com, ou en créant une question dans la section FAQ.',
                'category' => 'general',
                'is_public' => true,
            ],
            [
                'question' => 'Comment fonctionne le système de notation ?',
                'answer' => 'Après chaque séance, vous pouvez noter votre tuteur de 1 à 5 étoiles et laisser un commentaire. Ces évaluations sont publiques et aident les autres étudiants à choisir.',
                'category' => 'general',
                'is_public' => true,
            ],
            [
                'question' => 'Puis-je devenir tuteur ?',
                'answer' => 'Oui ! Si vous êtes étudiant ou enseignant, vous pouvez créer un profil de tuteur. Remplissez vos compétences, expériences et tarif horaire. Votre profil sera validé sous 48h.',
                'category' => 'account',
                'is_public' => true,
            ],
            [
                'question' => 'Comment sécuriser mon compte ?',
                'answer' => 'Utilisez un mot de passe fort, activez l\'authentification à deux facteurs si disponible, et ne partagez jamais vos identifiants. Changez régulièrement votre mot de passe.',
                'category' => 'account',
                'is_public' => true,
            ],
            [
                'question' => 'Que faire en cas de problème technique ?',
                'answer' => 'Vérifiez d\'abord votre connexion internet et rafraîchissez la page. Si le problème persiste, contactez le support avec une capture d\'écran et une description détaillée.',
                'category' => 'technical',
                'is_public' => true,
            ],
        ];

        foreach ($faqs as $faqData) {
            FAQ::create([
                'user_id' => $users->random()->id,
                'question' => $faqData['question'],
                'answer' => $faqData['answer'],
                'category' => $faqData['category'],
                'is_public' => $faqData['is_public'],
                'status' => 'answered',
                'votes' => rand(0, 15),
                'is_featured' => rand(0, 1),
            ]);
        }

        $this->command->info('FAQ créées avec succès !');
    }
} 