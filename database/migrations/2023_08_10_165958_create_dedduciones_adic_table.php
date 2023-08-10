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
            $table->string('descripcion');
            $table->decimal('monto', 10, 2);
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
