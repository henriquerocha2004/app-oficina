<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->ulid('id')->primary();
            
            // Plan information
            $table->string('name'); // Gratuito, Básico, Premium
            $table->string('slug')->unique(); // free, basic, premium
            $table->text('description')->nullable();
            
            // Pricing
            $table->decimal('price_monthly', 10, 2)->default(0);
            $table->decimal('price_yearly', 10, 2)->default(0);
            
            // Limits (JSON)
            $table->json('limits')->nullable();
            // Example: {"max_users": 3, "max_clients": 500, "max_vehicles": 1000, "max_orders_per_month": null}
            
            // Features (JSON)
            $table->json('features')->nullable();
            // Example: ["reports", "api_access", "priority_support"]
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_visible')->default(true); // Mostrar na página de preços
            
            // Order (para ordenação na exibição)
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            
            // Indexes
            $table->index('slug');
            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
