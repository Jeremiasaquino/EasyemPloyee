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
        Schema::create('nomina', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_nomina'); // Fecha de la nómina
            $table->unsignedBigInteger('empleado_id');
            $table->decimal('salario', 10, 2); // Salario base del empleado
            $table->decimal('hora_extra', 5, 2)->default(0); // Horas extras
            $table->decimal('total_beneficios', 10, 2); // Total de beneficios
            $table->decimal('total_deducciones', 10, 2); // Total de deducciones
            $table->decimal('total_prestamos_adelanto', 10, 2); // Total de préstamos por adelantado
            $table->decimal('salario_neto', 10, 2); // Salario neto
            $table->string('metodo_pago'); // Forma de pago (cheque, transferencia, etc.)
            $table->string('cuenta_bancaria')->nullable(); // Cuenta bancaria
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
        Schema::dropIfExists('nomina');
    }
};