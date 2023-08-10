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
        Schema::create('beneficios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empleado_id');
            $table->string('nombre'); // Nombre del beneficio (ejemplo: "Seguro de Salud")
            $table->decimal('monto', 10, 2); // Monto del beneficio
            $table->string('tipo_beneficio'); // Tipo de beneficio
            $table->enum('estado', ['Activo', 'Inactivo']); // Estado del beneficio (Activo, Inactivo)
            $table->timestamps();

            // Definir la relación con la tabla de empleados
            $table->foreign('empleado_id')->references('id')->on('empleados')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficios');
    }
};
