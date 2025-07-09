<?php

namespace App\Console\Commands;

use App\Services\AvatarService;
use Illuminate\Console\Command;

class CleanupOrphanedAvatars extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'avatars:cleanup {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up orphaned avatar files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $avatarService = app(AvatarService::class);
        $isDryRun = $this->option('dry-run');

        $this->info('🔍 Recherche des avatars orphelins...');

        if ($isDryRun) {
            $this->warn('Mode DRY RUN - Aucun fichier ne sera supprimé');
        }

        // Obtenir les statistiques avant nettoyage
        $statsBefore = $avatarService->getAvatarStats();
        $this->info("📊 Avatars avant nettoyage : {$statsBefore['total_avatars']} fichiers ({$statsBefore['total_size_mb']} MB)");

        // Nettoyer les avatars orphelins
        $deletedCount = $avatarService->cleanupOrphanedAvatars();

        // Obtenir les statistiques après nettoyage
        $statsAfter = $avatarService->getAvatarStats();

        if ($deletedCount > 0) {
            $this->info("✅ {$deletedCount} avatars orphelins ont été supprimés");
            $this->info("📊 Avatars après nettoyage : {$statsAfter['total_avatars']} fichiers ({$statsAfter['total_size_mb']} MB)");
            
            $savedSpace = $statsBefore['total_size_mb'] - $statsAfter['total_size_mb'];
            $this->info("💾 Espace libéré : {$savedSpace} MB");
        } else {
            $this->info('✅ Aucun avatar orphelin trouvé');
        }

        // Afficher les statistiques détaillées
        $this->newLine();
        $this->info('📈 Statistiques détaillées :');
        $this->table(
            ['Métrique', 'Valeur'],
            [
                ['Avatars actifs', $statsAfter['total_avatars']],
                ['Taille totale', $statsAfter['total_size_mb'] . ' MB'],
                ['Taille moyenne', $statsAfter['average_size_kb'] . ' KB'],
            ]
        );

        return Command::SUCCESS;
    }
} 