<?php

use App\Models\Role;
use App\Models\Permission;
use App\Services\RoleService;
use App\DTOs\RoleInputDTO;

uses(Tests\Helpers\TenantTestHelper::class);

beforeEach(function () {
    $this->initializeTenant();
    
    $this->roleService = app(RoleService::class);
    
    $this->permission1 = Permission::factory()->create([
        'slug' => 'clients.view',
        'module' => 'clients',
    ]);
    
    $this->permission2 = Permission::factory()->create([
        'slug' => 'clients.create',
        'module' => 'clients',
    ]);
});

it('can create a role', function () {
    $dto = new RoleInputDTO(
        name: 'Custom Role',
        description: 'A custom role for testing'
    );
    
    $role = $this->roleService->create($dto);
    
    expect($role)->toBeInstanceOf(Role::class)
        ->name->toBe('Custom Role')
        ->slug->toBe('custom-role')
        ->description->toBe('A custom role for testing')
        ->is_system->toBeFalse();
});

it('can update a role', function () {
    $role = Role::factory()->create([
        'name' => 'Old Name',
        'is_system' => false,
    ]);
    
    $dto = new RoleInputDTO(
        name: 'New Name',
        description: 'Updated description'
    );
    
    $updatedRole = $this->roleService->update($role->id, $dto);
    
    expect($updatedRole->name)->toBe('New Name')
        ->and($updatedRole->description)->toBe('Updated description');
});

it('can delete a custom role', function () {
    $role = Role::factory()->create([
        'is_system' => false,
    ]);
    
    $this->roleService->delete($role->id);
    
    expect(Role::find($role->id))->toBeNull();
});

it('cannot delete a system role', function () {
    $role = Role::factory()->create([
        'is_system' => true,
    ]);
    
    $this->roleService->delete($role->id);
})->throws(\App\Exceptions\CannotDeleteSystemRoleException::class);

it('can sync permissions to role', function () {
    $role = Role::factory()->create();
    
    $this->roleService->syncPermissions($role->id, [
        $this->permission1->id,
        $this->permission2->id,
    ]);
    
    $role->refresh();
    expect($role->permissions)->toHaveCount(2);
});

it('can remove all permissions from role', function () {
    $role = Role::factory()->create();
    $role->permissions()->attach([$this->permission1->id, $this->permission2->id]);
    
    $this->roleService->syncPermissions($role->id, []);
    
    $role->refresh();
    expect($role->permissions)->toHaveCount(0);
});

it('generates correct slug from name', function () {
    $dto = new RoleInputDTO(
        name: 'Test Role Name',
        description: 'Test'
    );
    
    $role = $this->roleService->create($dto);
    
    expect($role->slug)->toBe('test-role-name');
});
