<?php

namespace App\Http\Controllers;

use App\Models\FAQ;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class FAQController extends Controller
{
    public function index(): View
    {
        $faqs = FAQ::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('faqs.index', compact('faqs'));
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

        FAQ::create([
            'user_id' => auth()->id(),
            'question' => $request->question,
            'answer' => $request->answer,
            'category' => $request->category,
            'is_public' => $request->has('is_public'),
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

    public function public(): View
    {
        $faqs = FAQ::where('is_public', true)
            ->with('user')
            ->orderBy('category')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('category');

        $categories = [
            'general' => 'Général',
            'technical' => 'Technique',
            'payment' => 'Paiement',
            'sessions' => 'Séances',
            'account' => 'Compte'
        ];

        return view('faqs.public', compact('faqs', 'categories'));
    }
}
