<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RewardsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Limpiar la tabla antes de sembrar para evitar duplicados si se corre varias veces
        DB::table('rewards')->delete();

        // Sembrar los datos de los premios
        DB::table('rewards')->insert([
            [
                'name' => 'Taza de Café Regular',
                'description' => 'Canje por una taza de café caliente de tamaño regular.',
                'cost_points' => 100,
                'code' => 'CAFE100', // Código para el POS
                'stock' => 1000,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Descuento $50 MXN',
                'description' => 'Un descuento fijo de $50 pesos en la próxima compra.',
                'cost_points' => 250,
                'code' => 'DESC50',
                'stock' => 500,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Producto Gourmet Premium',
                'description' => 'Canje por cualquier producto de la sección gourmet.',
                'cost_points' => 500,
                'code' => 'GOURMET500',
                'stock' => 200,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}