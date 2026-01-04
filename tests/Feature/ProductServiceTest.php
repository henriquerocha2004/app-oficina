<?php

use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TenantTestHelper;

uses(RefreshDatabase::class, TenantTestHelper::class);

beforeEach(function () {
    $this->initializeTenant();
    $this->service = app(ProductService::class);
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('adjust stock in increases stock quantity', function () {
    $product = Product::factory()->create([
        'stock_quantity' => 10,
    ]);

    $result = $this->service->adjustStock(
        $product->id,
        5,
        'in',
        'purchase',
        'Compra de teste'
    );

    expect($result)->toBeInstanceOf(StockMovement::class);
    expect($product->fresh()->stock_quantity)->toBe(15);
});

test('adjust stock out decreases stock quantity', function () {
    $product = Product::factory()->create([
        'stock_quantity' => 10,
    ]);

    $result = $this->service->adjustStock(
        $product->id,
        3,
        'out',
        'sale',
        'Venda de teste'
    );

    expect($result)->toBeInstanceOf(StockMovement::class);
    expect($product->fresh()->stock_quantity)->toBe(7);
});

test('adjust stock creates stock movement record', function () {
    $product = Product::factory()->create([
        'stock_quantity' => 10,
    ]);

    $this->service->adjustStock(
        $product->id,
        5,
        'in',
        'purchase',
        'Compra de teste'
    );

    $movement = StockMovement::where('product_id', $product->id)->first();

    expect($movement)->not->toBeNull();
    expect($movement->movement_type)->toBe('in');
    expect($movement->quantity)->toBe(5);
    expect($movement->balance_after)->toBe(15);
    expect($movement->reason)->toBe('purchase');
    expect($movement->notes)->toBe('Compra de teste');
    expect($movement->user_id)->toBe($this->user->id);
});

test('adjust stock out throws exception when insufficient stock', function () {
    $product = Product::factory()->create([
        'stock_quantity' => 5,
    ]);

    expect(fn() => $this->service->adjustStock(
        $product->id,
        10,
        'out',
        'sale'
    ))->toThrow(InsufficientStockException::class, 'Estoque insuficiente');

    expect($product->fresh()->stock_quantity)->toBe(5);
});

test('adjust stock maintains correct balance after snapshot', function () {
    $product = Product::factory()->create([
        'stock_quantity' => 100,
    ]);

    // Multiple movements
    $this->service->adjustStock($product->id, 50, 'in', 'purchase');
    $this->service->adjustStock($product->id, 30, 'out', 'sale');
    $this->service->adjustStock($product->id, 20, 'in', 'return');

    $movements = StockMovement::where('product_id', $product->id)
        ->orderBy('created_at')
        ->get();

    expect($movements[0]->balance_after)->toBe(150);
    expect($movements[1]->balance_after)->toBe(120);
    expect($movements[2]->balance_after)->toBe(140);
    expect($product->fresh()->stock_quantity)->toBe(140);
});

test('recalculate stock corrects discrepancies', function () {
    // Clean existing movements to avoid interference
    StockMovement::query()->forceDelete();
    Product::query()->delete();

    $product = Product::factory()->create([
        'stock_quantity' => 0,
    ]);

    // Create movements manually - starting from 0
    StockMovement::factory()->create([
        'product_id' => $product->id,
        'movement_type' => 'in',
        'quantity' => 50,
        'balance_after' => 50,
        'created_at' => now()->subDays(2),
    ]);

    StockMovement::factory()->create([
        'product_id' => $product->id,
        'movement_type' => 'out',
        'quantity' => 20,
        'balance_after' => 30,
        'created_at' => now()->subDays(1),
    ]);

    // Manually set wrong quantity
    $product->update(['stock_quantity' => 999]);

    $correctedStock = $this->service->recalculateStock($product->id);

    // Expected: 0 + 50 (in) - 20 (out) = 30
    expect($correctedStock)->toBe(30);
    expect($product->fresh()->stock_quantity)->toBe(30);
});

test('get low stock products returns only products below minimum', function () {
    // Clean existing products including soft deleted
    Product::query()->forceDelete();

    Product::factory()->create([
        'name' => 'Product 1',
        'stock_quantity' => 5,
        'min_stock_level' => 10,
    ]);

    Product::factory()->create([
        'name' => 'Product 2',
        'stock_quantity' => 15,
        'min_stock_level' => 10,
    ]);

    Product::factory()->create([
        'name' => 'Product 3',
        'stock_quantity' => 3,
        'min_stock_level' => 5,
    ]);

    $lowStockProducts = $this->service->getLowStockProducts();

    expect($lowStockProducts)->toHaveCount(2);
    expect($lowStockProducts->pluck('name')->toArray())->toContain('Product 1', 'Product 3');
});

test('adjust stock transaction rolls back on error', function () {
    $product = Product::factory()->create([
        'stock_quantity' => 10,
    ]);

    // Mock a database error during movement creation
    StockMovement::creating(function () {
        throw new \Exception('Database error');
    });

    try {
        $this->service->adjustStock($product->id, 5, 'in', 'purchase');
    } catch (\Exception $e) {
        // Expected
    }

    // Stock should not have changed due to transaction rollback
    expect($product->fresh()->stock_quantity)->toBe(10);
    expect(StockMovement::where('product_id', $product->id)->count())->toBe(0);
});
