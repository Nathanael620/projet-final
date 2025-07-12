<?php

namespace App\Console\Commands;

use App\Models\Session;
use App\Mail\SessionReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class TestSessionReminders extends Command
{
    protected $signature = 'sessions:test-reminders {--email=}';
    protected $description = 'Tester l\'envoi des notifications de rappel pour les séances';

    public function handle()
    {
        $this->info('Test des notifications de rappel pour les séances...');

        // Récupérer une séance confirmée pour le test
        $session = Session::where('status', 'confirmed')
            ->with(['tutor', 'student'])
            ->first();

        if (!$session) {
            $this->error('Aucune séance confirmée trouvée. Créez d\'abord une séance.');
            return;
        }

        $this->info("Séance trouvée : {$session->title}");
        $this->info("Tuteur : {$session->tutor->name} ({$session->tutor->email})");
        $this->info("Étudiant : {$session->student->name} ({$session->student->email})");
        $this->info("Type : {$session->type}");
        $this->info("Date : {$session->scheduled_at->format('d/m/Y H:i')}");

        $email = $this->option('email');
        if ($email) {
            // Envoyer à l'email spécifié
            try {
                Mail::to($email)->send(new SessionReminderMail($session, 'tutor'));
                $this->info("Email de test envoyé à : {$email}");
            } catch (\Exception $e) {
                $this->error("Erreur lors de l'envoi : " . $e->getMessage());
            }
        } else {
            // Demander confirmation pour envoyer aux participants réels
            if ($this->confirm('Voulez-vous envoyer les emails aux participants réels ?')) {
                try {
                    // Envoyer au tuteur
                    Mail::to($session->tutor->email)
                        ->send(new SessionReminderMail($session, 'tutor'));
                    $this->info("Email envoyé au tuteur : {$session->tutor->email}");

                    // Envoyer à l'étudiant
                    Mail::to($session->student->email)
                        ->send(new SessionReminderMail($session, 'student'));
                    $this->info("Email envoyé à l'étudiant : {$session->student->email}");

                    $this->info('Test terminé avec succès !');
                } catch (\Exception $e) {
                    $this->error("Erreur lors de l'envoi : " . $e->getMessage());
                }
            } else {
                $this->info('Test annulé.');
            }
        }
    }
} 