<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TutorController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\FAQChatbotController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\FeedbackController;



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
    
    // Gestion des sessions
    Route::get('/profile/sessions', [ProfileController::class, 'sessions'])->name('profile.sessions');
    Route::post('/profile/sessions/{sessionId}/logout', [ProfileController::class, 'logoutSession'])->name('profile.logout-session');
    
    // Avatar management
    Route::post('/avatar/upload', [AvatarController::class, 'upload'])->name('avatar.upload');
    Route::delete('/avatar/remove', [AvatarController::class, 'remove'])->name('avatar.remove');
    Route::get('/avatar/{userId?}', [AvatarController::class, 'getAvatar'])->name('avatar.get');
    Route::post('/avatar/crop', [AvatarController::class, 'crop'])->name('avatar.crop');
    
    // Test route pour avatar
    Route::get('/test-avatar', function () {
        return view('test_avatar_upload');
    })->name('test.avatar');
    
    // Test simple pour avatar
    Route::get('/test-simple-avatar', function () {
        return view('test_simple_avatar');
    })->name('test.simple.avatar');
    
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
    Route::get('/messages/{otherUser}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{otherUser}', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{otherUser}/new', [MessageController::class, 'getNewMessages'])->name('messages.new');
    Route::post('/messages/{otherUser}/read', [MessageController::class, 'markAsRead'])->name('messages.read');
    Route::post('/messages/search', [MessageController::class, 'search'])->name('messages.search');
    Route::post('/messages/search-users', [MessageController::class, 'searchUsers'])->name('messages.search-users');
    Route::patch('/messages/{message}', [MessageController::class, 'update'])->name('messages.update');
    Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
    Route::get('/messages/{message}/download', [MessageController::class, 'downloadFile'])->name('messages.download');
    Route::get('/messages/stats', [MessageController::class, 'getStats'])->name('messages.stats');
    
    // FAQ
    Route::get('/faq', [FAQController::class, 'public'])->name('faqs.public');
    
    // Routes Chatbot FAQ (doivent être avant la route resource)
    Route::get('/faqs/chatbot', [FAQChatbotController::class, 'index'])->name('faqs.chatbot');
    Route::post('/faqs/chatbot/ask', [FAQChatbotController::class, 'ask'])->name('faqs.chatbot.ask');
    Route::post('/faqs/chatbot/suggestions', [FAQChatbotController::class, 'getSuggestions'])->name('faqs.chatbot.suggestions');
    Route::post('/faqs/chatbot/rate', [FAQChatbotController::class, 'rateResponse'])->name('faqs.chatbot.rate');
    Route::post('/faqs/chatbot/generate-questions', [FAQChatbotController::class, 'generateQuestions'])->name('faqs.chatbot.generate-questions');
    
    // Route resource FAQ (après les routes spécifiques)
    Route::resource('faqs', FAQController::class);
    
    // Routes IA pour FAQ
    Route::post('/faqs/generate-ai-answer', [FAQController::class, 'generateAIAnswer'])->name('faqs.generate-ai-answer');
    Route::post('/faqs/find-similar', [FAQController::class, 'findSimilar'])->name('faqs.find-similar');
    Route::post('/faqs/improve-answer', [FAQController::class, 'improveAnswer'])->name('faqs.improve-answer');
    Route::post('/faqs/{faq}/vote', [FAQController::class, 'vote'])->name('faqs.vote');
    
    // Routes admin (nécessitent le rôle admin)
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/admin/sessions', [AdminController::class, 'sessions'])->name('admin.sessions');
        Route::get('/admin/session-reminders', [AdminController::class, 'sessionReminders'])->name('admin.session-reminders');
        Route::post('/admin/send-reminders', [AdminController::class, 'sendReminders'])->name('admin.send-reminders');
    });

    // Routes de paiement
    Route::get('/payments/wallet', [PaymentController::class, 'wallet'])->name('payments.wallet');
    Route::get('/payments/history', [PaymentController::class, 'history'])->name('payments.history');
    Route::post('/payments/add-funds', [PaymentController::class, 'addFunds'])->name('payments.add-funds');
    Route::post('/payments/withdraw-funds', [PaymentController::class, 'withdrawFunds'])->name('payments.withdraw-funds');
    Route::get('/payments/{session}/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments/{session}/process', [PaymentController::class, 'process'])->name('payments.process');
    Route::get('/payments/{payment}/success', [PaymentController::class, 'success'])->name('payments.success');
    
    // Routes de feedback et notation
    Route::get('/feedback/{session}/create', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/feedback/{session}', [FeedbackController::class, 'store'])->name('feedback.store');
    Route::get('/feedback/my-feedbacks', [FeedbackController::class, 'myFeedbacks'])->name('feedback.my-feedbacks');
    Route::get('/feedback/{feedback}/edit', [FeedbackController::class, 'edit'])->name('feedback.edit');
    Route::put('/feedback/{feedback}', [FeedbackController::class, 'update'])->name('feedback.update');
    Route::delete('/feedback/{feedback}', [FeedbackController::class, 'destroy'])->name('feedback.destroy');
    Route::get('/users/{user}/feedbacks', [FeedbackController::class, 'userFeedbacks'])->name('feedback.user-feedbacks');
});

// Routes pour les notifications
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications/delete-read', [App\Http\Controllers\NotificationController::class, 'deleteRead'])->name('notifications.delete-read');
    Route::get('/notifications/unread', [App\Http\Controllers\NotificationController::class, 'getUnread'])->name('notifications.unread');
    Route::get('/notifications/stats', [App\Http\Controllers\NotificationController::class, 'getStats'])->name('notifications.stats');
     

Route::get('/profile/sessions', [ProfileController::class, 'sessions'])->name('profile.sessions');

});
