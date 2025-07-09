<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TutorController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Page d'accueil
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Routes d'authentification (générées par Laravel Breeze)
require __DIR__.'/auth.php';

// Routes protégées (nécessitent une authentification)
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/availability', [ProfileController::class, 'updateAvailability'])->name('profile.availability');
    Route::post('/profile/hourly-rate', [ProfileController::class, 'updateHourlyRate'])->name('profile.hourly-rate');
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show')->middleware('profile.access');
    
    // Gestion avancée des comptes
    Route::post('/profile/logout-all', [ProfileController::class, 'logoutAllDevices'])->name('profile.logout-all');
    Route::post('/profile/deactivate', [ProfileController::class, 'deactivate'])->name('profile.deactivate');
    Route::post('/profile/reactivate', [ProfileController::class, 'reactivate'])->name('profile.reactivate');
    
    // Avatar management
    Route::post('/avatar/upload', [AvatarController::class, 'upload'])->name('avatar.upload');
    Route::delete('/avatar/remove', [AvatarController::class, 'remove'])->name('avatar.remove');
    
    // Tuteurs
    Route::get('/tutors', [TutorController::class, 'index'])->name('tutors.index');
    Route::get('/tutors/{tutor}', [TutorController::class, 'show'])->name('tutors.show');
    
    // Séances
    Route::get('/sessions', [SessionController::class, 'index'])->name('sessions.index');
    Route::get('/sessions/create', [SessionController::class, 'create'])->name('sessions.create');
    Route::post('/sessions', [SessionController::class, 'store'])->name('sessions.store');
    Route::get('/sessions/{session}', [SessionController::class, 'show'])->name('sessions.show');
    Route::patch('/sessions/{session}', [SessionController::class, 'update'])->name('sessions.update');
    Route::delete('/sessions/{session}', [SessionController::class, 'destroy'])->name('sessions.destroy');
    
    // Messages
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{user}', [MessageController::class, 'store'])->name('messages.store');
    
    // FAQ
    Route::get('/faq', [FAQController::class, 'public'])->name('faqs.public');
    Route::middleware(['auth'])->group(function () {
        Route::resource('faqs', FAQController::class);
    });
    
    // Routes admin (nécessitent le rôle admin)
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/admin/sessions', [AdminController::class, 'sessions'])->name('admin.sessions');
    });

    // Routes de paiement
    Route::get('/payments/wallet', [PaymentController::class, 'wallet'])->name('payments.wallet');
    Route::get('/payments/history', [PaymentController::class, 'history'])->name('payments.history');
    Route::post('/payments/add-funds', [PaymentController::class, 'addFunds'])->name('payments.add-funds');
    Route::post('/payments/withdraw-funds', [PaymentController::class, 'withdrawFunds'])->name('payments.withdraw-funds');
    Route::get('/payments/{session}/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments/{session}/process', [PaymentController::class, 'process'])->name('payments.process');
    Route::get('/payments/{payment}/success', [PaymentController::class, 'success'])->name('payments.success');
});
