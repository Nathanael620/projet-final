<?php

namespace App\Http\Controllers;

use App\Models\FAQ;
use App\Services\FAQIntelligenceService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class FAQController extends Controller
{
    private FAQIntelligenceService $aiService;

    public function __construct(FAQIntelligenceService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index(): View
    {
        $query = FAQ::with('user');
        
        // Filtrage par catégorie
        if (request('category')) {
            $query->where('category', request('category'));
        }
        
        // Recherche intelligente
        if (request('search')) {
            $searchResults = $this->aiService->intelligentSearch(request('search'), 20);
            $faqIds = collect($searchResults)->pluck('id')->toArray();
            if (!empty($faqIds)) {
                $query->whereIn('id', $faqIds);
            } else {
                $query->where(function($q) {
                    $q->where('question', 'like', '%' . request('search') . '%')
                      ->orWhere('answer', 'like', '%' . request('search') . '%');
                });
            }
        }
        
        $faqs = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Statistiques IA
        $stats = $this->getAIStats();
        
        return view('faqs.index', compact('faqs', 'stats'));
    }

    public function create(): View
    {
        return view('faqs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string|max:2000',
            'category' => 'required|in:general,technical,payment,sessions,account',
        ]);

        $faq = FAQ::create([
            'user_id' => auth()->id(),
            'question' => $request->question,
            'answer' => $request->answer,
            'category' => $request->category,
            'is_public' => $request->has('is_public'),
            'status' => 'answered',
        ]);

        return redirect()->route('faqs.index')
            ->with('success', 'Question ajoutée avec succès !');
    }

    public function show(FAQ $faq): View
    {
        return view('faqs.show', compact('faq'));
    }

    public function edit(FAQ $faq): View
    {
        // Seul l'auteur ou un admin peut modifier
        if ($faq->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        return view('faqs.edit', compact('faq'));
    }

    public function update(Request $request, FAQ $faq): RedirectResponse
    {
        // Seul l'auteur ou un admin peut modifier
        if ($faq->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string|max:2000',
            'category' => 'required|in:general,technical,payment,sessions,account',
        ]);

        $faq->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'category' => $request->category,
            'is_public' => $request->has('is_public'),
        ]);

        return redirect()->route('faqs.show', $faq)
            ->with('success', 'Question mise à jour avec succès !');
    }

    public function destroy(FAQ $faq): RedirectResponse
    {
        // Seul l'auteur ou un admin peut supprimer
        if ($faq->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $faq->delete();

        return redirect()->route('faqs.index')
            ->with('success', 'Question supprimée avec succès !');
    }

    /**
     * Obtient les statistiques IA
     */
    private function getAIStats(): array
    {
        return $this->aiService->getAIStats();
    }

    /**
     * Génère une réponse IA pour une question
     */
    public function generateAIAnswer(Request $request): JsonResponse
    {
        $request->validate([
            'question' => 'required|string|max:500',
            'category' => 'nullable|string|in:general,technical,payment,sessions,account,other',
        ]);

        try {
            $question = $request->input('question');
            $category = $request->input('category', 'general');

            $answer = $this->aiService->generateAnswer($question, $category);

            if ($answer) {
                return response()->json([
                    'success' => true,
                    'answer' => $answer,
                    'suggestions' => $this->aiService->suggestImprovements($question, $answer)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Impossible de générer une réponse automatique. Veuillez écrire votre réponse manuellement.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur génération réponse IA', [
                'error' => $e->getMessage(),
                'question' => $request->input('question')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur technique. Veuillez réessayer.'
            ]);
        }
    }

    /**
     * Trouve des questions similaires
     */
    public function findSimilar(Request $request): JsonResponse
    {
        $request->validate([
            'question' => 'required|string|max:500',
            'limit' => 'nullable|integer|min:1|max:10',
        ]);

        try {
            $question = $request->input('question');
            $limit = $request->input('limit', 5);

            $similarFaqs = $this->aiService->findSimilarQuestions($question, $limit);

            $results = collect($similarFaqs)->map(function($faq) {
                return [
                    'id' => $faq->id,
                    'question' => $faq->question,
                    'answer' => Str::limit($faq->answer, 200),
                    'category' => $faq->getCategoryText(),
                    'votes' => $faq->votes,
                    'url' => route('faqs.show', $faq),
                ];
            })->toArray();

            return response()->json([
                'success' => true,
                'results' => $results,
                'count' => count($results)
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur recherche questions similaires', [
                'error' => $e->getMessage(),
                'question' => $request->input('question')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la recherche.',
                'results' => []
            ]);
        }
    }

    /**
     * Améliore une réponse existante
     */
    public function improveAnswer(Request $request): JsonResponse
    {
        $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:2000',
        ]);

        try {
            $question = $request->input('question');
            $answer = $request->input('answer');

            $improvements = $this->aiService->suggestImprovements($question, $answer);

            return response()->json([
                'success' => true,
                'improvements' => $improvements
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur amélioration réponse', [
                'error' => $e->getMessage(),
                'question' => $request->input('question')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'analyse.',
                'improvements' => [
                    'suggestions' => ['Vérifiez l\'orthographe', 'Ajoutez des détails'],
                    'score' => 5,
                    'improvements' => ['La réponse pourrait être plus détaillée']
                ]
            ]);
        }
    }

    /**
     * Vote pour une FAQ
     */
    public function vote(Request $request, FAQ $faq): JsonResponse
    {
        $request->validate([
            'vote' => 'required|in:up,down',
        ]);

        try {
            $vote = $request->input('vote');
            
            if ($vote === 'up') {
                $faq->incrementVotes();
            } else {
                $faq->decrementVotes();
            }

            return response()->json([
                'success' => true,
                'votes' => $faq->votes,
                'message' => 'Vote enregistré avec succès.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement du vote.'
            ]);
        }
    }

    /**
     * Affiche la FAQ publique
     */
    public function public(): View
    {
        $categories = [
            'general' => 'Général',
            'technical' => 'Technique',
            'payment' => 'Paiement',
            'sessions' => 'Séances',
            'account' => 'Compte',
            'other' => 'Autre'
        ];

        $faqs = [];
        foreach ($categories as $key => $label) {
            $faqs[$key] = FAQ::where('category', $key)
                ->where('is_public', true)
                ->where('status', 'answered')
                ->orderBy('votes', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('faqs.public', compact('faqs', 'categories'));
    }
}
