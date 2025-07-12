<?php

require_once 'vendor/autoload.php';

use App\Services\FAQIntelligenceService;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧠 Test du chatbot FAQ\n\n";

// Créer une instance du service IA
$aiService = new FAQIntelligenceService();

// Test 1: Génération de réponse
echo "Test 1: Génération de réponse\n";
$question = "Comment fonctionne le système de paiement ?";
$answer = $aiService->generateAnswer($question, 'payment');
echo "Question: {$question}\n";
echo "Réponse: " . substr($answer, 0, 200) . "...\n\n";

// Test 2: Recherche de questions similaires
echo "Test 2: Recherche de questions similaires\n";
$similarFaqs = $aiService->findSimilarQuestions($question, 3);
echo "Questions similaires trouvées: " . count($similarFaqs) . "\n";
foreach ($similarFaqs as $faq) {
    echo "- " . $faq->question . "\n";
}
echo "\n";

// Test 3: Analyse de sentiment
echo "Test 3: Analyse de sentiment\n";
$sentiment = $aiService->analyzeSentiment($question);
echo "Sentiment: {$sentiment['sentiment']} (confiance: {$sentiment['confidence']})\n\n";

echo "✅ Tests terminés !\n"; 