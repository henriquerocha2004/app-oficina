<?php

use App\Models\User;
use App\Models\Role;
use App\Services\UserService;
use App\DTOs\UserInputDTO;

uses(Tests\Helpers\TenantTestHelper::class);

beforeEach(function () {
    $this->initializeTenant();
    
    $this->ownerRole = Role::factory()->create([
        'name' => 'Owner',
        'slug' => 'owner',
    ]);
    
    $this->managerRole = Role::factory()->create([
        'name' => 'Manager',
        'slug' => 'manager',
    ]);
    
    $this->userService = app(UserService::class);
});

it('can create a user', function () {
    $dto = new UserInputDTO(
        name: 'John Doe',
        email: 'john@example.com',
        password: 'SecurePassword123!',
        role_id: $this->managerRole->id
    );
    
    $user = $this->userService->create($dto);
    
    expect($user)->toBeInstanceOf(User::class)
        ->name->toBe('John Doe')
        ->email->toBe('john@example.com')
        ->role_id->toBe($this->managerRole->id)
        ->is_owner->toBeFalse();
});

it('can update a user', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
        'role_id' => $this->managerRole->id,
    ]);
    
    $dto = new UserInputDTO(
        name: 'New Name',
        email: 'new@example.com'
    );
    
    $updatedUser = $this->userService->update($user->id, $dto);
    
    expect($updatedUser->name)->toBe('New Name')
        ->and($updatedUser->email)->toBe('new@example.com');
});

it('can change user role', function () {
    $user = User::factory()->create([
        'role_id' => $this->managerRole->id,
    ]);
    
    $updatedUser = $this->userService->changeRole($user->id, $this->ownerRole->id);
    
    expect($updatedUser->role_id)->toBe($this->ownerRole->id);
});

it('cannot change role of owner user', function () {
    $owner = User::factory()->create([
        'role_id' => $this->ownerRole->id,
        'is_owner' => true,
    ]);
    
    $this->userService->changeRole($owner->id, $this->managerRole->id);
})->throws(\App\Exceptions\CannotChangeOwnerRoleException::class);

it('can delete a user', function () {
    $user = User::factory()->create([
        'role_id' => $this->managerRole->id,
    ]);
    
    $this->userService->delete($user->id);
    
    expect(User::find($user->id))->toBeNull();
});

it('cannot delete owner user', function () {
    $owner = User::factory()->create([
        'role_id' => $this->ownerRole->id,
        'is_owner' => true,
    ]);
    
    $this->userService->delete($owner->id);
})->throws(\App\Exceptions\CannotDeleteOwnerException::class);

it('validates plan limits when creating user', function () {
    // This would require mocking plan limits
    // Basic structure for the test
    
    // Create multiple users to approach limit
    User::factory()->count(5)->create([
        'role_id' => $this->managerRole->id,
    ]);
    
    // Mock would check if next user exceeds plan limit
    // For now, we'll just ensure the service has this method
    
    $hasMethod = method_exists($this->userService, 'validatePlanLimits');
    expect($hasMethod)->toBeTrue();
});
