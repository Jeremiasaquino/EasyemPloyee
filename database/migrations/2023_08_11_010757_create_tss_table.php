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
        Schema::create('tss', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Nombre de la tss adicional (ejemplo: "AFP")
            $table->decimal('porcentaje', 10, 2); // Porcentaje
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tss');
    }
};
