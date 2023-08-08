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
            $table->string('dia_semana');
            $table->date('fecha');
            $table->time('hora_entrada');
            $table->time('hora_salida')->nullable();
            $table->time('hora_descanso_inicio')->nullable();
            $table->time('hora_descanso_fin')->nullable();
            $table->string('hora_extra')->nullable();
            $table->string('horas_trabajas')->nullable();
            $table->enum('estado', ['Presente', 'Ausente', 'Tarde']); // Agregar campo "estado" como enum
            $table->enum('descripcion', ['Entrada', 'Descanso', 'Fin Descanso', 'Salida'])->nullable(); // Agregar campo "estado" como enum
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