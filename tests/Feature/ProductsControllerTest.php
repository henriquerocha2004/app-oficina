<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Str;
use App\Models\StockMovement;
use Tests\Helpers\TenantTestHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\{actingAs, get, postJson, putJson, deleteJson, assertDatabaseHas, assertDatabaseMissing, assertSoftDeleted, getJson};

uses(RefreshDatabase::class, TenantTestHelper::class);

beforeEach(function () {
    $this->initializeTenant();
    $this->user = User::factory()->create();
    actingAs($this->user);
});

test('index returns inertia response with products', function () {
    Product::factory()->count(3)->create();

    $response = get('/products');

    $response->assertOk();
    $response->assertInertia(fn($page) => $page->component('products/Index'));
});

test('store creates new product', function () {
    $data = [
        'name' => 'Test Product',
        'sku' => 'TEST-001',
        'category' => 'engine',
        'unit' => 'unit',
        'stock_quantity' => 10,
        'min_stock_level' => 5,
        'unit_price' => 99.99,
        'is_active' => true,
    ];

    $response = postJson('/products', $data);

    $response->assertCreated();
    $response->assertJsonStructure([
        'message',
        'product_id',
    ]);

    assertDatabaseHas('products', [
        'name' => 'Test Product',
        'sku' => 'TEST-001',
    ]);
});

test('store validates required fields', function () {
    $response = postJson('/products', []);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name', 'category', 'unit', 'stock_quantity', 'unit_price']);
});

test('store validates sku uniqueness', function () {
    Product::factory()->create(['sku' => 'DUPLICATE-SKU']);

    $response = postJson('/products', [
        'name' => 'Test Product',
        'sku' => 'DUPLICATE-SKU',
        'category' => 'engine',
        'unit' => 'unit',
        'stock_quantity' => 10,
        'min_stock_level' => 5,
        'unit_price' => 99.99,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['sku']);
});

test('update modifies existing product', function () {
    $product = Product::factory()->create([
        'name' => 'Original Name',
        'stock_quantity' => 10,
    ]);

    $response = putJson("/products/{$product->id}", [
        'name' => 'Updated Name',
        'sku' => $product->sku,
        'category' => $product->category,
        'unit' => $product->unit,
        'stock_quantity' => 20,
        'min_stock_level' => $product->min_stock_level,
        'unit_price' => $product->unit_price,
        'is_active' => true,
    ]);

    $response->assertOk();
    assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Updated Name',
        'stock_quantity' => 20,
    ]);
});

test('delete soft deletes product', function () {
    $product = Product::factory()->create();

    $response = deleteJson("/products/{$product->id}");

    $response->assertOk();
    assertSoftDeleted('products', ['id' => $product->id]);
});

test('adjust stock endpoint increases stock', function () {
    $product = Product::factory()->create(['stock_quantity' => 10]);

    $response = postJson("/products/{$product->id}/adjust-stock", [
        'movement_type' => 'in',
        'quantity' => 5,
        'reason' => 'purchase',
        'notes' => 'Test purchase',
    ]);

    $response->assertOk();
    expect($product->fresh()->stock_quantity)->toBe(15);
});

test('adjust stock endpoint decreases stock', function () {
    $product = Product::factory()->create(['stock_quantity' => 10]);

    $response = postJson("/products/{$product->id}/adjust-stock", [
        'movement_type' => 'out',
        'quantity' => 3,
        'reason' => 'sale',
    ]);

    $response->assertOk();
    expect($product->fresh()->stock_quantity)->toBe(7);
});

test('adjust stock validates insufficient stock', function () {
    StockMovement::query()->forceDelete();
    Product::query()->delete();

    $product = Product::factory()->create(['stock_quantity' => 5]);

    $response = postJson("/products/{$product->id}/adjust-stock", [
        'movement_type' => 'out',
        'quantity' => 10,
        'reason' => 'sale',
    ]);

    $response->assertStatus(422);
    expect($product->fresh()->stock_quantity)->toBe(5);
});

test('get low stock returns products below minimum', function () {
    StockMovement::query()->forceDelete();
    Product::query()->delete();

    Product::factory()->create([
        'stock_quantity' => 5,
        'min_stock_level' => 10,
    ]);

    Product::factory()->create([
        'stock_quantity' => 15,
        'min_stock_level' => 10,
    ]);

    $response = getJson('/products/low-stock');

    $response->assertOk();
    $response->assertJsonCount(1, 'products');
});

test('find by filters searches products', function () {
    Product::factory()->create(['name' => 'Motor Oil Filter']);
    Product::factory()->create(['name' => 'Brake Pads']);
    Product::factory()->create(['name' => 'Oil Filter']);

    $response = getJson('/products/filters?search=oil');

    $response->assertOk();
    $response->assertJsonCount(2, 'products.items');
});

