<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // Identificação
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('sku', 50)->unique()->nullable();
            $table->string('barcode', 50)->unique()->nullable();
            $table->string('manufacturer')->nullable();

            // Categorização
            $table->enum('category', [
                'engine',        // Motor
                'suspension',    // Suspensão
                'brakes',        // Freios
                'electrical',    // Elétrica
                'filters',       // Filtros
                'fluids',        // Fluidos (óleo, líquidos)
                'tires',         // Pneus
                'body_parts',    // Lataria/Carroceria
                'interior',      // Interior
                'tools',         // Ferramentas
                'other'          // Outros
            ])->default('other');

            // Estoque
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock_level')->nullable();
            $table->enum('unit', ['unit', 'liter', 'kg', 'meter', 'box'])->default('unit');

            // Precificação
            $table->decimal('unit_price', 10, 2);
            $table->decimal('suggested_price', 10, 2)->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('sku');
            $table->index('barcode');
            $table->index('category');
            $table->index('is_active');
            $table->index(['stock_quantity', 'min_stock_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
