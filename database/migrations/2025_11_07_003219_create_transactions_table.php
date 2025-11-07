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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            // Clave foránea que enlaza la transacción al usuario
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Tipo de transacción: CREDIT o DEBIT
            $table->enum('type', ['CREDIT', 'DEBIT']);
            
            // Monto de la transacción (puntos). Usamos decimal para precisión.
            $table->integer('amount'); 
            
            // Descripción de la transacción
            $table->string('description', 255);

            // Estado para el procesamiento asíncrono (útil para jobs)
            $table->enum('status', ['PENDING', 'PROCESSING', 'COMPLETED', 'FAILED'])->default('PENDING');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};