<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class TestNotificationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notifications {--user=1} {--type=all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tester le système de notifications';

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user');
        $type = $this->option('type');

        $user = User::find($userId);
        if (!$user) {
            $this->error("Utilisateur avec l'ID {$userId} non trouvé.");
            return 1;
        }

        $this->info("Test des notifications pour l'utilisateur: {$user->name}");

        if ($type === 'all' || $type === 'message') {
            $this->testMessageNotifications($user);
        }

        if ($type === 'all' || $type === 'session') {
            $this->testSessionNotifications($user);
        }

        if ($type === 'all' || $type === 'payment') {
            $this->testPaymentNotifications($user);
        }

        $this->info("Test terminé. Vérifiez les notifications dans l'interface web.");
        return 0;
    }

    private function testMessageNotifications(User $user)
    {
        $this->info("Création de notifications de messages...");

        // Créer un utilisateur fictif pour les tests
        $sender = User::where('id', '!=', $user->id)->first();
        if (!$sender) {
            $this->warn("Aucun autre utilisateur trouvé pour les tests de messages.");
            return;
        }

        // Notification de nouveau message
        $this->notificationService->notifyNewMessage(
            $user,
            $sender,
            "Ceci est un message de test pour vérifier le système de notifications."
        );

        // Notification de message modifié
        $this->notificationService->notifyMessageEdited(
            $user,
            $sender,
            "Ce message a été modifié pour tester les notifications."
        );

        $this->info("✓ 2 notifications de messages créées");
    }

    private function testSessionNotifications(User $user)
    {
        $this->info("Création de notifications de séances...");

        $sender = User::where('id', '!=', $user->id)->first();
        if (!$sender) {
            $this->warn("Aucun autre utilisateur trouvé pour les tests de séances.");
            return;
        }

        // Notification de nouvelle séance
        $this->notificationService->notifyNewSession(
            $user,
            $sender,
            [
                'id' => 1,
                'subject' => 'Mathématiques',
                'date' => now()->addDays(2)->format('d/m/Y'),
                'time' => '14:00',
                'duration' => 60
            ]
        );

        // Notification de séance annulée
        $this->notificationService->notifySessionCancelled(
            $user,
            $sender,
            [
                'id' => 2,
                'subject' => 'Physique',
                'date' => now()->addDays(1)->format('d/m/Y'),
                'time' => '16:00',
                'duration' => 90
            ]
        );

        $this->info("✓ 2 notifications de séances créées");
    }

    private function testPaymentNotifications(User $user)
    {
        $this->info("Création de notifications de paiements...");

        // Notification de paiement
        $this->notificationService->notifyPayment(
            $user,
            [
                'id' => 1,
                'amount' => 50.00,
                'currency' => 'EUR',
                'method' => 'Carte bancaire',
                'status' => 'completed'
            ]
        );

        $this->info("✓ 1 notification de paiement créée");
    }
} 