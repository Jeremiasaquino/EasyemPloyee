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
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_empleado')->unique()->default(\Illuminate\Support\Str::uuid());
            $table->string('nombre');
            $table->string('apellidos');
            $table->string('email')->unique();
            $table->string('telefono')->unique();
            $table->date('fecha_nacimiento');
            $table->string('genero');
            $table->integer('edad');
            $table->string('nacionalidad');
            $table->string('estado_civil');
            $table->string('tipo_identificacion');
            $table->string('numero_identificacion')->unique();
            $table->string('numero_seguro_social')->unique();
            $table->string('foto')->nullable();
            $table->string('foto_id')->nullable();
            $table->enum('estado', ['Activo', 'Suspendido', 'Inactivo'])->default('Activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
