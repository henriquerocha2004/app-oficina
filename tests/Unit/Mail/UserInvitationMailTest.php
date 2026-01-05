<?php

use App\Mail\UserInvitationMail;
use App\Models\User;
use App\Models\Role;
use App\Models\UserInvitation;

uses(Tests\Helpers\TenantTestHelper::class);

beforeEach(function () {
    $this->initializeTenant();
});

it('has correct subject', function () {
    $role = Role::factory()->create(['name' => 'Manager']);
    $inviter = User::factory()->create(['name' => 'John Doe']);
    
    $invitation = UserInvitation::factory()->create([
        'email' => 'test@example.com',
        'role_id' => $role->id,
        'invited_by_user_id' => $inviter->id,
    ]);
    
    $mailable = new UserInvitationMail($invitation);
    
    expect($mailable->subject)->toBe('Convite para acessar o sistema');
});

it('contains invitation data', function () {
    $role = Role::factory()->create(['name' => 'Manager']);
    $inviter = User::factory()->create(['name' => 'John Doe']);
    
    $invitation = UserInvitation::factory()->create([
        'email' => 'test@example.com',
        'role_id' => $role->id,
        'invited_by_user_id' => $inviter->id,
    ]);
    
    $mailable = new UserInvitationMail($invitation);
    
    $mailable->assertSeeInHtml($invitation->email);
    $mailable->assertSeeInHtml($invitation->token);
});

it('has correct recipient', function () {
    $invitation = UserInvitation::factory()->create([
        'email' => 'test@example.com',
    ]);
    
    $mailable = new UserInvitationMail($invitation);
    
    $mailable->assertHasTo('test@example.com');
});
