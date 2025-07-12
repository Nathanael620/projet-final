<?php

namespace App\Http\Controllers;

use App\Models\FAQ;
use App\Services\FAQIntelligenceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;

class FAQChatbotController extends Controller
{
    private FAQIntelligenceService $aiService;

    public function __construct(FAQIntelligenceService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Affiche l'interface du chatbot
     */
    public function index(): View
    {
        $popularFaqs = FAQ::where('is_public', true)
            ->orderBy('votes', 'desc')
            ->limit(5)
            ->get();

        $categories = [
            'general' => 'Général',
            'technical' => 'Technique',
            'payment' => 'Paiement',
            'sessions' => 'Séances',
            'account' => 'Compte'
        ];

        return view('faqs.chatbot', compact('popularFaqs', 'categories'));
    }

    /**
     * Traite une question du chatbot
     */
    public function ask(Request $request): JsonResponse
    {
        // Vérifier l'authentification
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez être connecté pour utiliser le chatbot.',
                'data' => [
                    'answer' => 'Veuillez vous connecter pour utiliser le chatbot.',
                    'existing_faqs' => [],
                    'similar_questions' => [],
                    'sentiment' => ['sentiment' => 'neutral', 'confidence' => 0.5, 'keywords' => []],
                    'suggestions' => ['Connectez-vous', 'Consultez nos FAQ publiques'],
                ]
            ], 401);
        }

        $request->validate([
            'question' => 'required|string|max:500',
            'context' => 'nullable|string',
        ]);

        $question = $request->input('question');
        $context = $request->input('context', 'general');
        
        // Log de débogage
        \Log::info('Chatbot question reçue', [
            'question' => $question,
            'context' => $context,
            'user_id' => auth()->id()
        ]);
        
        try {
            // Rechercher des FAQ similaires
            $similarFaqs = $this->aiService->findSimilarQuestions($question, 3);
            
            // Générer une réponse IA
            $aiAnswer = $this->aiService->generateAnswer($question, $context);
            
            // Analyser le sentiment
            $sentiment = $this->aiService->analyzeSentiment($question);
            
            // Générer des suggestions
            $suggestions = $this->generateSuggestions($question, $similarFaqs);
            
            // Préparer les FAQ existantes pour l'affichage
            $existingFaqs = collect($similarFaqs)->map(function($faq) {
                return [
                    'id' => $faq->id,
                    'question' => $faq->question,
                    'answer' => Str::limit($faq->answer, 150),
                    'category' => $faq->getCategoryText(),
                    'votes' => $faq->votes,
                ];
            })->toArray();

            $response = [
                'success' => true,
                'data' => [
                    'answer' => $aiAnswer,
                    'existing_faqs' => $existingFaqs,
                    'similar_questions' => collect($similarFaqs)->pluck('question')->toArray(),
                    'sentiment' => $sentiment,
                    'suggestions' => $suggestions,
                    'confidence' => $this->calculateConfidence($question, $similarFaqs),
                ]
            ];

            // Log de débogage de la réponse
            \Log::info('Chatbot réponse générée', [
                'answer_length' => strlen($aiAnswer),
                'similar_faqs_count' => count($similarFaqs),
                'suggestions_count' => count($suggestions)
            ]);

            return response()->json($response);
            
        } catch (\Exception $e) {
            \Log::error('Erreur chatbot', [
                'error' => $e->getMessage(),
                'question' => $question,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Désolé, je rencontre des difficultés techniques. Veuillez réessayer.',
                'data' => [
                    'answer' => 'Je ne peux pas traiter votre question pour le moment. Veuillez essayer de reformuler ou contacter notre support.',
                    'existing_faqs' => [],
                    'similar_questions' => [],
                    'sentiment' => ['sentiment' => 'neutral', 'confidence' => 0.5, 'keywords' => []],
                    'suggestions' => ['Reformulez votre question', 'Consultez nos FAQ populaires', 'Contactez le support'],
                ]
            ]);
        }
    }

    /**
     * Génère des suggestions basées sur la question
     */
    public function getSuggestions(Request $request): JsonResponse
    {
        $request->validate([
            'question' => 'required|string|max:500',
        ]);

        try {
            $question = $request->input('question');
            
            // Trouver des questions similaires
            $similarFaqs = $this->aiService->findSimilarQuestions($question, 5);
            
            // Générer des suggestions basées sur les mots-clés
            $keywords = $this->extractKeywords($question);
            $suggestions = [];
            
            foreach ($keywords as $keyword) {
                if (strlen($keyword) > 3) {
                    $suggestions[] = "Comment fonctionne {$keyword} ?";
                    $suggestions[] = "Problèmes avec {$keyword}";
                }
            }
            
            // Ajouter des suggestions génériques
            $suggestions = array_merge($suggestions, [
                'Comment créer un compte ?',
                'Comment réserver une séance ?',
                'Comment effectuer un paiement ?',
                'Comment contacter le support ?',
            ]);
            
            // Limiter et mélanger
            $suggestions = array_slice(array_unique($suggestions), 0, 6);
            shuffle($suggestions);

            return response()->json([
                'success' => true,
                'suggestions' => $suggestions
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'suggestions' => ['Comment puis-je vous aider ?', 'Consultez nos FAQ', 'Contactez le support']
            ]);
        }
    }

    /**
     * Évalue la réponse du chatbot
     */
    public function rateResponse(Request $request): JsonResponse
    {
        $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string',
        ]);

        try {
            $question = $request->input('question');
            $answer = $request->input('answer');
            $rating = $request->input('rating');
            $feedback = $request->input('feedback');
            
            // Analyser le feedback pour des améliorations
            $improvements = [];
            if ($feedback) {
                $sentiment = $this->aiService->analyzeSentiment($feedback);
                if ($sentiment['sentiment'] === 'negative') {
                    $improvements[] = 'Améliorer la précision des réponses';
                    $improvements[] = 'Fournir plus de détails';
                }
            }
            
            // Si la note est basse, suggérer des améliorations
            if ($rating <= 2) {
                $improvements[] = 'Reformuler la réponse de manière plus claire';
                $improvements[] = 'Ajouter des exemples concrets';
            }
            
            // Enregistrer l'évaluation (optionnel)
            \Log::info('Évaluation chatbot', [
                'question' => $question,
                'rating' => $rating,
                'feedback' => $feedback,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Merci pour votre évaluation !',
                'improvements' => $improvements
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement de l\'évaluation',
                'improvements' => []
            ]);
        }
    }

    /**
     * Génère des questions automatiques
     */
    public function generateQuestions(Request $request): JsonResponse
    {
        $request->validate([
            'content' => 'required|string',
            'category' => 'nullable|string',
        ]);

        try {
            $content = $request->input('content');
            $category = $request->input('category', 'general');
            
            $questions = $this->aiService->generateAutoQuestions($content, $category);
            
            return response()->json([
                'success' => true,
                'questions' => $questions
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'questions' => [
                    'Comment fonctionne cette fonctionnalité ?',
                    'Quels sont les avantages ?',
                    'Y a-t-il des limitations ?'
                ]
            ]);
        }
    }

    /**
     * Génère des suggestions intelligentes
     */
    private function generateSuggestions(string $question, array $similarFaqs): array
    {
        $suggestions = [];
        
        // Si des FAQ similaires existent
        if (!empty($similarFaqs)) {
            $suggestions[] = 'Consultez nos questions similaires ci-dessous';
        }
        
        // Suggestions basées sur les mots-clés
        $keywords = $this->extractKeywords($question);
        foreach (array_slice($keywords, 0, 2) as $keyword) {
            if (strlen($keyword) > 3) {
                $suggestions[] = "En savoir plus sur {$keyword}";
            }
        }
        
        // Suggestions génériques
        $suggestions[] = 'Consultez notre guide d\'utilisation';
        $suggestions[] = 'Contactez notre support si nécessaire';
        
        return array_slice($suggestions, 0, 4);
    }

    /**
     * Calcule la confiance de la réponse
     */
    private function calculateConfidence(string $question, array $similarFaqs): float
    {
        if (empty($similarFaqs)) {
            return 0.3; // Faible confiance si aucune FAQ similaire
        }
        
        $bestMatch = $similarFaqs[0];
        $similarityScore = $this->calculateSimilarityScore($bestMatch, $question);
        
        // Normaliser le score entre 0 et 1
        $confidence = min(1.0, $similarityScore / 10);
        
        return max(0.3, $confidence); // Minimum 30% de confiance
    }

    /**
     * Calcule un score de similarité simple
     */
    private function calculateSimilarityScore(FAQ $faq, string $query): float
    {
        $queryWords = explode(' ', strtolower($query));
        $questionWords = explode(' ', strtolower($faq->question));
        $answerWords = explode(' ', strtolower($faq->answer));
        
        $score = 0;
        
        foreach ($queryWords as $word) {
            if (strlen($word) > 2) {
                if (in_array($word, $questionWords)) {
                    $score += 2;
                }
                if (in_array($word, $answerWords)) {
                    $score += 1;
                }
            }
        }
        
        return $score + ($faq->votes * 0.1);
    }

    /**
     * Extrait les mots-clés d'une question
     */
    private function extractKeywords(string $text): array
    {
        $text = strtolower($text);
        $words = preg_split('/\s+/', $text);
        $words = array_filter($words, function($word) {
            return strlen($word) > 3 && !in_array($word, [
                'avec', 'pour', 'dans', 'sur', 'par', 'les', 'des', 'une', 'qui', 'que', 'quoi',
                'comment', 'pourquoi', 'quand', 'où', 'combien', 'quel', 'quelle'
            ]);
        });
        
        return array_slice(array_unique($words), 0, 5);
    }
}
