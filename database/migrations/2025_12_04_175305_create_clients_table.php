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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            
            // Datos de contacto y autenticación del cliente
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password'); // Para que el cliente pueda iniciar sesión
            $table->string('phone')->nullable();

            // Llave foránea que lo relaciona con la sucursal que lo enroló
            // IMPORTANTE: Esto asegura que el cliente 'pertenece' solo a esa sucursal.
            $table->foreignId('branch_id')->constrained('users')->onDelete('restrict')->comment('ID del usuario Sucursal o Yepez que registró a este cliente.');

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};