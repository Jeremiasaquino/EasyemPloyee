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
        Schema::create('historial_empresa_anterior', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empleado_id');
            // Agrega las columnas específicas para historial_empresa_anterior
            $table->string('nombre_empresa_anterior')->nullable();
            $table->string('cargo_anterior')->nullable();
            $table->date('fecha_inicio_trabajo_anterior')->nullable();
            $table->date('fecha_salida_trabajo_anterior')->nullable();
            $table->string('motivo_salida')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('empleado_id')->references('id')->on('empleados')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_empresa_anterior');
    }
};