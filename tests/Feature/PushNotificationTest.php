<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Session;
use App\Services\PushNotificationService;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PushNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected PushNotificationService $pushNotificationService;
    protected NotificationService $notificationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notificationService = new NotificationService();
        $this->pushNotificationService = new PushNotificationService($this->notificationService);
    }

    public function test_session_created_notification_sent_to_both_users(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $tutor = User::factory()->create(['role' => 'tutor']);

        $session = Session::create([
            'student_id' => $student->id,
            'tutor_id' => $tutor->id,
            'title' => 'Test Session',
            'description' => 'Test Description',
            'subject' => 'mathematics',
            'level' => 'beginner',
            'type' => 'online',
            'status' => 'pending',
            'scheduled_at' => now()->addHour(),
            'duration_minutes' => 60,
            'price' => 20.00,
        ]);

        $this->pushNotificationService->notifySessionCreated($session);

        // Vérifier que l'étudiant a reçu une notification
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $student->id,
            'notifiable_type' => User::class,
            'type' => 'session_created_student',
        ]);

        // Vérifier que le tuteur a reçu une notification
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $tutor->id,
            'notifiable_type' => User::class,
            'type' => 'session_created_tutor',
        ]);

        // Vérifier le contenu des notifications
        $studentNotification = $student->notifications()->where('type', 'session_created_student')->first();
        $this->assertNotNull($studentNotification);
        $this->assertEquals('Séance créée avec succès', $studentNotification->data['title']);

        $tutorNotification = $tutor->notifications()->where('type', 'session_created_tutor')->first();
        $this->assertNotNull($tutorNotification);
        $this->assertEquals('Nouvelle demande de séance', $tutorNotification->data['title']);
    }

    public function test_session_accepted_notification_sent_to_student(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $tutor = User::factory()->create(['role' => 'tutor']);

        $session = Session::create([
            'student_id' => $student->id,
            'tutor_id' => $tutor->id,
            'title' => 'Test Session',
            'description' => 'Test Description',
            'subject' => 'mathematics',
            'level' => 'beginner',
            'type' => 'online',
            'status' => 'accepted',
            'scheduled_at' => now()->addHour(),
            'duration_minutes' => 60,
            'price' => 20.00,
        ]);

        $this->pushNotificationService->notifySessionAccepted($session);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $student->id,
            'notifiable_type' => User::class,
            'type' => 'session_accepted',
        ]);

        $notification = $student->notifications()->where('type', 'session_accepted')->first();
        $this->assertNotNull($notification);
        $this->assertEquals('Séance acceptée', $notification->data['title']);
        $this->assertEquals('success', $notification->data['color']);
    }

    public function test_session_rejected_notification_sent_to_student(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $tutor = User::factory()->create(['role' => 'tutor']);

        $session = Session::create([
            'student_id' => $student->id,
            'tutor_id' => $tutor->id,
            'title' => 'Test Session',
            'description' => 'Test Description',
            'subject' => 'mathematics',
            'level' => 'beginner',
            'type' => 'online',
            'status' => 'rejected',
            'scheduled_at' => now()->addHour(),
            'duration_minutes' => 60,
            'price' => 20.00,
        ]);

        $this->pushNotificationService->notifySessionRejected($session);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $student->id,
            'notifiable_type' => User::class,
            'type' => 'session_rejected',
        ]);

        $notification = $student->notifications()->where('type', 'session_rejected')->first();
        $this->assertNotNull($notification);
        $this->assertEquals('Séance refusée', $notification->data['title']);
        $this->assertEquals('danger', $notification->data['color']);
    }

    public function test_session_cancelled_notification_sent_to_other_user(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $tutor = User::factory()->create(['role' => 'tutor']);

        $session = Session::create([
            'student_id' => $student->id,
            'tutor_id' => $tutor->id,
            'title' => 'Test Session',
            'description' => 'Test Description',
            'subject' => 'mathematics',
            'level' => 'beginner',
            'type' => 'online',
            'status' => 'cancelled',
            'scheduled_at' => now()->addHour(),
            'duration_minutes' => 60,
            'price' => 20.00,
        ]);

        // L'étudiant annule la séance
        $this->pushNotificationService->notifySessionCancelled($session, $student);

        // Le tuteur devrait recevoir la notification
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $tutor->id,
            'notifiable_type' => User::class,
            'type' => 'session_cancelled',
        ]);

        $notification = $tutor->notifications()->where('type', 'session_cancelled')->first();
        $this->assertNotNull($notification);
        $this->assertEquals('Séance annulée', $notification->data['title']);
        $this->assertEquals('warning', $notification->data['color']);
    }

    public function test_session_reminder_notification_sent_to_both_users(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $tutor = User::factory()->create(['role' => 'tutor']);

        $session = Session::create([
            'student_id' => $student->id,
            'tutor_id' => $tutor->id,
            'title' => 'Test Session',
            'description' => 'Test Description',
            'subject' => 'mathematics',
            'level' => 'beginner',
            'type' => 'online',
            'status' => 'accepted',
            'scheduled_at' => now()->addHour(),
            'duration_minutes' => 60,
            'price' => 20.00,
        ]);

        $this->pushNotificationService->notifySessionReminder($session);

        // Vérifier les notifications de rappel
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $student->id,
            'notifiable_type' => User::class,
            'type' => 'session_reminder_student',
        ]);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $tutor->id,
            'notifiable_type' => User::class,
            'type' => 'session_reminder_tutor',
        ]);

        $studentNotification = $student->notifications()->where('type', 'session_reminder_student')->first();
        $this->assertNotNull($studentNotification);
        $this->assertEquals('Rappel de séance', $studentNotification->data['title']);
        $this->assertEquals('warning', $studentNotification->data['color']);
    }

    public function test_session_completed_notification_sent_to_both_users(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $tutor = User::factory()->create(['role' => 'tutor']);

        $session = Session::create([
            'student_id' => $student->id,
            'tutor_id' => $tutor->id,
            'title' => 'Test Session',
            'description' => 'Test Description',
            'subject' => 'mathematics',
            'level' => 'beginner',
            'type' => 'online',
            'status' => 'completed',
            'scheduled_at' => now()->subHour(),
            'duration_minutes' => 60,
            'price' => 20.00,
        ]);

        $this->pushNotificationService->notifySessionCompleted($session);

        // Vérifier les notifications de fin de séance
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $student->id,
            'notifiable_type' => User::class,
            'type' => 'session_completed_student',
        ]);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $tutor->id,
            'notifiable_type' => User::class,
            'type' => 'session_completed_tutor',
        ]);

        $studentNotification = $student->notifications()->where('type', 'session_completed_student')->first();
        $this->assertNotNull($studentNotification);
        $this->assertEquals('Séance terminée', $studentNotification->data['title']);
        $this->assertEquals('success', $studentNotification->data['color']);
        $this->assertEquals('Laisser un avis', $studentNotification->data['action_text']);
    }
} 