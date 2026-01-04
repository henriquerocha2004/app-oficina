<?php

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TenantTestHelper;

uses(RefreshDatabase::class, TenantTestHelper::class);

beforeEach(function () {
    $this->initializeTenant();
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('index returns inertia response', function () {
    $response = $this->get('/stock-movements');

    $response->assertOk();
    $response->assertInertia(fn($page) => $page->component('stock-movements/Index'));
});

test('find by filters returns stock movements', function () {
    // Clean existing data
    StockMovement::query()->forceDelete();
    Product::query()->delete();

    $product = Product::factory()->create();

    StockMovement::factory()->count(5)->create([
        'product_id' => $product->id,
        'movement_type' => 'in',
    ]);

    StockMovement::factory()->count(3)->create([
        'product_id' => $product->id,
        'movement_type' => 'out',
    ]);

    $response = $this->getJson('/stock-movements/filters');

    $response->assertOk();
    $response->assertJsonCount(8, 'movements.items');
});

test('find by filters filters by movement type', function () {
    // Clean existing data
    StockMovement::query()->forceDelete();
    Product::query()->delete();

    $product = Product::factory()->create();

    StockMovement::factory()->count(5)->create([
        'product_id' => $product->id,
        'movement_type' => 'in',
    ]);

    StockMovement::factory()->count(3)->create([
        'product_id' => $product->id,
        'movement_type' => 'out',
    ]);

    $response = $this->getJson('/stock-movements/filters?movement_type=in');

    $response->assertOk();
    $response->assertJsonCount(5, 'movements.items');
});

test('find by filters filters by reason', function () {
    // Clean existing data
    StockMovement::query()->forceDelete();
    Product::query()->delete();

    $product = Product::factory()->create();

    StockMovement::factory()->count(3)->create([
        'product_id' => $product->id,
        'reason' => 'purchase',
    ]);

    StockMovement::factory()->count(2)->create([
        'product_id' => $product->id,
        'reason' => 'sale',
    ]);

    $response = $this->getJson('/stock-movements/filters?reason=purchase');

    $response->assertOk();
    $response->assertJsonCount(3, 'movements.items');
});

test('find by filters filters by product id', function () {
    $product1 = Product::factory()->create();
    $product2 = Product::factory()->create();

    StockMovement::factory()->count(5)->create(['product_id' => $product1->id]);
    StockMovement::factory()->count(3)->create(['product_id' => $product2->id]);

    $response = $this->getJson("/stock-movements/filters?product_id={$product1->id}");

    $response->assertOk();
    $response->assertJsonCount(5, 'movements.items');
});

test('get by product returns movements for specific product', function () {
    $product = Product::factory()->create();

    StockMovement::factory()->count(5)->create(['product_id' => $product->id]);
    StockMovement::factory()->count(3)->create(); // Other product

    $response = $this->getJson("/stock-movements/product/{$product->id}");

    $response->assertOk();
    $response->assertJsonCount(5, 'movements');
});
