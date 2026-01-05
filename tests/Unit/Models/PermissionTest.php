<?php

use App\Models\Permission;

uses(Tests\Helpers\TenantTestHelper::class);

beforeEach(function () {
    $this->initializeTenant();
});

it('can create permission', function () {
    $permission = Permission::factory()->create([
        'name' => 'View Clients',
        'slug' => 'clients.view',
        'module' => 'clients',
    ]);
    
    expect($permission)->toBeInstanceOf(Permission::class)
        ->name->toBe('View Clients')
        ->slug->toBe('clients.view')
        ->module->toBe('clients');
});

it('has unique slug', function () {
    Permission::factory()->create(['slug' => 'test.permission']);
    
    expect(fn() => Permission::factory()->create(['slug' => 'test.permission']))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

it('can belong to multiple roles', function () {
    $permission = Permission::factory()->create();
    $role1 = \App\Models\Role::factory()->create();
    $role2 = \App\Models\Role::factory()->create();
    
    $permission->roles()->attach([$role1->id, $role2->id]);
    
    expect($permission->roles)->toHaveCount(2);
});
