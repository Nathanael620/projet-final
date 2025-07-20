<?php

namespace App\Mail;

use App\Models\Session;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SessionCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $session;
    public $recipientRole;

    /**
     * Create a new message instance.
     */
    public function __construct(Session $session, string $recipientRole)
    {
        $this->session = $session;
        $this->recipientRole = $recipientRole; // 'student' ou 'tutor'
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = 'Nouvelle séance créée : ' . $this->session->title;
        return $this->subject($subject)
            ->view('emails.session-created');
    }
} 