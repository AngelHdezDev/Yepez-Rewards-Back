<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Agrega la columna branch_id como llave foránea (nullable)
            $table->foreignId('branch_id')
                ->nullable() // Los clientes y el super-admin no tienen sucursal
                ->after('password') // Colócala después de 'password'
                ->constrained() // Crea la llave foránea a la tabla 'branches'
                ->onDelete('set null'); // Si la sucursal se elimina, este campo se pone en NULL
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Verificar si la columna existe antes de intentar eliminar
            if (Schema::hasColumn('users', 'branch_id')) {
                // Eliminar la clave foránea primero si existe
                $table->dropForeign(['branch_id']);
                // Eliminar la columna
                $table->dropColumn('branch_id');
            }
        });
    }
};