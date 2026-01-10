<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_supplier', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // Foreign Keys
            $table->foreignUlid('product_id')
                ->constrained('products')
                ->onDelete('cascade');
            $table->foreignUlid('supplier_id')
                ->constrained('suppliers')
                ->onDelete('cascade');

            // Dados do fornecedor para este produto
            $table->string('supplier_sku')->nullable();
            $table->decimal('cost_price', 10, 2);
            $table->integer('lead_time_days')->nullable();
            $table->integer('min_order_quantity')->default(1);
            $table->boolean('is_preferred')->default(false);
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->unique(['product_id', 'supplier_id']);
            $table->index('is_preferred');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_supplier');
    }
};
