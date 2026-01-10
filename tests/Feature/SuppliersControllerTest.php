<?php

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TenantTestHelper;

uses(RefreshDatabase::class, TenantTestHelper::class);

beforeEach(function () {
    $this->initializeTenant();
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('index returns inertia response with suppliers', function () {
    Supplier::factory()->count(3)->create();

    $response = $this->get('/suppliers');

    $response->assertOk();
    $response->assertInertia(fn($page) => $page->component('suppliers/Index'));
});

test('store creates new supplier', function () {
    $data = [
        'name' => 'Test Supplier',
        'document_number' => '12.345.678/0001-90',
        'cnpj' => '12.345.678/0001-90',
        'email' => 'supplier@test.com',
        'phone' => '(11) 98765-4321',
        'is_active' => true,
    ];

    $response = $this->postJson('/suppliers', $data);

    $response->assertCreated();
    $this->assertDatabaseHas('suppliers', [
        'name' => 'Test Supplier',
        'email' => 'supplier@test.com',
    ]);
});

test('store validates name is required', function () {
    $response = $this->postJson('/suppliers', [
        'email' => 'test@test.com',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name']);
});

test('update modifies existing supplier', function () {
    $supplier = Supplier::factory()->create(['name' => 'Original Name']);

    $response = $this->putJson("/suppliers/{$supplier->id}", [
        'name' => 'Updated Name',
        'document_number' => $supplier->document_number, // Add required field        'is_active' => true,
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('suppliers', [
        'id' => $supplier->id,
        'name' => 'Updated Name',
    ]);
});

test('delete soft deletes supplier', function () {
    $supplier = Supplier::factory()->create();

    $response = $this->deleteJson("/suppliers/{$supplier->id}");

    $response->assertOk();
    $this->assertSoftDeleted('suppliers', ['id' => $supplier->id]);
});

test('find by filters searches suppliers', function () {
    // Ensure fresh state
    Supplier::query()->delete();

    Supplier::factory()->create(['name' => 'ABC Supplier']);
    Supplier::factory()->create(['name' => 'XYZ Supplier']);
    Supplier::factory()->create(['name' => 'ABC Parts']);

    $response = $this->getJson('/suppliers/filters?search=abc');

    $response->assertOk();
    $response->assertJsonCount(2, 'suppliers.items');
});
