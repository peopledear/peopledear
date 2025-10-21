<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class UserInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Invitation $invitation
    ) {
        $this->invitation->loadMissing(['role', 'inviter']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You have been invited to PeopleDear',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invitation',
            with: [
                'url' => route('invitation.show', $this->invitation->token),
                'inviterName' => $this->invitation->inviter->name,
                'roleName' => $this->invitation->role->display_name,
                'expiresAt' => $this->invitation->expires_at->format('F j, Y'),
            ],
        );
    }
}
