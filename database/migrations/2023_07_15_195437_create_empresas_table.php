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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            // Agrega más columnas según tus necesidades
            $table->string('logo')->nullable();
            $table->string('razon_social');
            $table->string('nombre_comercial')->unique();
            $table->string('direccion');
            $table->string('correo_electronico');
            $table->string('rnc_cedula')->unique();
            $table->string('telefono')->unique();
            $table->string('provincia');
            $table->string('municipio');
            $table->string('sitio_web');
            $table->enum('regimen', ['Régimen general', 'Régimen simplificado de tributación (RST)', 'Regímenes especiales de tributación'])->nullable();
            $table->string('sector');
            $table->enum('numero_empleados', ['1-10', '11-50', '51-100', '101-500', '500+']);
            $table->string('moneda');
            $table->enum('separador_decimal', [',', '.']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
