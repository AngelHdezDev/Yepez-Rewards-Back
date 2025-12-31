<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración para añadir el campo de imagen.
     */
    public function up(): void
    {
        Schema::table('rewards', function (Blueprint $table) {
            // Usamos nullable() para que los registros existentes no den error
            // Lo ideal es guardarlo después del nombre o la descripción
            $table->string('image_url')->nullable()->after('description');
        });
    }

    /**
     * Revierte los cambios eliminando la columna.
     */
    public function down(): void
    {
        Schema::table('rewards', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });
    }
};