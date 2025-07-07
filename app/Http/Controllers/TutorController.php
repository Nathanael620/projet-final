<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TutorController extends Controller
{
    public function index(): View
    {
        $tutors = User::where('role', 'tutor')
            ->where('is_available', true)
            ->orderBy('rating', 'desc')
            ->paginate(12);
            
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
