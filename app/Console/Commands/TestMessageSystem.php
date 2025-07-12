<?php

namespace App\Console\Commands;

use App\Models\Message;
use App\Models\User;
use App\Services\MessageNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TestMessageSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:test {--user-id=} {--action=stats}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test du système de messagerie';

    private MessageNotificationService $notificationService;

    public function __construct(MessageNotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $action = $this->option('action');
        $userId = $this->option('user-id');

        $this->info('🧪 Test du système de messagerie');
        $this->newLine();

        switch ($action) {
            case 'stats':
                $this->testStats($userId);
                break;
            case 'send':
                $this->testSendMessage($userId);
                break;
            case 'notifications':
                $this->testNotifications($userId);
                break;
            case 'cleanup':
                $this->testCleanup();
                break;
            case 'conversations':
                $this->testConversations($userId);
                break;
            default:
                $this->error("Action inconnue: {$action}");
                $this->info('Actions disponibles: stats, send, notifications, cleanup, conversations');
                return 1;
        }

        return 0;
    }

    /**
     * Tester les statistiques
     */
    private function testStats(?int $userId): void
    {
        $this->info('📊 Test des statistiques de messagerie');
        
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("Utilisateur {$userId} non trouvé");
                return;
            }
            $users = collect([$user]);
        } else {
            $users = User::whereIn('role', ['student', 'tutor'])->take(5)->get();
        }

        foreach ($users as $user) {
            $this->newLine();
            $this->info("Utilisateur: {$user->name} ({$user->email})");
            
            $stats = $this->notificationService->getUserMessageStats($user);
            
            $this->table(
                ['Métrique', 'Valeur'],
                [
                    ['Messages envoyés', $stats['total_sent']],
                    ['Messages reçus', $stats['total_received']],
                    ['Messages non lus', $stats['unread_count']],
                    ['Conversations', $stats['conversations_count']],
                    ['Messages ce mois', $stats['messages_this_month']],
                ]
            );
        }
    }

    /**
     * Tester l'envoi de message
     */
    private function testSendMessage(?int $userId): void
    {
        $this->info('📤 Test d\'envoi de message');
        
        if (!$userId) {
            $this->error('--user-id requis pour cette action');
            return;
        }

        $user = User::find($userId);
        if (!$user) {
            $this->error("Utilisateur {$userId} non trouvé");
            return;
        }

        // Trouver un autre utilisateur pour envoyer un message
        $otherUser = User::where('id', '!=', $userId)
            ->whereIn('role', ['student', 'tutor'])
            ->first();

        if (!$otherUser) {
            $this->error('Aucun autre utilisateur trouvé');
            return;
        }

        $content = "Message de test envoyé le " . now()->format('d/m/Y H:i:s');
        
        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $otherUser->id,
            'content' => $content,
            'type' => 'text',
        ]);

        $this->info("✅ Message envoyé avec succès");
        $this->info("De: {$user->name}");
        $this->info("À: {$otherUser->name}");
        $this->info("Contenu: {$content}");
        $this->info("ID: {$message->id}");

        // Tester les notifications
        $this->notificationService->notifyNewMessage($message);
        $this->info("✅ Notification envoyée");
    }

    /**
     * Tester les notifications
     */
    private function testNotifications(?int $userId): void
    {
        $this->info('🔔 Test des notifications');
        
        if (!$userId) {
            $this->error('--user-id requis pour cette action');
            return;
        }

        $user = User::find($userId);
        if (!$user) {
            $this->error("Utilisateur {$userId} non trouvé");
            return;
        }

        // Trouver un message récent
        $message = Message::where('receiver_id', $user->id)
            ->where('is_read', false)
            ->first();

        if (!$message) {
            $this->info('Aucun message non lu trouvé, création d\'un message de test...');
            
            $sender = User::where('id', '!=', $userId)
                ->whereIn('role', ['student', 'tutor'])
                ->first();

            if (!$sender) {
                $this->error('Aucun expéditeur trouvé');
                return;
            }

            $message = Message::create([
                'sender_id' => $sender->id,
                'receiver_id' => $user->id,
                'content' => 'Message de test pour les notifications - ' . now()->format('d/m/Y H:i:s'),
                'type' => 'text',
            ]);
        }

        $this->info("Test de notification pour le message {$message->id}");
        $this->notificationService->notifyNewMessage($message);
        $this->info("✅ Notification traitée");
    }

    /**
     * Tester le nettoyage
     */
    private function testCleanup(): void
    {
        $this->info('🧹 Test du nettoyage des anciens messages');
        
        $oldMessagesCount = Message::where('created_at', '<', now()->subMonths(6))
            ->where('is_read', true)
            ->count();

        $this->info("Messages anciens trouvés: {$oldMessagesCount}");
        
        if ($oldMessagesCount > 0) {
            if ($this->confirm('Voulez-vous supprimer ces anciens messages ?')) {
                $this->notificationService->cleanupOldNotifications();
                $this->info("✅ Nettoyage effectué");
            }
        } else {
            $this->info("Aucun message ancien à supprimer");
        }
    }

    /**
     * Tester les conversations
     */
    private function testConversations(?int $userId): void
    {
        $this->info('💬 Test des conversations récentes');
        
        if (!$userId) {
            $this->error('--user-id requis pour cette action');
            return;
        }

        $user = User::find($userId);
        if (!$user) {
            $this->error("Utilisateur {$userId} non trouvé");
            return;
        }

        $conversations = $this->notificationService->getRecentConversations($user, 5);
        
        if (empty($conversations)) {
            $this->info('Aucune conversation trouvée');
            return;
        }

        $this->info("Conversations récentes pour {$user->name}:");
        
        foreach ($conversations as $conversation) {
            $this->newLine();
            $this->info("👤 {$conversation['user']->name} ({$conversation['user']->role})");
            $this->info("   Dernier message: {$conversation['last_message']->content}");
            $this->info("   Messages non lus: {$conversation['unread_count']}");
            $this->info("   Total messages: {$conversation['total_messages']}");
            $this->info("   Dernière activité: {$conversation['last_message_time']->diffForHumans()}");
        }
    }
} 