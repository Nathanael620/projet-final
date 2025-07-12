<?php

namespace App\Console\Commands;

use App\Services\FAQIntelligenceService;
use Illuminate\Console\Command;

class TestAISystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'faq:test-ai {--question= : Question spécifique à tester}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Teste le système d\'intelligence artificielle FAQ';

    private FAQIntelligenceService $aiService;

    public function __construct(FAQIntelligenceService $aiService)
    {
        parent::__construct();
        $this->aiService = $aiService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧠 Test du système d\'intelligence artificielle FAQ');
        $this->newLine();

        // Test de configuration
        $this->testConfiguration();

        // Test de génération de réponse
        $this->testAnswerGeneration();

        // Test de recherche
        $this->testSearch();

        // Test d'analyse de sentiment
        $this->testSentimentAnalysis();

        // Test de suggestions
        $this->testSuggestions();

        $this->newLine();
        $this->info('✅ Tests terminés !');
    }

    private function testConfiguration()
    {
        $this->info('📋 Test de configuration...');
        
        $apiKey = config('services.openai.api_key');
        if (empty($apiKey) || $apiKey === 'your-openai-api-key') {
            $this->warn('⚠️  Clé API OpenAI non configurée');
            $this->line('   Le système fonctionnera en mode fallback');
        } else {
            $this->info('✅ Clé API OpenAI configurée');
        }
        
        $this->newLine();
    }

    private function testAnswerGeneration()
    {
        $this->info('🤖 Test de génération de réponse...');
        
        $question = $this->option('question') ?: 'Comment fonctionne le système de paiement ?';
        $this->line("   Question testée : {$question}");
        
        try {
            $startTime = microtime(true);
            $answer = $this->aiService->generateAnswer($question, 'payment');
            $endTime = microtime(true);
            
            if ($answer) {
                $this->info('✅ Réponse générée avec succès');
                $this->line("   Temps de réponse : " . round(($endTime - $startTime) * 1000, 2) . "ms");
                $this->line("   Longueur : " . strlen($answer) . " caractères");
                $this->line("   Aperçu : " . substr($answer, 0, 100) . "...");
            } else {
                $this->warn('⚠️  Aucune réponse générée (mode fallback)');
            }
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de la génération : ' . $e->getMessage());
        }
        
        $this->newLine();
    }

    private function testSearch()
    {
        $this->info('🔍 Test de recherche intelligente...');
        
        $query = 'paiement mobile money';
        $this->line("   Requête testée : {$query}");
        
        try {
            $startTime = microtime(true);
            $results = $this->aiService->intelligentSearch($query, 5);
            $endTime = microtime(true);
            
            $this->info('✅ Recherche effectuée avec succès');
            $this->line("   Temps de recherche : " . round(($endTime - $startTime) * 1000, 2) . "ms");
            $this->line("   Résultats trouvés : " . count($results));
            
            if (!empty($results)) {
                $this->line("   Premier résultat : " . $results[0]->question);
            }
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de la recherche : ' . $e->getMessage());
        }
        
        $this->newLine();
    }

    private function testSentimentAnalysis()
    {
        $this->info('😊 Test d\'analyse de sentiment...');
        
        $texts = [
            'positive' => 'J\'adore cette plateforme, elle est géniale !',
            'negative' => 'Je déteste ce service, il ne fonctionne jamais.',
            'neutral' => 'Comment fonctionne le système ?'
        ];
        
        foreach ($texts as $type => $text) {
            $this->line("   Test {$type} : {$text}");
            
            try {
                $sentiment = $this->aiService->analyzeSentiment($text);
                $this->line("   → Sentiment détecté : {$sentiment['sentiment']} (confiance: {$sentiment['confidence']})");
            } catch (\Exception $e) {
                $this->error("   ❌ Erreur : " . $e->getMessage());
            }
        }
        
        $this->newLine();
    }

    private function testSuggestions()
    {
        $this->info('💡 Test de suggestions d\'amélioration...');
        
        $question = 'Comment payer ?';
        $answer = 'Utilisez le bouton payer.';
        
        $this->line("   Question : {$question}");
        $this->line("   Réponse : {$answer}");
        
        try {
            $suggestions = $this->aiService->suggestImprovements($question, $answer);
            
            $this->info('✅ Suggestions générées');
            $this->line("   Score : {$suggestions['score']}/10");
            
            if (!empty($suggestions['suggestions'])) {
                $this->line("   Suggestions :");
                foreach ($suggestions['suggestions'] as $suggestion) {
                    $this->line("   - {$suggestion}");
                }
            }
            
            if (!empty($suggestions['improvements'])) {
                $this->line("   Améliorations :");
                foreach ($suggestions['improvements'] as $improvement) {
                    $this->line("   - {$improvement}");
                }
            }
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de la génération des suggestions : ' . $e->getMessage());
        }
        
        $this->newLine();
    }
} 