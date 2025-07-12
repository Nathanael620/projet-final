<?php

namespace Tests\Feature;

use App\Models\FAQ;
use App\Models\User;
use App\Services\FAQIntelligenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FAQIntelligenceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private FAQIntelligenceService $aiService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->aiService = new FAQIntelligenceService();
    }

    /** @test */
    public function it_can_generate_ai_answer()
    {
        $question = 'Comment fonctionne le système de paiement ?';
        $category = 'payment';

        // Simuler une réponse IA (en production, cela appellerait l'API OpenAI)
        $answer = $this->aiService->generateAnswer($question, $category);

        // Vérifier que la réponse n'est pas vide (même en mode fallback)
        $this->assertNotNull($answer);
        $this->assertIsString($answer);
    }

    /** @test */
    public function it_can_find_similar_questions()
    {
        // Créer quelques FAQ de test
        FAQ::create([
            'user_id' => $this->user->id,
            'question' => 'Comment payer avec Mobile Money ?',
            'answer' => 'Utilisez l\'option Mobile Money dans le processus de paiement.',
            'category' => 'payment',
            'is_public' => true,
        ]);

        FAQ::create([
            'user_id' => $this->user->id,
            'question' => 'Comment fonctionne Orange Money ?',
            'answer' => 'Orange Money est accepté comme méthode de paiement.',
            'category' => 'payment',
            'is_public' => true,
        ]);

        $question = 'Comment effectuer un paiement ?';
        $similar = $this->aiService->findSimilarQuestions($question, 5);

        // Vérifier que des questions similaires sont trouvées
        $this->assertIsArray($similar);
        $this->assertGreaterThan(0, count($similar));
    }

    /** @test */
    public function it_can_analyze_sentiment()
    {
        $positiveText = 'J\'adore cette plateforme, elle est géniale !';
        $negativeText = 'Je déteste ce service, il ne fonctionne jamais.';
        $neutralText = 'Comment fonctionne le système ?';

        $positiveSentiment = $this->aiService->analyzeSentiment($positiveText);
        $negativeSentiment = $this->aiService->analyzeSentiment($negativeText);
        $neutralSentiment = $this->aiService->analyzeSentiment($neutralText);

        // Vérifier que l'analyse retourne un résultat valide
        $this->assertArrayHasKey('sentiment', $positiveSentiment);
        $this->assertArrayHasKey('confidence', $positiveSentiment);
        $this->assertArrayHasKey('keywords', $positiveSentiment);

        $this->assertArrayHasKey('sentiment', $negativeSentiment);
        $this->assertArrayHasKey('confidence', $negativeSentiment);
        $this->assertArrayHasKey('keywords', $negativeSentiment);

        $this->assertArrayHasKey('sentiment', $neutralSentiment);
        $this->assertArrayHasKey('confidence', $neutralSentiment);
        $this->assertArrayHasKey('keywords', $neutralSentiment);
    }

    /** @test */
    public function it_can_suggest_improvements()
    {
        $question = 'Comment fonctionne le paiement ?';
        $answer = 'Le paiement fonctionne.';

        $improvements = $this->aiService->suggestImprovements($question, $answer);

        // Vérifier que les suggestions sont retournées
        $this->assertArrayHasKey('suggestions', $improvements);
        $this->assertArrayHasKey('score', $improvements);
        $this->assertArrayHasKey('improvements', $improvements);

        $this->assertIsArray($improvements['suggestions']);
        $this->assertIsInt($improvements['score']);
        $this->assertIsArray($improvements['improvements']);
    }

    /** @test */
    public function it_can_perform_intelligent_search()
    {
        // Créer des FAQ de test
        FAQ::create([
            'user_id' => $this->user->id,
            'question' => 'Comment réserver une séance ?',
            'answer' => 'Allez dans la section séances et cliquez sur réserver.',
            'category' => 'sessions',
            'is_public' => true,
        ]);

        $query = 'réserver séance';
        $results = $this->aiService->intelligentSearch($query, 10);

        // Vérifier que la recherche retourne des résultats
        $this->assertIsArray($results);
        $this->assertGreaterThan(0, count($results));
    }

    /** @test */
    public function it_can_generate_auto_questions()
    {
        $content = 'La plateforme permet aux étudiants de trouver des tuteurs pour des séances de soutien scolaire.';
        $category = 'general';

        $questions = $this->aiService->generateAutoQuestions($content, $category);

        // Vérifier que des questions sont générées
        $this->assertIsArray($questions);
        $this->assertGreaterThan(0, count($questions));
    }

    /** @test */
    public function it_handles_api_errors_gracefully()
    {
        // Simuler une erreur d'API en modifiant temporairement la configuration
        config(['services.openai.api_key' => 'invalid_key']);

        $question = 'Test question';
        $answer = $this->aiService->generateAnswer($question, 'general');

        // Vérifier que le service gère l'erreur gracieusement
        $this->assertNull($answer);

        // Restaurer la configuration
        config(['services.openai.api_key' => env('OPENAI_API_KEY')]);
    }

    /** @test */
    public function it_can_create_faq_with_ai_integration()
    {
        $this->actingAs($this->user);

        $faqData = [
            'question' => 'Comment utiliser l\'IA pour générer des réponses ?',
            'category' => 'general',
            'use_ai' => true,
        ];

        $response = $this->post(route('faqs.store'), $faqData);

        $response->assertRedirect(route('faqs.index'));
        $response->assertSessionHas('success');

        // Vérifier que la FAQ a été créée
        $this->assertDatabaseHas('f_a_q_s', [
            'question' => $faqData['question'],
            'category' => $faqData['category'],
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function it_can_access_chatbot_interface()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('faqs.chatbot'));

        $response->assertStatus(200);
        $response->assertViewIs('faqs.chatbot');
        $response->assertViewHas('popularFaqs');
        $response->assertViewHas('categories');
    }

    /** @test */
    public function it_can_ask_chatbot_question()
    {
        $this->actingAs($this->user);

        $questionData = [
            'question' => 'Comment fonctionne la plateforme ?',
            'context' => 'Première utilisation',
        ];

        $response = $this->postJson(route('faqs.chatbot.ask'), $questionData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'answer',
                'existing_faqs',
                'similar_questions',
                'sentiment',
                'suggestions',
            ],
        ]);
    }

    /** @test */
    public function it_validates_chatbot_input()
    {
        $this->actingAs($this->user);

        // Test avec une question vide
        $response = $this->postJson(route('faqs.chatbot.ask'), [
            'question' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['question']);

        // Test avec une question trop longue
        $response = $this->postJson(route('faqs.chatbot.ask'), [
            'question' => str_repeat('a', 501),
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['question']);
    }
} 