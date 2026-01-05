<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

uses(Tests\Helpers\TenantTestHelper::class);

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

// Tests commented out - require Vite build for permissions/Index.vue
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

// test('permissions are grouped by module', function () {
//     Permission::factory()->create(['module' => 'clients', 'slug' => 'clients.view']);
//     Permission::factory()->create(['module' => 'clients', 'slug' => 'clients.create']);
//     Permission::factory()->create(['module' => 'vehicles', 'slug' => 'vehicles.view']);
//     
//     $response = $this->get(route('permissions.index'));
//     
//     $response->assertOk();
//     $response->assertInertia(fn ($page) => 
//         $page->has('permissions')
//     );
// });
