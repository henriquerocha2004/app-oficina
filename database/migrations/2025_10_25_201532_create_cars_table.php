<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('brand');
            $table->string('model');
            $table->integer('year');
            $table->string('type');
            $table->string('licence_plate')->nullable();
            $table->string('vin')->nullable();
            $table->string('transmission')->nullable();
            $table->string('color')->nullable();
            $table->integer('cilinder_capacity')->nullable();
            $table->integer('mileage')->nullable();
            $table->text('observations')->nullable();
            $table->ulid('client_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('clients');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
