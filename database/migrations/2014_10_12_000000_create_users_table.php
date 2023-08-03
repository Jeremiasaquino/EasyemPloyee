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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->nullable();
            $table->string('codigo_empleado')->unique()->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('api_token', 80)
                            ->unique()
                            ->nullable()
                            ->default(null);
            $table->rememberToken();
            $table->enum('role', ['Administrador', 'Recursos Humanos', 'Gerente', 'Empleado'])->default('Empleado');
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
            $table->string('foto')->nullable();
            $table->string('foto_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
