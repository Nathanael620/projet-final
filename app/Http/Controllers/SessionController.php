<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SessionController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        
        if ($user->isTutor()) {
            $query = $user->tutorSessions()->with(['student', 'tutor']);
        } else {
            $query = $user->studentSessions()->with(['student', 'tutor']);
        }
        
        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('subject')) {
            $query->where('subject', $request->subject);
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        $sessions = $query->orderBy('scheduled_at', 'desc')
            ->paginate(10)
            ->withQueryString();
        
        return view('sessions.index', compact('sessions'));
    }

    public function create(): View
    {
        $tutors = User::where('role', 'tutor')
            ->where('is_available', true)
            ->orderBy('rating', 'desc')
            ->get();
            
        return view('sessions.create', compact('tutors'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'tutor_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'subject' => 'required|in:mathematics,physics,chemistry,biology,computer_science,languages,literature,history,geography,economics,other',
            'level' => 'required|in:beginner,intermediate,advanced',
            'type' => 'required|in:online,in_person',
            'scheduled_at' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:30|max:480', // 30min à 8h
            'location' => 'nullable|string|max:255|required_if:type,in_person',
        ]);

        $tutor = User::findOrFail($request->tutor_id);
        
        $session = Session::create([
            'student_id' => auth()->id(),
            'tutor_id' => $request->tutor_id,
            'title' => $request->title,
            'description' => $request->description,
            'subject' => $request->subject,
            'level' => $request->level,
            'type' => $request->type,
            'scheduled_at' => $request->scheduled_at,
            'duration_minutes' => $request->duration_minutes,
            'price' => ($tutor->hourly_rate ?? 20) * ($request->duration_minutes / 60),
            'location' => $request->location,
            'status' => 'pending',
        ]);

        return redirect()->route('sessions.show', $session)
            ->with('success', 'Demande de séance créée avec succès !');
    }

    public function show(Session $session): View
    {
        $user = auth()->user();
        
        // Vérifier que l'utilisateur a accès à cette séance
        if ($session->student_id !== $user->id && $session->tutor_id !== $user->id) {
            abort(403);
        }
        
        $session->load(['student', 'tutor', 'feedbacks']);
        
        return view('sessions.show', compact('session'));
    }

    public function update(Request $request, Session $session): RedirectResponse
    {
        $user = auth()->user();
        
        // Seul le tuteur peut accepter/refuser une séance
        if ($session->tutor_id !== $user->id) {
            abort(403);
        }
        
        $request->validate([
            'status' => 'required|in:accepted,rejected',
            'notes' => 'nullable|string|max:1000',
            'meeting_link' => 'nullable|url|required_if:type,online',
        ]);
        
        $session->update([
            'status' => $request->status,
            'notes' => $request->notes,
            'meeting_link' => $request->meeting_link,
        ]);
        
        $statusText = $request->status === 'accepted' ? 'acceptée' : 'refusée';
        
        return redirect()->route('sessions.show', $session)
            ->with('success', "Séance {$statusText} avec succès !");
    }

    public function destroy(Session $session): RedirectResponse
    {
        $user = auth()->user();
        
        // Seul l'étudiant peut annuler une séance en attente
        if ($session->student_id !== $user->id || $session->status !== 'pending') {
            abort(403);
        }
        
        $session->update(['status' => 'cancelled']);
        
        return redirect()->route('sessions.index')
            ->with('success', 'Séance annulée avec succès !');
    }
}
