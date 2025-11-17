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
        // Renomear tabela cars para vehicles
        Schema::rename('cars', 'vehicles');

        // Ajustar campo type para vehicle_type (se necessário adicionar mais especificidade)
        Schema::table('vehicles', function (Blueprint $table) {
            $table->renameColumn('type', 'vehicle_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter as mudanças
        Schema::table('vehicles', function (Blueprint $table) {
            $table->renameColumn('vehicle_type', 'type');
        });

        Schema::rename('vehicles', 'cars');
    }
};
