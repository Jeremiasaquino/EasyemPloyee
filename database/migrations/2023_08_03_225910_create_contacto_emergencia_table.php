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
        Schema::create('contacto_emergencia', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empleado_id');
            $table->string('nombre_contacto1')->nullable();
            $table->string('telefono_contacto1')->unique()->nullable();
            $table->string('direccion_contacto1')->nullable();
            $table->string('nombre_contacto2')->nullable();
            $table->string('telefono_contacto2')->unique()->nullable();
            $table->string('direccion_contacto2')->nullable();
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
        Schema::dropIfExists('contacto_emergencia');
    }
};