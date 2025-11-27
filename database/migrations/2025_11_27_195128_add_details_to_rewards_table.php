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
        Schema::table('rewards', function (Blueprint $table) {
            // Información del premio
            $table->string('name')->comment('Nombre del producto o servicio canjeable.')->after('id');
            $table->text('description')->nullable()->comment('Descripción detallada del premio y sus condiciones.')->after('name');
            $table->integer('cost_points')->unsigned()->comment('Costo del premio en puntos de lealtad.')->after('description');
            $table->string('code')->unique()->comment('Código interno o código de cupón asociado al premio.')->after('cost_points');
            
            // Control de disponibilidad
            $table->integer('stock')->default(0)->comment('Cantidad disponible para canje (0 si es ilimitado).')->after('code');
            $table->boolean('is_active')->default(true)->comment('Indica si el premio está activo y visible para el canje.')->after('stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rewards', function (Blueprint $table) {
            // Revertir los cambios (eliminar las columnas en caso de rollback)
            $table->dropColumn(['name', 'description', 'cost_points', 'code', 'stock', 'is_active']);
        });
    }
};