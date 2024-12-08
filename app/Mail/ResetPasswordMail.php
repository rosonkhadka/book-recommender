<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;

class ResetPasswordMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public string $url;

    public function __construct(
        public string $email,
        public string $token
    ) {
        $this->url = config("app.frontend_url") . '/reset-password?email=' . $this->email . '&token=' . $this->token;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Password Mail',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.resetpassword',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
