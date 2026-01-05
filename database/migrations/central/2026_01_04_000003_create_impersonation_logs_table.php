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
        Schema::connection('central')->create('impersonation_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignId('admin_user_id')->constrained()->onDelete('cascade');
            $table->string('admin_email');
            $table->string('tenant_id');
            $table->string('tenant_name');
            $table->string('target_user_id');
            $table->string('target_user_name');
            $table->string('target_user_email');
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['admin_user_id', 'tenant_id']);
            $table->index('started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('central')->dropIfExists('impersonation_logs');
    }
};
