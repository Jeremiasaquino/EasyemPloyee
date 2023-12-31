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
        Schema::create('prestamos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empleado_id');
            $table->decimal('monto', 10, 2); // Monto del préstamo por adelantado
            $table->date('fecha_prestamo'); // Fecha del préstamo
            $table->enum('estado', ['Activo', 'Pagado']); // Estado del préstamo (Activo, Pagado)
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
        Schema::dropIfExists('prestamos');
    }
};