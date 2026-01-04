<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // Relacionamento
            $table->foreignUlid('product_id')
                ->constrained('products')
                ->onDelete('cascade');

            // Tipo de movimentação
            $table->enum('movement_type', ['in', 'out']);

            // Quantidade
            $table->integer('quantity');
            $table->integer('balance_after')->nullable();

            // Referência (o que causou esta movimentação)
            $table->string('reference_type')->nullable();
            $table->string('reference_id')->nullable();

            // Detalhes
            $table->enum('reason', [
                'purchase',      // Compra de fornecedor
                'sale',          // Venda/Uso em OS
                'adjustment',    // Ajuste manual
                'loss',          // Perda/Quebra
                'return',        // Devolução de cliente
                'transfer',      // Transferência (futuro)
                'initial',       // Estoque inicial
                'other'          // Outros
            ]);
            $table->text('notes')->nullable();

            // Auditoria
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // Indexes
            $table->index('product_id');
            $table->index('movement_type');
            $table->index('reason');
            $table->index(['reference_type', 'reference_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
