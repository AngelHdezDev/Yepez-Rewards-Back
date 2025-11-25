<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{

    public function run(): void
    {

        DB::table('branches')->insert([

            [
                'name' => 'Sucursal Américas',
                'city' => 'Guadalajara',
                'address' => 'Av. De las Américas #160 Col. Ladrón de Guevara, Guadalajara, Jalisco C.P. 44600',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Sucursal 8 de julio',
                'city' => 'Guadalajara',
                'address' => 'Av. 8 de Julio #1537 Col. Morelos, Guadalajara, Jalisco C.P. 44910',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Sucursal Ávila Camacho',
                'city' => 'Guadalajara',
                'address' => 'Av. Ávila Camacho #1891 Col. Lomas del Country, Guadalajara, Jalisco C.P. 44260',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Sucursal Belisario',
                'city' => 'Guadalajara',
                'address' => 'Belisario Domínguez #1540 Col. Belisario Domínguez, Guadalajara, Jalisco C.P. 44330',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Sucursal Cruz del Sur',
                'city' => 'Guadalajara',
                'address' => 'Av. Cruz del Sur #3377 Col. Jardines del sur, Guadalajara, Jalisco C.P. 44950',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Sucursal Ejército',
                'city' => 'Guadalajara',
                'address' => 'Calz. del ejército #1301 Col. Quinta Velarde, Guadalajara, Jalisco C.P. 44430',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Sucursal Minerva',
                'city' => 'Guadalajara',
                'address' => 'Av. Circunvalación Agustín Yáñez #2875, Col. Arcos Vallarta, Guadalajara, Jalisco C.P. 44130',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Sucursal Revolución',
                'city' => 'Guadalajara',
                'address' => 'Calz. Revolución #1321 Col. Oro, Guadalajara, Jalisco C.P. 44400',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Sucursal Río Nilo',
                'city' => 'Guadalajara',
                'address' => 'Av Río Nilo 3983, Jardines de Los Historiadores, 44860 Guadalajara, Jal.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Sucursal Mesa del Norte',
                'city' => 'Guadalajara',
                'address' => 'Calle. Sierra nevada #2309 Col. Belisario Domínguez, Guadalajara, Jalisco C.P. 44320',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Sucursal Plutarco',
                'city' => 'Guadalajara',
                'address' => 'Av. Plutarco Elías Calles #728 Col. Libertad, Guadalajara, Jalisco C.P. 44750',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Sucursal Javier Mina',
                'city' => 'Guadalajara',
                'address' => 'Av. Francisco Javier Mina 1093, La Penal, 44380 Guadalajara, Jal.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Sucursal Legazpi',
                'city' => 'Guadalajara',
                'address' => 'Av. Miguel López de Legaspi 1594, Col. Industrial, 44930 Guadalajara, Jal.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Sucursal Zona Industrial',
                'city' => 'Guadalajara',
                'address' => 'Av. Miguel López de Legaspi 581, Echeverría, 44970 Guadalajara, Jal.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Sucursal Oblatos',
                'city' => 'Guadalajara',
                'address' => 'Hacienda de oblatos 2157. Col. Tetlán Rio Verde. Guadalajara, Jalisco',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Sucursal Calzada del águila',
                'city' => 'Guadalajara',
                'address' => 'Av. Alemania 1107-A, Moderna, 44190 Guadalajara, Jal.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Sucursal Los Charros',
                'city' => 'Zapopan',
                'address' => 'Blvd de los charros #1600 Col. El Vigía, Zapopan, Jalisco',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Sucursal Zapopan Norte',
                'city' => 'Zapopan',
                'address' => 'Av. Industria Textil #2176-D Col. Zapopan Industrial Norte C.P. 45130',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Sucursal La Tuzania',
                'city' => 'Zapopan',
                'address' => 'Av. De Los Cerezos 936-A, La Tuzania, 45138 Zapopan, Jal.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Sucursal San Isidro',
                'city' => 'Zapopan',
                'address' => 'Camino a las cañadas 900 6D. Av. Valle San Isidro',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Sucursal Capital Norte',
                'city' => 'Zapopan',
                'address' => 'Av. Guadalajara #3743 local 1 Col. Hogares de Nuevo México',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],  

            [
                'name' => 'Sucursal Copernico',
                'city' => 'Zapopan',
                'address' => 'Volcán Paricutín #6035 Esq. Copernico Col. El Colli Urbano',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ], 

            [
                'name' => 'Sucursal Chapalita',
                'city' => 'Zapopan',
                'address' => 'Av. Guadalupe #1638 Col. Chapalita Oriente, Zapopan, Jalisco C.P. 45040',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Sucursal Rio Verde',
                'city' => 'Zapopan',
                'address' => 'Av. Perif. Pte. Manuel Gómez Morin 3100-Local 11, Miramar, 45060 Zapopan, Jal.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Sucursal Tlajomulco',
                'city' => 'Tlajomulco',
                'address' => 'Av. Adolf Bernard Horn Junior #1580, 45640 Col. los Sauces, Jal.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Sucursal El Salto',
                'city' => 'El Salto',
                'address' => 'Carretera a El Salto número 3-B San José del Castillo. CP. 45685',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Sucursal San Agustín',
                'city' => 'San Agustín',
                'address' => 'Av. Adolfo Lopez Mateos Sur #504. Col. San Agustin, C.P 45640',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Sucursal Tlaquepaque',
                'city' => 'Tlaquepaque',
                'address' => 'Centro de Servicio LTH 8 de julio y Periférico (PERISUR)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}