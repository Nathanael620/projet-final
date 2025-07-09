<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TutorController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', 'tutor')
            ->where('is_available', true);
        
        // Filtre par matière (si implémenté dans les compétences)
        if ($request->filled('subject')) {
            $subject = $request->subject;
            $query->whereJsonContains('skills', $subject);
        }
        
        // Filtre par niveau
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }
        
        // Filtre par prix maximum
        if ($request->filled('max_price')) {
            $query->where('hourly_rate', '<=', $request->max_price);
        }
        
        $tutors = $query->orderBy('rating', 'desc')
            ->paginate(12)
            ->withQueryString();
            
        return view('tutors.index', compact('tutors'));
    }

    public function show(User $tutor): View
    {
        if ($tutor->role !== 'tutor') {
            abort(404);
        }
        
        return view('tutors.show', compact('tutor'));
    }
}
