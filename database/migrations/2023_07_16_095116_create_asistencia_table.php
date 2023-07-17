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
        Schema::create('asistencia', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empleado_id');
            $table->date('fecha');
            $table->string('dias'); // Agregar campo "dias"
            $table->time('hora_entrada');
            $table->time('hora_salida')->nullable();
            $table->time('hora_descanso_inicio');
            $table->time('hora_descanso_fin');
            $table->time('hora_extra')->nullable();
            $table->enum('estado', ['Presente', 'Ausente', 'Tarde']); // Agregar campo "estado" como enum
            $table->timestamps();
            $table->foreign('empleado_id')->references('id')->on('empleados')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencia');
    }
};
