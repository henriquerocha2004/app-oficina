<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
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
    
    $this->owner = User::factory()->create([
        'role_id' => $this->ownerRole->id,
        'is_owner' => true,
    ]);
    
    $this->actingAs($this->owner);
});

test('user can view roles index', function () {
    Role::factory()->count(3)->create();
    
    $response = $this->get(route('roles.index'));
    
    $response->assertOk();
    $response->assertInertia(fn ($page) => 
        $page->component('roles/Index')
            ->has('roles.data', 4) // 3 created + 1 owner role
    );
});

test('user can create a custom role', function () {
    $response = $this->post(route('roles.store'), [
        'name' => 'Custom Role',
        'description' => 'A custom role for testing',
    ]);
    
    $response->assertRedirect();
    
    $this->assertDatabaseHas('roles', [
        'name' => 'Custom Role',
        'slug' => 'custom-role',
        'description' => 'A custom role for testing',
        'is_system' => false,
    ]);
});

test('user can update a custom role', function () {
    $role = Role::factory()->create([
        'name' => 'Old Name',
        'slug' => 'old-name',
        'is_system' => false,
    ]);
    
    $response = $this->put(route('roles.update', $role), [
        'name' => 'New Name',
        'slug' => 'new-name',
        'description' => 'Updated description',
    ]);
    
    $response->assertRedirect();
    
    $role->refresh();
    expect($role->name)->toBe('New Name');
    expect($role->slug)->toBe('new-name');
    expect($role->description)->toBe('Updated description');
});

test('cannot update system role name', function () {
    $response = $this->put(route('roles.update', $this->ownerRole), [
        'name' => 'Hacked Owner',
        'description' => 'New description',
    ]);
    
    // Should either fail validation or just not update the name
    $this->ownerRole->refresh();
    expect($this->ownerRole->name)->toBe('Owner');
});

test('user can delete custom role', function () {
    $role = Role::factory()->create([
        'is_system' => false,
    ]);
    
    $response = $this->delete(route('roles.destroy', $role));
    
    $response->assertRedirect();
    
    $this->assertDatabaseMissing('roles', [
        'id' => $role->id,
    ]);
});

test('cannot delete system role', function () {
    $response = $this->delete(route('roles.destroy', $this->ownerRole));
    
    $response->assertSessionHasErrors();
    
    $this->assertDatabaseHas('roles', [
        'id' => $this->ownerRole->id,
    ]);
});

test('user can sync permissions to role', function () {
    $role = Role::factory()->create();
    
    $permission1 = Permission::factory()->create();
    $permission2 = Permission::factory()->create();
    $permission3 = Permission::factory()->create();
    
    $response = $this->post(route('roles.sync-permissions', $role), [
        'permission_ids' => [$permission1->id, $permission2->id],
    ]);
    
    $response->assertRedirect();
    
    $role->refresh();
    expect($role->permissions)->toHaveCount(2);
    expect($role->permissions->pluck('id')->toArray())
        ->toContain($permission1->id)
        ->toContain($permission2->id)
        ->not->toContain($permission3->id);
});

test('can remove all permissions from role', function () {
    $role = Role::factory()->create();
    
    $permission = Permission::factory()->create();
    $role->permissions()->attach($permission->id);
    
    $response = $this->post(route('roles.sync-permissions', $role), [
        'permission_ids' => [],
    ]);
    
    $response->assertRedirect();
    
    $role->refresh();
    expect($role->permissions)->toHaveCount(0);
});

// This test requires Vite build - skipped for now
// test('user can view permissions index', function () {
//     Permission::factory()->count(5)->create();
//     
//     $response = $this->get(route('permissions.index'));
//     
//     $response->assertOk();
//     $response->assertInertia(fn ($page) => 
//         $page->component('permissions/Index')
//             ->has('permissions')
//     );
// });
