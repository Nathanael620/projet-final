<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'in:student,tutor'],
            'skills' => ['nullable', 'string', 'max:500'],
            'level' => ['required', 'in:beginner,intermediate,advanced'],
            'bio' => ['nullable', 'string', 'max:500'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0', 'max:1000'],
        ]);

        // Traitement des compétences
        $skills = null;
        if ($request->skills) {
            $skillsArray = array_map('trim', explode(',', $request->skills));
            $skillsArray = array_filter($skillsArray); // Supprimer les éléments vides
            $skills = !empty($skillsArray) ? $skillsArray : null;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => $request->role,
            'skills' => $skills,
            'level' => $request->level,
            'bio' => $request->bio,
            'hourly_rate' => $request->hourly_rate,
            'is_available' => $request->role === 'tutor',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
