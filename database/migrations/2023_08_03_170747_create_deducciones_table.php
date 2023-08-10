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
        Schema::create('deducciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empleado_id');
            $table->string('nombre'); // Nombre de la deducción (ejemplo: "Seguro Médico")
            $table->decimal('monto', 10, 2); // Monto de la deducción
            $table->enum('tipo_deduccion', ['Fijo', 'Porcentaje']); // Tipo de deducción (Fijo, Porcentaje)
            $table->enum('estado', ['Activa', 'Inactiva']); // Estado de la deducción (Activa, Inactiva)
            $table->timestamps();

            // Foreign key constraint for empleado_id
            $table->foreign('empleado_id')->references('id')->on('empleados')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deducciones');
    }
};
