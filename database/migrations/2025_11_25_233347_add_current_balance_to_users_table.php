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
        Schema::table('users', function (Blueprint $table) {
            // Añade la columna 'current_balance' (saldo actual de puntos)
            // Se usa 'unsignedBigInteger' y se permite 'nullable' para que no falle al inicio.
            // Establecemos 0 como valor por defecto, lo que es común para saldos de puntos.
            $table->unsignedBigInteger('current_balance')->default(0)->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revertir la migración (eliminar la columna)
            $table->dropColumn('current_balance');
        });
    }
};