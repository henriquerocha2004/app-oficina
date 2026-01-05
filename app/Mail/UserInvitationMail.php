<?php

namespace App\Mail;

use App\Models\Tenant;
use App\Models\UserInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public UserInvitation $invitation,
        public string $acceptUrl,
        public ?Tenant $tenant = null,
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $tenantName = $this->tenant?->name ?? config('app.name');
        
        return new Envelope(
            subject: "Convite para {$tenantName}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.user-invitation',
            with: [
                'invitedByName' => $this->invitation->invitedBy->name,
                'tenantName' => $this->tenant?->name ?? config('app.name'),
                'roleName' => $this->invitation->role->name,
                'acceptUrl' => $this->acceptUrl,
                'expiresAt' => $this->invitation->expires_at,
                'tenantLogo' => $this->tenant?->settings['logo'] ?? null,
            ],
        );
    }
}
