<?php

namespace App\Console\Commands;

use App\Models\Session;
use App\Mail\SessionReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendSessionReminders extends Command
{
    protected $signature = 'sessions:send-reminders';
    protected $description = 'Envoyer les notifications de rappel pour les séances dans les 10 prochaines minutes';

    public function handle()
    {
        $this->info('Envoi des notifications de rappel pour les séances...');

        // Récupérer les séances qui commencent dans les 10 prochaines minutes
        $now = Carbon::now();
        $tenMinutesFromNow = $now->copy()->addMinutes(10);
        
        $sessions = Session::where('status', 'confirmed')
            ->whereBetween('scheduled_at', [$now, $tenMinutesFromNow])
            ->where('reminder_sent', false) // Éviter les doublons
            ->with(['tutor', 'student'])
            ->get();

        $this->info("Trouvé {$sessions->count()} séance(s) nécessitant un rappel.");

        $sentCount = 0;
        foreach ($sessions as $session) {
            try {
                // Envoyer le mail au tuteur
                Mail::to($session->tutor->email)
                    ->send(new SessionReminderMail($session, 'tutor'));

                // Envoyer le mail à l'étudiant
                Mail::to($session->student->email)
                    ->send(new SessionReminderMail($session, 'student'));

                // Marquer comme envoyé
                $session->update(['reminder_sent' => true]);

                $sentCount++;
                $this->info("Rappel envoyé pour la séance #{$session->id} - {$session->title}");
            } catch (\Exception $e) {
                $this->error("Erreur lors de l'envoi du rappel pour la séance #{$session->id}: " . $e->getMessage());
            }
        }

        $this->info("Terminé ! {$sentCount} notification(s) envoyée(s).");
    }
} 