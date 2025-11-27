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
        // SOLO agregar columnas si no existen - SIN foreign keys aquí
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->after('user_id');
            }
            
            if (!Schema::hasColumn('transactions', 'reward_id')) {
                $table->foreignId('reward_id')->nullable()->after('amount');
            }
        });

        // AGREGAR FOREIGN KEYS por separado
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'branch_id')) {
                $table->foreign('branch_id')
                      ->references('id')
                      ->on('branches')
                      ->onDelete('set null');
            }
            
            if (Schema::hasColumn('transactions', 'reward_id')) {
                $table->foreign('reward_id')
                      ->references('id')
                      ->on('rewards')
                      ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Eliminar foreign keys
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['reward_id']);
            
            // Eliminar columnas
            $table->dropColumn(['branch_id', 'reward_id']);
        });
    }
};