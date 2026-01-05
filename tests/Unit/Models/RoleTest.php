<?php

use App\Models\Role;
use App\Models\Permission;

uses(Tests\Helpers\TenantTestHelper::class);

beforeEach(function () {
    $this->initializeTenant();
});

it('can create role', function () {
    $role = Role::factory()->create([
        'name' => 'Test Role',
        'slug' => 'test-role',
    ]);
    
    expect($role)->toBeInstanceOf(Role::class)
        ->name->toBe('Test Role')
        ->slug->toBe('test-role');
});

it('has unique slug', function () {
    Role::factory()->create(['slug' => 'test-role']);
    
    expect(fn() => Role::factory()->create(['slug' => 'test-role']))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

it('can have multiple permissions', function () {
    $role = Role::factory()->create();
    $permission1 = Permission::factory()->create();
    $permission2 = Permission::factory()->create();
    
    $role->permissions()->attach([$permission1->id, $permission2->id]);
    
    expect($role->permissions)->toHaveCount(2);
});

it('can have many users', function () {
    $role = Role::factory()->create();
    $user1 = \App\Models\User::factory()->create(['role_id' => $role->id]);
    $user2 = \App\Models\User::factory()->create(['role_id' => $role->id]);
    
    expect($role->users)->toHaveCount(2);
});

it('system roles have is_system flag', function () {
    $systemRole = Role::factory()->create(['is_system' => true]);
    $customRole = Role::factory()->create(['is_system' => false]);
    
    expect($systemRole->is_system)->toBeTrue()
        ->and($customRole->is_system)->toBeFalse();
});
