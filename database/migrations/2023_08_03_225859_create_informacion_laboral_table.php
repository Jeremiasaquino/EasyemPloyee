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
        Schema::create('informacion_laboral', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empleado_id');
           // Agrega las columnas específicas para informacion_larabol
            $table->date('fecha_contrato');
            $table->date('finalizacion_contrato')->nullable();
            $table->string('tipo_contrato');
            $table->string('tipo_salario');
            $table->string('salario');
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
        Schema::dropIfExists('informacion_laboral');
    }
};