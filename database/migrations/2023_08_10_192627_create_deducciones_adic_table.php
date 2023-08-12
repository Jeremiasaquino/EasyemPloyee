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
        Schema::create('dedduciones_adic', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empleado_id');
            $table->string('nombre'); // Nombre de la deducción adicional (ejemplo: "Cafetería")
            $table->decimal('monto', 10, 2); // Monto de la deducción adicional
            $table->enum('tipo_deducción', ['Fijo', 'Porcentaje']); // Tipo de deducción adicional (Fijo, Porcentaje)
            $table->enum('estado', ['Activa', 'Inactiva']); // Estado de la deducción adicional (Activa, Inactiva)
            $table->date('fecha_inicio'); // Fecha de inicio de la deducción adicional
            $table->date('fecha_final')->nullable(); // Fecha de finalización de la deducción adicional (si aplica)
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
        Schema::dropIfExists('dedduciones_adic');
    }
};