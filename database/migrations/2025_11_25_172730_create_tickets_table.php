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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            
            // Datos del ticket (lo que registra la sucursal)
            $table->string('ticket_number')->unique()->comment('Número o folio único del ticket de compra.');
            $table->decimal('amount', 10, 2)->comment('Monto total de la compra.');
            $table->date('issue_date')->comment('Fecha en que se emitió el ticket.');

            // Lógica de puntos
            $table->unsignedBigInteger('points_earned')->comment('Puntos asignados al cliente por esta compra.');

            // Relaciones
            // FK: ID del cliente (rol 'client') que recibe los puntos
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // FK: ID de la sucursal (rol 'sucursal') que registró el ticket
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};