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
        Schema::create('redemptions', function (Blueprint $table) {
            $table->id();
            
            // Llaves foráneas
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('Usuario que realizó el canje.');
            $table->foreignId('reward_id')->constrained('rewards')->onDelete('cascade')->comment('El premio que fue canjeado.');
            
            // Detalles de la transacción (para congelar el valor y el nombre en el momento del canje)
            $table->string('reward_name')->comment('Nombre del premio en el momento del canje.');
            $table->integer('points_cost')->unsigned()->comment('Puntos descontados en el momento del canje.');
            $table->string('redemption_code')->unique()->comment('Código único para verificar la validez del canje, si aplica.');
            
            // Estado de canje
            $table->string('status')->default('pending')->comment('Estado del canje: pending, completed, cancelled.');

            $table->timestamps();
            
            // Índice compuesto para facilitar consultas frecuentes.
            $table->index(['user_id', 'reward_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redemptions');
    }
};