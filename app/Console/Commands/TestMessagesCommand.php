<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Message;
use Illuminate\Console\Command;

class TestMessagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:messages {--user1=1} {--user2=2}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tester le système de messages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user1Id = $this->option('user1');
        $user2Id = $this->option('user2');

        $user1 = User::find($user1Id);
        $user2 = User::find($user2Id);

        if (!$user1 || !$user2) {
            $this->error("Utilisateurs non trouvés. Vérifiez les IDs fournis.");
            return 1;
        }

        $this->info("Test du système de messages entre {$user1->name} et {$user2->name}");

        // Créer quelques messages de test
        $messages = [
            [
                'sender_id' => $user1->id,
                'receiver_id' => $user2->id,
                'content' => 'Bonjour ! Comment allez-vous ?',
                'created_at' => now()->subMinutes(10)
            ],
            [
                'sender_id' => $user2->id,
                'receiver_id' => $user1->id,
                'content' => 'Très bien, merci ! Et vous ?',
                'created_at' => now()->subMinutes(8)
            ],
            [
                'sender_id' => $user1->id,
                'receiver_id' => $user2->id,
                'content' => 'Parfait ! Avez-vous des questions sur le cours ?',
                'created_at' => now()->subMinutes(5)
            ],
            [
                'sender_id' => $user2->id,
                'receiver_id' => $user1->id,
                'content' => 'Oui, j\'aimerais clarifier quelques points.',
                'created_at' => now()->subMinutes(3)
            ],
            [
                'sender_id' => $user1->id,
                'receiver_id' => $user2->id,
                'content' => 'Bien sûr ! Je suis là pour vous aider.',
                'created_at' => now()->subMinute()
            ]
        ];

        $createdCount = 0;
        foreach ($messages as $messageData) {
            try {
                Message::create($messageData);
                $createdCount++;
                $this->line("✓ Message créé: {$messageData['content']}");
            } catch (\Exception $e) {
                $this->error("✗ Erreur lors de la création du message: " . $e->getMessage());
            }
        }

        // Afficher les statistiques
        $totalMessages = Message::count();
        $unreadMessages = Message::where('is_read', false)->count();
        $conversations = Message::selectRaw('DISTINCT CASE 
            WHEN sender_id = ? THEN receiver_id 
            ELSE sender_id 
        END as other_user_id', [$user1->id])
        ->where('sender_id', $user1->id)
        ->orWhere('receiver_id', $user1->id)
        ->count();

        $this->info("\n📊 Statistiques:");
        $this->line("Total des messages: {$totalMessages}");
        $this->line("Messages non lus: {$unreadMessages}");
        $this->line("Conversations de {$user1->name}: {$conversations}");
        $this->line("Messages créés dans ce test: {$createdCount}");

        $this->info("\n✅ Test terminé ! Vous pouvez maintenant tester l'interface web.");
        return 0;
    }
} 