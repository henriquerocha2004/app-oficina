<?php

use App\Models\User;
use App\Models\Role;
use App\Models\UserInvitation;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserInvitationMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TenantTestHelper;

uses(RefreshDatabase::class, TenantTestHelper::class);

beforeEach(function () {
    $this->initializeTenant();
    $this->ownerRole = Role::factory()->create([
        'name' => 'Owner',
        'slug' => 'owner',
        'is_system' => true,
    ]);
    
    $this->managerRole = Role::factory()->create([
        'name' => 'Manager',
        'slug' => 'manager',
        'is_system' => true,
    ]);
    
    $this->user = User::factory()->create([
        'role_id' => $this->ownerRole->id,
        'is_owner' => true,
    ]);
    
    $this->actingAs($this->user);
});

test('user can view invitations index', function () {
    UserInvitation::factory()->count(3)->create([
        'role_id' => $this->managerRole->id,
        'invited_by_user_id' => $this->user->id,
    ]);
    
    $response = $this->get(route('invitations.index'));
    
    $response->assertOk();
    $response->assertInertia(fn ($page) => 
        $page->component('invitations/Index')
            ->has('invitations.data', 3)
    );
});

test('user can create an invitation', function () {
    Mail::fake();
    
    $response = $this->post(route('invitations.store'), [
        'email' => 'newuser@example.com',
        'role_id' => $this->managerRole->id,
    ]);
    
    $response->assertRedirect(route('users.index'));
    
    $this->assertDatabaseHas('user_invitations', [
        'email' => 'newuser@example.com',
        'role_id' => $this->managerRole->id,
        'invited_by_user_id' => $this->user->id,
    ]);
    
    Mail::assertSent(UserInvitationMail::class);
});

test('cannot create invitation with duplicate email', function () {
    // Create existing user
    User::factory()->create([
        'email' => 'existing@example.com',
        'role_id' => $this->managerRole->id,
    ]);
    
    $response = $this->post(route('invitations.store'), [
        'email' => 'existing@example.com',
        'role_id' => $this->managerRole->id,
    ]);
    
    $response->assertSessionHasErrors('email');
});

test('cannot create invitation with pending invitation email', function () {
    UserInvitation::factory()->create([
        'email' => 'pending@example.com',
        'role_id' => $this->managerRole->id,
        'invited_by_user_id' => $this->user->id,
        'accepted_at' => null,
    ]);
    
    $response = $this->post(route('invitations.store'), [
        'email' => 'pending@example.com',
        'role_id' => $this->managerRole->id,
    ]);
    
    $response->assertSessionHasErrors('email');
});

test('user can resend an invitation', function () {
    Mail::fake();
    
    $invitation = UserInvitation::factory()->create([
        'email' => 'test@example.com',
        'role_id' => $this->managerRole->id,
        'invited_by_user_id' => $this->user->id,
    ]);
    
    $oldToken = $invitation->token;
    
    $response = $this->post(route('invitations.resend', $invitation));
    
    $response->assertRedirect();
    
    $invitation->refresh();
    expect($invitation->token)->not->toBe($oldToken);
    
    Mail::assertSent(UserInvitationMail::class);
});

test('user can cancel an invitation', function () {
    $invitation = UserInvitation::factory()->create([
        'email' => 'test@example.com',
        'role_id' => $this->managerRole->id,
        'invited_by_user_id' => $this->user->id,
    ]);
    
    $response = $this->delete(route('invitations.cancel', $invitation));
    
    $response->assertRedirect();
    
    $this->assertDatabaseMissing('user_invitations', [
        'id' => $invitation->id,
    ]);
});

test('guest can view invitation accept page with valid token', function () {
    $invitation = UserInvitation::factory()->create([
        'email' => 'test@example.com',
        'role_id' => $this->managerRole->id,
        'invited_by_user_id' => $this->user->id,
        'expires_at' => now()->addDays(7),
    ]);
    
    $this->app['auth']->logout(); // Logout
    
    $response = $this->get(route('invitations.accept.show', ['token' => $invitation->token]));
    
    $response->assertOk();
    $response->assertInertia(fn ($page) => 
        $page->component('invitations/Accept')
            ->has('invitation')
            ->where('invitation.email', 'test@example.com')
    );
});

test('guest can accept invitation and create account', function () {
    $invitation = UserInvitation::factory()->create([
        'email' => 'newuser@example.com',
        'role_id' => $this->managerRole->id,
        'invited_by_user_id' => $this->user->id,
        'expires_at' => now()->addDays(7),
    ]);
    
    $this->app['auth']->logout(); // Logout
    
    $response = $this->post(route('invitations.accept'), [
        'token' => $invitation->token,
        'name' => 'New User',
        'password' => 'SecurePassword123!',
        'password_confirmation' => 'SecurePassword123!',
    ]);
    
    $response->assertRedirect(route('dashboard'));
    
    $this->assertDatabaseHas('users', [
        'email' => 'newuser@example.com',
        'name' => 'New User',
        'role_id' => $this->managerRole->id,
    ]);
    
    $invitation->refresh();
    expect($invitation->accepted_at)->not->toBeNull();
});

test('cannot accept expired invitation', function () {
    $invitation = UserInvitation::factory()->create([
        'email' => 'test@example.com',
        'role_id' => $this->managerRole->id,
        'invited_by_user_id' => $this->user->id,
        'expires_at' => now()->subDay(), // Expired
    ]);
    
    $this->app['auth']->logout();
    
    $response = $this->post(route('invitations.accept'), [
        'token' => $invitation->token,
        'name' => 'Test User',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);
    
    $response->assertSessionHasErrors();
});
