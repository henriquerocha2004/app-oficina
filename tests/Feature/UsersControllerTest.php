<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TenantTestHelper;

uses(RefreshDatabase::class, TenantTestHelper::class);

beforeEach(function () {
    $this->initializeTenant();
    
    $this->ownerRole = Role::create([
        'name' => 'Owner',
        'slug' => 'owner',
        'is_system' => true,
    ]);
    
    $this->managerRole = Role::create([
        'name' => 'Manager',
        'slug' => 'manager',
        'is_system' => true,
    ]);
    
    $this->owner = User::create([
        'name' => 'Owner User',
        'email' => 'owner@example.com',
        'password' => bcrypt('password'),
        'role_id' => $this->ownerRole->id,
        'is_owner' => true,
    ]);
    
    $this->actingAs($this->owner);
});

test('user can view users index', function () {
    User::factory()->count(5)->create([
        'role_id' => $this->managerRole->id,
    ]);
    
    $response = $this->get(route('users.index'));
    
    $response->assertOk();
    $response->assertInertia(fn ($page) => 
        $page->component('users/Index')
            ->has('users.data', 6) // 5 created + 1 owner
    );
});

test('user can change another user role', function () {
    $user = User::factory()->create([
        'role_id' => $this->managerRole->id,
    ]);
    
    $attendantRole = Role::factory()->create([
        'name' => 'Attendant',
        'slug' => 'attendant',
    ]);
    
    $response = $this->post(route('users.change-role', $user), [
        'role_id' => $attendantRole->id,
    ]);
    
    $response->assertRedirect();
    
    $user->refresh();
    expect($user->role_id)->toBe($attendantRole->id);
});

test('cannot change owner role', function () {
    $attendantRole = Role::factory()->create([
        'name' => 'Attendant',
        'slug' => 'attendant',
    ]);
    
    $response = $this->post(route('users.change-role', $this->owner), [
        'role_id' => $attendantRole->id,
    ]);
    
    $response->assertSessionHasErrors();
    
    $this->owner->refresh();
    expect($this->owner->role_id)->toBe($this->ownerRole->id);
});

test('user can delete another user', function () {
    $user = User::factory()->create([
        'role_id' => $this->managerRole->id,
    ]);
    
    $response = $this->delete(route('users.destroy', $user));
    
    $response->assertRedirect();
    
    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});

test('cannot delete owner user', function () {
    $response = $this->delete(route('users.destroy', $this->owner));
    
    $response->assertSessionHasErrors();
    
    $this->assertDatabaseHas('users', [
        'id' => $this->owner->id,
    ]);
});

test('regular user can access user management', function () {
    // Note: Permission middleware not yet implemented
    // This test verifies basic access works
    $regularUser = User::factory()->create([
        'role_id' => $this->managerRole->id,
    ]);
    
    $this->actingAs($regularUser);
    
    $response = $this->get(route('users.index'));
    
    $response->assertOk();
});
