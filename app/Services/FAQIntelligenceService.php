<?php

namespace App\Services;

use App\Models\FAQ;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FAQIntelligenceService
{
    private const OPENAI_API_URL = 'https://api.openai.com/v1/chat/completions';
    private const CACHE_TTL = 3600; // 1 heure
    private const MAX_RETRIES = 3;

    /**
     * Génère une réponse automatique basée sur la question
     */
    public function generateAnswer(string $question, string $category = 'general'): ?string
    {
        try {
            // Vérifier d'abord s'il y a des FAQ similaires
            $similarFaqs = $this->findSimilarQuestions($question, 3);
            
            if (!empty($similarFaqs)) {
                $bestMatch = $similarFaqs[0];
                $context = "Question similaire trouvée: {$bestMatch->question}\nRéponse: {$bestMatch->answer}";
            } else {
                $context = $this->getContextForCategory($category);
            }

            // Essayer l'API OpenAI si configurée
            if ($this->isOpenAIConfigured()) {
                $response = $this->callOpenAI($question, $context);
                if ($response) {
                    return $response;
                }
            }

            // Fallback: générer une réponse basée sur le contexte
            return $this->generateFallbackAnswer($question, $category, $similarFaqs);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération de réponse IA', [
                'error' => $e->getMessage(),
                'question' => $question,
                'category' => $category
            ]);
            
            return $this->generateFallbackAnswer($question, $category, []);
        }
    }

    /**
     * Trouve des questions similaires
     */
    public function findSimilarQuestions(string $question, int $limit = 5): array
    {
        try {
            // Recherche intelligente combinée
            $semanticResults = $this->semanticSearch($question, $limit);
            $keywordResults = $this->keywordSearch($question, $limit);
            
            // Combiner et dédupliquer
            $combined = array_merge($semanticResults, $keywordResults);
            $unique = [];
            $seen = [];
            
            foreach ($combined as $faq) {
                if (!in_array($faq->id, $seen)) {
                    $unique[] = $faq;
                    $seen[] = $faq->id;
                }
            }
            
            // Trier par pertinence (votes + similarité)
            usort($unique, function($a, $b) use ($question) {
                $scoreA = $this->calculateSimilarityScore($a, $question);
                $scoreB = $this->calculateSimilarityScore($b, $question);
                return $scoreB <=> $scoreA;
            });
            
            return array_slice($unique, 0, $limit);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la recherche de questions similaires', [
                'error' => $e->getMessage(),
                'question' => $question
            ]);
            
            return $this->keywordSearch($question, $limit);
        }
    }

    /**
     * Analyse le sentiment d'une question/réponse
     */
    public function analyzeSentiment(string $text): array
    {
        try {
            if ($this->isOpenAIConfigured()) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                'Content-Type' => 'application/json',
            ])->post(self::OPENAI_API_URL, [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Analyse le sentiment du texte fourni. Réponds uniquement avec un JSON contenant: {"sentiment": "positive|negative|neutral", "confidence": 0.0-1.0, "keywords": ["mot1", "mot2"]}'
                    ],
                    [
                        'role' => 'user',
                        'content' => $text
                    ]
                ],
                'max_tokens' => 100,
                'temperature' => 0.3,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '{}';
                    $result = json_decode($content, true);
                    if ($result) {
                        return $result;
                    }
                }
            }

            // Fallback: analyse simple basée sur les mots-clés
            return $this->simpleSentimentAnalysis($text);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'analyse de sentiment', [
                'error' => $e->getMessage(),
                'text' => $text
            ]);
            
            return $this->simpleSentimentAnalysis($text);
        }
    }

    /**
     * Suggère des améliorations pour une réponse
     */
    public function suggestImprovements(string $question, string $answer): array
    {
        try {
            if ($this->isOpenAIConfigured()) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                'Content-Type' => 'application/json',
            ])->post(self::OPENAI_API_URL, [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Analyse la réponse fournie et suggère des améliorations. Réponds en JSON: {"suggestions": ["suggestion1", "suggestion2"], "score": 0-10, "improvements": ["amélioration1", "amélioration2"]}'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Question: {$question}\nRéponse: {$answer}"
                    ]
                ],
                'max_tokens' => 300,
                'temperature' => 0.5,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '{}';
                    $result = json_decode($content, true);
                    if ($result) {
                        return $result;
                    }
                }
            }

            // Fallback: suggestions basiques
            return $this->generateBasicSuggestions($question, $answer);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suggestion d\'améliorations', [
                'error' => $e->getMessage(),
                'question' => $question
            ]);
            
            return $this->generateBasicSuggestions($question, $answer);
        }
    }

    /**
     * Génère des questions automatiques basées sur le contenu
     */
    public function generateAutoQuestions(string $content, string $category = 'general'): array
    {
        try {
            if ($this->isOpenAIConfigured()) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                'Content-Type' => 'application/json',
            ])->post(self::OPENAI_API_URL, [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Génère 3-5 questions pertinentes basées sur le contenu fourni. Réponds en JSON: {"questions": ["question1", "question2", "question3"]}'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Contenu: {$content}\nCatégorie: {$category}"
                    ]
                ],
                'max_tokens' => 200,
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '{}';
                $result = json_decode($content, true);
                    if (isset($result['questions'])) {
                        return $result['questions'];
                    }
                }
            }

            // Fallback: questions basées sur les mots-clés
            return $this->generateKeywordBasedQuestions($content, $category);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération de questions automatiques', [
                'error' => $e->getMessage(),
                'content' => $content
            ]);
            
            return $this->generateKeywordBasedQuestions($content, $category);
        }
    }

    /**
     * Recherche intelligente dans les FAQ
     */
    public function intelligentSearch(string $query, int $limit = 10): array
    {
        $cacheKey = 'faq_search_' . md5($query . $limit);
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($query, $limit) {
            try {
            // Recherche par similarité sémantique
            $semanticResults = $this->semanticSearch($query, $limit);
            
            // Recherche par mots-clés
            $keywordResults = $this->keywordSearch($query, $limit);
            
            // Combiner et dédupliquer les résultats
            $combined = array_merge($semanticResults, $keywordResults);
            $unique = [];
            $seen = [];
            
            foreach ($combined as $faq) {
                if (!in_array($faq->id, $seen)) {
                    $unique[] = $faq;
                    $seen[] = $faq->id;
                }
            }
                
                // Trier par pertinence
                usort($unique, function($a, $b) use ($query) {
                    $scoreA = $this->calculateSimilarityScore($a, $query);
                    $scoreB = $this->calculateSimilarityScore($b, $query);
                    return $scoreB <=> $scoreA;
                });
            
            return array_slice($unique, 0, $limit);
                
            } catch (\Exception $e) {
                Log::error('Erreur lors de la recherche intelligente', [
                    'error' => $e->getMessage(),
                    'query' => $query
                ]);
                
                return $this->keywordSearch($query, $limit);
            }
        });
    }

    /**
     * Obtient les statistiques IA
     */
    public function getAIStats(): array
    {
        return [
            'total' => FAQ::count(),
            'public' => FAQ::where('is_public', true)->count(),
            'popular' => FAQ::where('votes', '>', 5)->count(), // FAQ avec plus de 5 votes
            'answered' => FAQ::where('status', 'answered')->count(),
            'recent' => FAQ::where('created_at', '>=', now()->subDays(7))->count(),
            'categories' => FAQ::selectRaw('category, count(*) as count')
                ->groupBy('category')
                ->pluck('count', 'category')
                ->toArray(),
        ];
    }

    /**
     * Vérifie si OpenAI est configuré
     */
    private function isOpenAIConfigured(): bool
    {
        $apiKey = config('services.openai.api_key');
        return !empty($apiKey) && $apiKey !== 'your-openai-api-key';
    }

    /**
     * Appelle l'API OpenAI avec retry
     */
    private function callOpenAI(string $question, string $context): ?string
    {
        for ($attempt = 1; $attempt <= self::MAX_RETRIES; $attempt++) {
            try {
                $response = Http::timeout(30)->withHeaders([
                    'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                    'Content-Type' => 'application/json',
                ])->post(self::OPENAI_API_URL, [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => "Tu es un assistant spécialisé dans l'aide aux utilisateurs d'une plateforme de tutorat. 
                            Réponds de manière claire, concise et utile. Utilise le contexte fourni pour donner des réponses précises.
                            Réponds toujours en français."
                        ],
                        [
                            'role' => 'user',
                            'content' => "Contexte: {$context}\n\nQuestion: {$question}\n\nGénère une réponse utile et détaillée."
                        ]
                    ],
                    'max_tokens' => 500,
                    'temperature' => 0.7,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $data['choices'][0]['message']['content'] ?? null;
                }

                if ($attempt < self::MAX_RETRIES) {
                    sleep(1); // Attendre avant de réessayer
                }
            } catch (\Exception $e) {
                Log::warning("Tentative {$attempt} échouée pour OpenAI", ['error' => $e->getMessage()]);
                if ($attempt < self::MAX_RETRIES) {
                    sleep(1);
                }
            }
        }

        return null;
    }

    /**
     * Génère une réponse de fallback
     */
    private function generateFallbackAnswer(string $question, string $category, array $similarFaqs): string
    {
        if (!empty($similarFaqs)) {
            $bestMatch = $similarFaqs[0];
            return "Basé sur une question similaire dans notre base de données :\n\n" . $bestMatch->answer;
        }

        $context = $this->getContextForCategory($category);
        $keywords = $this->extractKeywords($question);
        
        return "Merci pour votre question sur {$context}. " .
               "Je vais vous aider à trouver la réponse la plus appropriée. " .
               "Si vous ne trouvez pas ce que vous cherchez, n'hésitez pas à contacter notre support.";
    }

    /**
     * Analyse de sentiment simple
     */
    private function simpleSentimentAnalysis(string $text): array
    {
        $positiveWords = ['merci', 'super', 'génial', 'excellent', 'parfait', 'bon', 'bien', 'aide', 'utile'];
        $negativeWords = ['problème', 'erreur', 'bug', 'mauvais', 'nul', 'déçu', 'frustré', 'colère'];
        
        $text = Str::lower($text);
        $positiveCount = 0;
        $negativeCount = 0;
        
        foreach ($positiveWords as $word) {
            $positiveCount += substr_count($text, $word);
        }
        
        foreach ($negativeWords as $word) {
            $negativeCount += substr_count($text, $word);
        }
        
        if ($positiveCount > $negativeCount) {
            $sentiment = 'positive';
            $confidence = min(0.9, 0.5 + ($positiveCount - $negativeCount) * 0.1);
        } elseif ($negativeCount > $positiveCount) {
            $sentiment = 'negative';
            $confidence = min(0.9, 0.5 + ($negativeCount - $positiveCount) * 0.1);
        } else {
            $sentiment = 'neutral';
            $confidence = 0.5;
        }
        
        return [
            'sentiment' => $sentiment,
            'confidence' => $confidence,
            'keywords' => array_slice(explode(' ', $text), 0, 5)
        ];
    }

    /**
     * Génère des suggestions basiques
     */
    private function generateBasicSuggestions(string $question, string $answer): array
    {
        $suggestions = [];
        $improvements = [];
        
        if (strlen($answer) < 50) {
            $improvements[] = 'La réponse pourrait être plus détaillée';
        }
        
        if (!str_contains($answer, '.')) {
            $improvements[] = 'Ajoutez des points pour structurer la réponse';
        }
        
        $suggestions[] = 'Vérifiez l\'orthographe et la grammaire';
        $suggestions[] = 'Ajoutez des exemples concrets si possible';
        
        return [
            'suggestions' => $suggestions,
            'score' => min(10, max(1, strlen($answer) / 20)),
            'improvements' => $improvements
        ];
    }

    /**
     * Génère des questions basées sur les mots-clés
     */
    private function generateKeywordBasedQuestions(string $content, string $category): array
    {
        $keywords = $this->extractKeywords($content);
        $questions = [];
        
        foreach (array_slice($keywords, 0, 3) as $keyword) {
            $questions[] = "Comment fonctionne {$keyword} ?";
        }
        
        $questions[] = "Quels sont les avantages de cette fonctionnalité ?";
        $questions[] = "Y a-t-il des limitations à connaître ?";
        
        return array_slice($questions, 0, 5);
    }

    /**
     * Calcule un score de similarité
     */
    private function calculateSimilarityScore(FAQ $faq, string $query): float
    {
        $queryWords = explode(' ', Str::lower($query));
        $questionWords = explode(' ', Str::lower($faq->question));
        $answerWords = explode(' ', Str::lower($faq->answer));
        
        $score = 0;
        
        foreach ($queryWords as $word) {
            if (strlen($word) > 2) {
                if (in_array($word, $questionWords)) {
                    $score += 2; // Plus de poids pour les mots dans la question
                }
                if (in_array($word, $answerWords)) {
                    $score += 1;
                }
            }
        }
        
        // Bonus pour les votes
        $score += $faq->votes * 0.1;
        
        return $score;
    }

    /**
     * Extrait les mots-clés d'un texte
     */
    private function extractKeywords(string $text): array
    {
        $text = Str::lower($text);
        $words = preg_split('/\s+/', $text);
        $words = array_filter($words, function($word) {
            return strlen($word) > 3 && !in_array($word, ['avec', 'pour', 'dans', 'sur', 'par', 'les', 'des', 'une', 'qui', 'que', 'quoi']);
        });
        
        return array_slice(array_unique($words), 0, 10);
    }

    /**
     * Obtient le contexte pour une catégorie
     */
    private function getContextForCategory(string $category): string
    {
        $contexts = [
            'general' => 'Questions générales sur la plateforme de tutorat, son fonctionnement et ses fonctionnalités.',
            'technical' => 'Problèmes techniques, bugs, problèmes de connexion, compatibilité navigateur.',
            'payment' => 'Questions sur les paiements, facturation, remboursements, méthodes de paiement.',
            'sessions' => 'Gestion des séances de tutorat, réservation, annulation, planning.',
            'account' => 'Gestion du compte utilisateur, profil, paramètres, sécurité.',
        ];

        return $contexts[$category] ?? $contexts['general'];
    }

    /**
     * Recherche par mots-clés
     */
    private function keywordSearch(string $query, int $limit): array
    {
        $keywords = explode(' ', Str::lower($query));
        
        return FAQ::where(function ($q) use ($keywords) {
            foreach ($keywords as $keyword) {
                if (strlen($keyword) > 2) {
                    $q->where(function ($subQ) use ($keyword) {
                        $subQ->where('question', 'like', "%{$keyword}%")
                             ->orWhere('answer', 'like', "%{$keyword}%");
                    });
                }
            }
        })
        ->where('is_public', true)
        ->orderBy('votes', 'desc')
        ->limit($limit)
        ->get()
        ->all();
    }

    /**
     * Recherche sémantique améliorée
     */
    private function semanticSearch(string $query, int $limit): array
    {
        // Recherche par similarité de mots
        $queryWords = explode(' ', Str::lower($query));
        
        return FAQ::where('is_public', true)
            ->where(function ($q) use ($queryWords) {
                foreach ($queryWords as $word) {
                    if (strlen($word) > 3) {
                        $q->orWhere('question', 'like', "%{$word}%")
                          ->orWhere('answer', 'like', "%{$word}%");
                    }
                }
            })
            ->orderBy('votes', 'desc')
            ->limit($limit)
            ->get()
            ->all();
    }
} 