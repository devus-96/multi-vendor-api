<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Support\Facades\App;

class PasswordReset extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public User $user)
    {
    }

    public function envelope(): Envelope
    {
        App::setLocale($this->user->lang ?? config('app.locale'));

        return new Envelope(
            subject: __('password-reset.subject', ['app' => config('app.name')]),
        );
    }

    public function content(): Content
    {
        App::setLocale($this->user->lang ?? config('app.locale'));

        return new Content(
            markdown: 'mail.password-reset',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
