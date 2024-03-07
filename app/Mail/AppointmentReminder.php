<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $username;

    public function __construct($username)
    {
        $this->username = $username;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: 'vetcareconnect@gmail.com',
            subject: 'VetCareConnect - Regisztráció megerősítése',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.register_confirm',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