// Product-Supplier Association Tests
test('get suppliers returns all suppliers for a product', function () {
    $product = Product::factory()->create();
    $supplier1 = \App\Models\Supplier::factory()->create(['name' => 'Supplier One']);
    $supplier2 = \App\Models\Supplier::factory()->create(['name' => 'Supplier Two']);

    $product->suppliers()->attach($supplier1->id, [
        'id' => \Illuminate\Support\Str::ulid()->toString(),
        'cost_price' => 100.00,
        'supplier_sku' => 'SUP-SKU-001',
        'lead_time_days' => 5,
        'min_order_quantity' => 10,
        'is_preferred' => true,
    ]);

    $product->suppliers()->attach($supplier2->id, [
        'id' => \Illuminate\Support\Str::ulid()->toString(),
        'cost_price' => 95.00,
        'supplier_sku' => 'SUP-SKU-002',
        'lead_time_days' => 7,
        'min_order_quantity' => 5,
        'is_preferred' => false,
    ]);

    $response = getJson("/products/{$product->id}/suppliers");

    $response->assertOk();
    $response->assertJsonCount(2, 'suppliers');
    $response->assertJsonFragment(['name' => 'Supplier One']);
    $response->assertJsonFragment(['cost_price' => 100.00]);
});

test('attach supplier creates product-supplier relationship', function () {
    $product = Product::factory()->create();
    $supplier = \App\Models\Supplier::factory()->create();

    $data = [
        'supplier_id' => $supplier->id,
        'cost_price' => 150.00,
        'supplier_sku' => 'TEST-SKU-123',
        'lead_time_days' => 10,
        'min_order_quantity' => 20,
        'is_preferred' => true,
        'notes' => 'Primary supplier for this product',
    ];

    $response = postJson("/products/{$product->id}/suppliers", $data);

    $response->assertCreated();
    $response->assertJson(['message' => 'Fornecedor vinculado com sucesso']);

    assertDatabaseHas('product_supplier', [
        'product_id' => $product->id,
        'supplier_id' => $supplier->id,
        'cost_price' => 150.00,
        'supplier_sku' => 'TEST-SKU-123',
        'is_preferred' => true,
    ]);
});

test('attach supplier validates required fields', function () {
    $product = Product::factory()->create();

    $response = postJson("/products/{$product->id}/suppliers", []);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['supplier_id', 'cost_price', 'min_order_quantity']);
});

test('attach supplier validates cost price must be non-negative', function () {
    $product = Product::factory()->create();
    $supplier = \App\Models\Supplier::factory()->create();

    $response = postJson("/products/{$product->id}/suppliers", [
        'supplier_id' => $supplier->id,
        'cost_price' => -10.00,
        'min_order_quantity' => 1,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['cost_price']);
});

test('update supplier updates product-supplier relationship', function () {
    $product = Product::factory()->create();
    $supplier = Supplier::factory()->create();

    $product->suppliers()->attach($supplier->id, [
        'id' => Str::ulid()->toString(),
        'cost_price' => 100.00,
        'lead_time_days' => 5,
        'min_order_quantity' => 10,
        'is_preferred' => false,
    ]);

    $data = [
        'cost_price' => 120.00,
        'supplier_sku' => 'UPDATED-SKU',
        'lead_time_days' => 3,
        'min_order_quantity' => 15,
        'is_preferred' => true,
        'notes' => 'Updated notes',
    ];

    $response = putJson("/products/{$product->id}/suppliers/{$supplier->id}", $data);

    $response->assertOk();
    $response->assertJson(['message' => 'Fornecedor atualizado com sucesso']);

    assertDatabaseHas('product_supplier', [
        'product_id' => $product->id,
        'supplier_id' => $supplier->id,
        'cost_price' => 120.00,
        'supplier_sku' => 'UPDATED-SKU',
        'is_preferred' => true,
    ]);
});

test('detach supplier removes product-supplier relationship', function () {
    $product = Product::factory()->create();
    $supplier = \App\Models\Supplier::factory()->create();

    $product->suppliers()->attach($supplier->id, [
        'id' => \Illuminate\Support\Str::ulid()->toString(),
        'cost_price' => 100.00,
        'min_order_quantity' => 10,
    ]);

    assertDatabaseHas('product_supplier', [
        'product_id' => $product->id,
        'supplier_id' => $supplier->id,
    ]);

    $response = deleteJson("/products/{$product->id}/suppliers/{$supplier->id}");

    $response->assertOk();
    $response->assertJson(['message' => 'Fornecedor removido com sucesso']);

    assertDatabaseMissing('product_supplier', [
        'product_id' => $product->id,
        'supplier_id' => $supplier->id,
    ]);
});

test('cannot attach same supplier twice to a product', function () {
    $product = Product::factory()->create();
    $supplier = \App\Models\Supplier::factory()->create();

    $product->suppliers()->attach($supplier->id, [
        'id' => \Illuminate\Support\Str::ulid()->toString(),
        'cost_price' => 100.00,
        'min_order_quantity' => 10,
    ]);

    $data = [
        'supplier_id' => $supplier->id,
        'cost_price' => 150.00,
        'min_order_quantity' => 20,
    ];

    $response = postJson("/products/{$product->id}/suppliers", $data);

    $response->assertStatus(422);
});
