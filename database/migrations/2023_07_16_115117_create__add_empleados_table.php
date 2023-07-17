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
        Schema::table('empleados', function (Blueprint $table) {
            $table->unsignedBigInteger('cargo_id');
            $table->foreign('cargo_id')->references('id')->on('cargo')->onDelete('cascade');
            
            $table->unsignedBigInteger('horario_id');
            $table->foreign('horario_id')->references('id')->on('horario')->onDelete('cascade');
            
            $table->unsignedBigInteger('departamento_id');
            $table->foreign('departamento_id')->references('id')->on('departamento')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
        $table->dropForeign('empleados_cargo_id_foreign');
        $table->dropColumn('cargo_id');
        
        $table->dropForeign('empleados_horario_id_foreign');
        $table->dropColumn('horario_id');
        
        $table->dropForeign('empleados_departamento_id_foreign');
        $table->dropColumn('departamento_id');
        });
    }
};
