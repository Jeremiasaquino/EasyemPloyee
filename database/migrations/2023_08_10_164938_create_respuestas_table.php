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
        Schema::create('respuestas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('evaluacion_id');
            $table->unsignedBigInteger('pregunta_id');
            $table->integer('puntuacion'); // Por ejemplo: de 1 a 5
            $table->text('comentario')->nullable();
            $table->timestamps();

            $table->foreign('evaluacion_id')->references('id')->on('evaluaciones')->onDelete('cascade');
            $table->foreign('pregunta_id')->references('id')->on('preguntas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respuestas');
    }
};
