<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use SensitiveParameter;

class PasswordResetOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        #[SensitiveParameter] public string $code,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Kode reset password — Creative Trees Group');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.password-reset-otp');
    }
}
