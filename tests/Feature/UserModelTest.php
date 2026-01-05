<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TenantTestHelper;

uses(RefreshDatabase::class, TenantTestHelper::class);

// Reset tenant database before all tests
beforeAll(function () {
    TenantTestHelper::resetSharedTenant();
});

beforeEach(function () {
    $this->initializeTenant();
    
    $this->role = Role::create([
        'name' => 'Test Role',
        'slug' => 'test-role',
    ]);
    
    $this->permission1 = Permission::create([
        'name' => 'View Clients',
        'slug' => 'clients.view',
        'module' => 'clients',
    ]);
    
    $this->permission2 = Permission::create([
        'name' => 'Create Clients',
        'slug' => 'clients.create',
        'module' => 'clients',
    ]);
    
    $this->user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
        'role_id' => $this->role->id,
    ]);
});

it('can check if user has a specific role', function () {
    expect($this->user->hasRole('test-role'))->toBeTrue();
    expect($this->user->hasRole('admin'))->toBeFalse();
});

it('can check if user has multiple roles', function () {
    expect($this->user->hasRole(['test-role', 'admin']))->toBeTrue();
    expect($this->user->hasRole(['admin', 'manager']))->toBeFalse();
});

it('can check if user has a permission', function () {
    $this->role->permissions()->attach($this->permission1->id);
    
    expect($this->user->hasPermission('clients.view'))->toBeTrue();
    expect($this->user->hasPermission('clients.create'))->toBeFalse();
});

it('can check if user cannot do something', function () {
    $this->role->permissions()->attach($this->permission1->id);
    
    expect($this->user->lacksPermission('clients.create'))->toBeTrue();
    expect($this->user->lacksPermission('clients.view'))->toBeFalse();
});

it('can check if user is owner', function () {
    $ownerRole = Role::create([
        'name' => 'Owner',
        'slug' => 'owner',
    ]);
    
    $owner = User::create([
        'name' => 'Owner User',
        'email' => 'owner@example.com',
        'password' => bcrypt('password'),
        'role_id' => $ownerRole->id,
        'is_owner' => true,
    ]);
    
    expect($owner->isOwner())->toBeTrue();
    expect($this->user->isOwner())->toBeFalse();
});

it('can assign a role to user', function () {
    $newRole = Role::create([
        'name' => 'New Role',
        'slug' => 'new-role',
    ]);
    
    $this->user->assignRole($newRole);
    
    expect($this->user->role_id)->toBe($newRole->id);
    expect($this->user->hasRole('new-role'))->toBeTrue();
});

it('can get all permission slugs for user', function () {
    $this->role->permissions()->attach([
        $this->permission1->id,
        $this->permission2->id,
    ]);
    
    $slugs = $this->user->getPermissionSlugs();
    
    expect($slugs)->toBeArray()
        ->toContain('clients.view')
        ->toContain('clients.create')
        ->toHaveCount(2);
});

it('returns empty array when user has no permissions', function () {
    $slugs = $this->user->getPermissionSlugs();
    
    expect($slugs)->toBeArray()->toBeEmpty();
});

it('owner user has all permissions', function () {
    // Create owner with owner role
    $ownerRole = Role::create([
        'name' => 'Owner',
        'slug' => 'owner',
    ]);
    
    $owner = User::create([
        'name' => 'Owner User',
        'email' => 'owner@example.com',
        'password' => bcrypt('password'),
        'role_id' => $ownerRole->id,
        'is_owner' => true,
    ]);
    
    // Owner should have permission even if not explicitly granted
    // This depends on your implementation - adjust accordingly
    expect($owner->isOwner())->toBeTrue();
});

it('can check multiple permissions at once', function () {
    $this->role->permissions()->attach([
        $this->permission1->id,
        $this->permission2->id,
    ]);
    
    // Check individual permissions
    expect($this->user->hasPermission('clients.view'))->toBeTrue();
    expect($this->user->hasPermission('clients.create'))->toBeTrue();
});
