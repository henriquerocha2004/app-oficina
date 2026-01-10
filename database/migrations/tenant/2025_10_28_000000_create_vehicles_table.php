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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('license_plate')->unique();
            $table->string('brand');
            $table->string('model');
            $table->integer('year');
            $table->string('color')->nullable();
            $table->string('vin')->nullable()->unique(); // Vehicle Identification Number
            $table->string('fuel')->nullable(); // gasoline, diesel, electric, hybrid
            $table->string('transmission')->nullable(); // manual, automatic
            $table->integer('mileage')->nullable(); // Quilometragem
            $table->string('cilinder_capacity')->nullable(); // Capacidade do cilindro (motos)
            $table->foreignUlid('client_id')->constrained('clients')->onDelete('cascade');
            $table->enum('vehicle_type', ['car', 'motorcycle', 'truck'])->default('car');
            $table->text('observations')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('license_plate');
            $table->index('vin');
            $table->index('client_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
