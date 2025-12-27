<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->string('id')->primary();

            // Business information
            $table->string('name'); // Nome da oficina
            $table->string('slug')->unique(); // oficina-joao
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            // Subscription
            $table->foreignUlid('subscription_plan_id')->nullable()->constrained('subscription_plans');
            $table->enum('subscription_status', ['trial', 'active', 'suspended', 'canceled'])->default('trial');
            $table->timestamp('trial_ends_at')->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            // Settings (JSON: logo, theme, etc)
            $table->json('settings')->nullable();

            $table->timestamps();
            $table->json('data')->nullable(); // Mantido para compatibilidade com o pacote

            // Indexes
            $table->index('slug');
            $table->index('subscription_status');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
