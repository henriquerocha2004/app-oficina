<?php

namespace App\Services;

use App\DTOs\AcceptInvitationDTO;
use App\DTOs\InvitationInputDTO;
use App\Mail\UserInvitationMail;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InvitationService
{
    /**
     * Get paginated invitations.
     */
    public function getPaginated(int $perPage = 15)
    {
        return UserInvitation::with(['role', 'invitedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Create and send a new invitation.
     */
    public function invite(InvitationInputDTO $dto): UserInvitation
    {
        // Check subscription plan limits
        $this->validatePlanLimits();

        $invitation = DB::transaction(function () use ($dto) {
            // Create invitation with unique token
            $invitation = UserInvitation::create([
                'email' => $dto->email,
                'role_id' => $dto->roleId,
                'invited_by_user_id' => $dto->invitedByUserId,
                'token' => Str::random(64),
                'expires_at' => now()->addDays(config('app.invitation_expiration_days', 7)),
            ]);

            // Send invitation email
            $this->sendInvitationEmail($invitation);

            return $invitation;
        });

        return $invitation;
    }

    /**
     * Resend an invitation with a new token.
     */
    public function resend(string $invitationId): UserInvitation
    {
        $invitation = UserInvitation::findOrFail($invitationId);

        if ($invitation->isAccepted()) {
            throw new \InvalidArgumentException('Este convite já foi aceito.');
        }

        // Update with new token and expiration
        $invitation->update([
            'token' => Str::random(64),
            'expires_at' => now()->addDays(config('app.invitation_expiration_days', 7)),
        ]);

        // Send new invitation email
        $this->sendInvitationEmail($invitation);

        return $invitation->fresh();
    }

    /**
     * Cancel an invitation.
     */
    public function cancel(string $invitationId): bool
    {
        $invitation = UserInvitation::findOrFail($invitationId);

        if ($invitation->isAccepted()) {
            throw new \InvalidArgumentException('Não é possível cancelar um convite já aceito.');
        }

        return $invitation->delete();
    }

    /**
     * Accept an invitation and create the user.
     */
    public function accept(AcceptInvitationDTO $dto): User
    {
        $invitation = UserInvitation::where('token', $dto->token)->firstOrFail();

        if ($invitation->isExpired()) {
            throw new \InvalidArgumentException('Este convite expirou. Solicite um novo convite.');
        }

        if ($invitation->isAccepted()) {
            throw new \InvalidArgumentException('Este convite já foi utilizado.');
        }

        // Check if email is already registered
        if (User::where('email', $invitation->email)->exists()) {
            throw new \InvalidArgumentException('Este email já está cadastrado.');
        }

        // Validate plan limits before creating user
        $this->validatePlanLimits();

        $user = DB::transaction(function () use ($invitation, $dto) {
            // Create user
            $user = User::create($dto->toUserArray($invitation->email, $invitation->role_id));

            // Mark invitation as accepted
            $invitation->markAsAccepted();

            return $user;
        });

        return $user;
    }

    /**
     * Find invitation by token.
     */
    public function findByToken(string $token): ?UserInvitation
    {
        return UserInvitation::where('token', $token)
            ->with(['role', 'invitedBy'])
            ->first();
    }

    /**
     * Get all pending invitations.
     */
    public function getPendingInvitations()
    {
        return UserInvitation::with(['role', 'invitedBy'])
            ->pending()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Send invitation email.
     */
    private function sendInvitationEmail(UserInvitation $invitation): void
    {
        $tenant = tenancy()->tenant;
        
        // Build accept URL
        $acceptUrl = route('invitations.accept.show', ['token' => $invitation->token]);

        Mail::to($invitation->email)->send(
            new UserInvitationMail($invitation, $acceptUrl, $tenant)
        );
    }

    /**
     * Validate subscription plan limits.
     */
    private function validatePlanLimits(): void
    {
        $tenant = tenancy()->tenant;
        
        if (!$tenant) {
            return; // Skip validation if not in tenant context
        }

        $currentUserCount = User::count();
        $pendingInvitationsCount = UserInvitation::pending()->count();
        $totalCount = $currentUserCount + $pendingInvitationsCount + 1; // +1 for new user/invitation

        $maxUsers = $tenant->subscriptionPlan->limits['max_users'] ?? null;

        if ($maxUsers !== null && $totalCount > $maxUsers) {
            throw new \DomainException(
                "Limite de usuários atingido. Seu plano permite até {$maxUsers} usuários. " .
                "Faça upgrade do seu plano para adicionar mais usuários."
            );
        }
    }
}
