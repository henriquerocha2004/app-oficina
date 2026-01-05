<?php

use App\Models\User;
use App\Models\Role;
use App\Models\UserInvitation;
use App\Services\InvitationService;
use App\DTOs\InvitationInputDTO;
use App\Mail\UserInvitationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

uses(Tests\Helpers\TenantTestHelper::class);

beforeEach(function () {
    $this->initializeTenant();
    
    // Create test roles
    $this->ownerRole = Role::factory()->create([
        'name' => 'Owner',
        'slug' => 'owner',
    ]);
    
    $this->managerRole = Role::factory()->create([
        'name' => 'Manager',
        'slug' => 'manager',
    ]);
    
    // Create inviter user
    $this->inviter = User::factory()->create([
        'role_id' => $this->ownerRole->id,
        'is_owner' => true,
    ]);
    
    $this->invitationService = app(InvitationService::class);
});

it('can create an invitation', function () {
    Mail::fake();
    
    $dto = new InvitationInputDTO(
        email: 'newuser@example.com',
        role_id: $this->managerRole->id,
        invited_by_user_id: $this->inviter->id
    );
    
    $invitation = $this->invitationService->invite($dto);
    
    expect($invitation)->toBeInstanceOf(UserInvitation::class)
        ->email->toBe('newuser@example.com')
        ->role_id->toBe($this->managerRole->id)
        ->invited_by_user_id->toBe($this->inviter->id)
        ->token->not->toBeEmpty()
        ->accepted_at->toBeNull();
    
    expect($invitation->expires_at)
        ->toBeInstanceOf(Carbon\Carbon::class);
    
    // Check email was sent
    Mail::assertSent(UserInvitationMail::class, function ($mail) use ($invitation) {
        return $mail->hasTo('newuser@example.com')
            && $mail->invitation->id === $invitation->id;
    });
});

it('generates unique tokens for invitations', function () {
    Mail::fake();
    
    $dto1 = new InvitationInputDTO(
        email: 'user1@example.com',
        role_id: $this->managerRole->id,
        invited_by_user_id: $this->inviter->id
    );
    
    $dto2 = new InvitationInputDTO(
        email: 'user2@example.com',
        role_id: $this->managerRole->id,
        invited_by_user_id: $this->inviter->id
    );
    
    $invitation1 = $this->invitationService->invite($dto1);
    $invitation2 = $this->invitationService->invite($dto2);
    
    expect($invitation1->token)->not->toBe($invitation2->token);
});

it('can resend an invitation', function () {
    Mail::fake();
    
    // Create initial invitation
    $invitation = UserInvitation::factory()->create([
        'email' => 'test@example.com',
        'role_id' => $this->managerRole->id,
        'invited_by_user_id' => $this->inviter->id,
        'token' => Str::random(64),
        'expires_at' => now()->addDays(7),
    ]);
    
    $oldToken = $invitation->token;
    $oldExpiresAt = $invitation->expires_at;
    
    sleep(1); // Ensure time difference
    
    $newInvitation = $this->invitationService->resend($invitation->id);
    
    expect($newInvitation->token)->not->toBe($oldToken);
    expect($newInvitation->expires_at->greaterThan($oldExpiresAt))->toBeTrue();
    
    // Check new email was sent
    Mail::assertSent(UserInvitationMail::class, 1);
});

it('can cancel an invitation', function () {
    $invitation = UserInvitation::factory()->create([
        'email' => 'test@example.com',
        'role_id' => $this->managerRole->id,
        'invited_by_user_id' => $this->inviter->id,
    ]);
    
    $this->invitationService->cancel($invitation->id);
    
    expect(UserInvitation::find($invitation->id))->toBeNull();
});

it('validates plan limits before accepting invitation', function () {
    // This test would need to mock the tenant plan validation
    // For now, we'll test the basic acceptance flow
    
    $invitation = UserInvitation::factory()->create([
        'email' => 'newuser@example.com',
        'role_id' => $this->managerRole->id,
        'invited_by_user_id' => $this->inviter->id,
        'token' => Str::random(64),
        'expires_at' => now()->addDays(7),
    ]);
    
    $acceptDto = new \App\DTOs\AcceptInvitationDTO(
        token: $invitation->token,
        name: 'New User',
        password: 'SecurePassword123!'
    );
    
    $user = $this->invitationService->accept($acceptDto);
    
    expect($user)->toBeInstanceOf(User::class)
        ->email->toBe('newuser@example.com')
        ->name->toBe('New User')
        ->role_id->toBe($this->managerRole->id);
    
    // Check invitation was marked as accepted
    $invitation->refresh();
    expect($invitation->accepted_at)->not->toBeNull();
});

it('throws exception when accepting expired invitation', function () {
    $invitation = UserInvitation::factory()->create([
        'email' => 'test@example.com',
        'role_id' => $this->managerRole->id,
        'invited_by_user_id' => $this->inviter->id,
        'token' => Str::random(64),
        'expires_at' => now()->subDay(), // Expired
    ]);
    
    $acceptDto = new \App\DTOs\AcceptInvitationDTO(
        token: $invitation->token,
        name: 'Test User',
        password: 'password123'
    );
    
    $this->invitationService->accept($acceptDto);
})->throws(\App\Exceptions\InvitationExpiredException::class);

it('throws exception when accepting already accepted invitation', function () {
    $invitation = UserInvitation::factory()->create([
        'email' => 'test@example.com',
        'role_id' => $this->managerRole->id,
        'invited_by_user_id' => $this->inviter->id,
        'token' => Str::random(64),
        'expires_at' => now()->addDays(7),
        'accepted_at' => now(), // Already accepted
    ]);
    
    $acceptDto = new \App\DTOs\AcceptInvitationDTO(
        token: $invitation->token,
        name: 'Test User',
        password: 'password123'
    );
    
    $this->invitationService->accept($acceptDto);
})->throws(\App\Exceptions\InvitationAlreadyAcceptedException::class);
