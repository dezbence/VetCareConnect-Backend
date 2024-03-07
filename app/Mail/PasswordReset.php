<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    public $url;

        public function __construct(string $url)
    {
        $this->url = 'http://localhost:5173/uj-jelszo/'.$url;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: 'vetcareconnect@gmail.com',
            subject: 'VetCareConnect - Jelszó visszaállítása',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.password_reset',
            with: [
                'url' => $this->url,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
