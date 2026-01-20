<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Branch; // Importar el modelo Branch
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ------------------------------------------------
        // 1. OBTENCIÓN DE IDs DE SUCURSALES (DEBEN EXISTIR PREVIAMENTE)
        // ------------------------------------------------
        $branchAmericasId = Branch::where('name', 'Sucursal Américas')->value('id');
        $branch8JulioId = Branch::where('name', 'Sucursal 8 de julio')->value('id');
        $branchAvilaCamachoId = Branch::where('name', 'Sucursal Avila Camacho')->value('id');
        $branchBelisarioId = Branch::where('name', 'Sucursal Belisario')->value('id');
        $branchCruzDelSurId = Branch::where('name', 'Sucursal Cruz del Sur')->value('id');
        $branchEjercitoId = Branch::where('name', 'Sucursal Ejército')->value('id');
        $branchMinervaId = Branch::where('name', 'Sucursal Minerva')->value('id');
        $branchRevolucionId = Branch::where('name', 'Sucursal Revolución')->value('id');
        $branchRioNiloId = Branch::where('name', 'Sucursal Río Nilo')->value('id');
        $branchMesadelNorteId = Branch::where('name', 'Sucursal Mesa del Norte')->value('id');
        $branchPlutarcobId = Branch::where('name', 'Sucursal Plutarco')->value('id');
        $branchJavierMinaId = Branch::where('name', 'Sucursal Javier Mina')->value('id');
        $branchLegazpiId = Branch::where('name', 'Sucursal Legazpi')->value('id');
        $branchZonaIndustrialId = Branch::where('name', 'Sucursal Zona Industrial')->value('id');
        $branchOblatosId = Branch::where('name', 'Sucursal Oblatos')->value('id');
        $branchCalzadadelAguilaId = Branch::where('name', 'Sucursal Calzada del águila')->value('id');
        $branchCharrosId = Branch::where('name', 'Sucursal Los Charros')->value('id');
        $branchZapopanNorteId = Branch::where('name', 'Sucursal Zapopan Norte')->value('id');
        $branchTuzaniaId = Branch::where('name', 'Sucursal La Tuzanía')->value('id');
        $branchSanIsidroId = Branch::where('name', 'Sucursal San Isidro')->value('id');
        $branchCapitalNorteId = Branch::where('name', 'Sucursal Capital Norte')->value('id');
        $branchCopernicoId = Branch::where('name', 'Sucursal Copernico')->value('id');
        $branchChapalitaId = Branch::where('name', 'Sucursal Chapalita')->value('id');
        $branchRioVerdeId = Branch::where('name', 'Sucursal Rio Verde')->value('id');
        $branchTlajomulcoId = Branch::where('name', 'Sucursal Tlajomulco')->value('id');
        $branchElSaltoId = Branch::where('name', 'Sucursal El Salto')->value('id');
        $branchSanAgustinId = Branch::where('name', 'Sucursal San Agustín')->value('id');
        $branchTlaquepaqueId = Branch::where('name', 'Sucursal Tlaquepaque')->value('id');
 
        // ------------------------------------------------
        // 2. CREACIÓN DE ROLES Y PERMISOS
        // ------------------------------------------------
        $roleYepez = Role::firstOrCreate(['name' => 'yepez']);      // Yepez Central (Administración total)
        $roleSucursal = Role::firstOrCreate(['name' => 'sucursal']); // Sucursales (Gestión de puntos y canjes)
        $roleCliente = Role::firstOrCreate(['name' => 'cliente']);   // Clientes finales (acceso a su perfil)

        // 2. Crear Permisos
        Permission::firstOrCreate(['name' => 'manage rewards']);
        Permission::firstOrCreate(['name' => 'view dashboard']);
        Permission::firstOrCreate(['name' => 'redeem reward']);
        Permission::firstOrCreate(['name' => 'check points']);

        // 3. Asignar Permisos a Roles
        $roleYepez->givePermissionTo(['manage rewards', 'view dashboard', 'redeem reward', 'check points']);
        $roleSucursal->givePermissionTo(['redeem reward', 'check points']);


        // ------------------------------------------------
        // 3. USUARIO YEPEZ CENTRAL
        // ------------------------------------------------
        $yepezUser = User::firstOrCreate(
            ['email' => 'yepez@gmail.com'],
            [
                'name' => 'Yepez Central Admin',
                'password' => Hash::make('password'),
                'branch_id' => null,
            ]
        );
        if (!$yepezUser->hasRole('yepez')) {
            $yepezUser->assignRole('yepez');
        }


        $yepezUser = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('passwordAngel'),
                'branch_id' => null,
            ]
        );
        if (!$yepezUser->hasRole('yepez')) {
            $yepezUser->assignRole('yepez');
        }


        // ------------------------------------------------
        // 4. CUENTAS DE SUCURSAL (27 cuentas individuales)
        // ------------------------------------------------
        
        // Sucursal Américas
        $userAmericas = User::firstOrCreate(
            ['email' => 'sucursalamericas@gmail.com'],
            [
                'name' => 'Sucursal Américas',
                'password' => Hash::make('password'),
                'branch_id' => $branchAmericasId,
            ]
        );
        if (!$userAmericas->hasRole('sucursal')) {
            $userAmericas->assignRole('sucursal');
        }

        // Sucursal 8 de julio
        $user8Julio = User::firstOrCreate(
            ['email' => 'sucursal8dejulio@gmail.com'],
            [
                'name' => 'Sucursal 8 de julio',
                'password' => Hash::make('password'),
                'branch_id' => $branch8JulioId,
            ]
        );
        if (!$user8Julio->hasRole('sucursal')) {
            $user8Julio->assignRole('sucursal');
        }

        // Sucursal Avila Camacho
        $userAvilaCamacho = User::firstOrCreate(
            ['email' => 'sucursalavilacamacho@gmail.com'],
            [
                'name' => 'Sucursal Avila Camacho',
                'password' => Hash::make('password'),
                'branch_id' => $branchAvilaCamachoId,
            ]
        );
        if (!$userAvilaCamacho->hasRole('sucursal')) {
            $userAvilaCamacho->assignRole('sucursal');
        }

        // Sucursal Belisario
        $userBelisario = User::firstOrCreate(
            ['email' => 'sucursalbelisario@gmail.com'],
            [
                'name' => 'Sucursal Belisario',
                'password' => Hash::make('password'),
                'branch_id' => $branchBelisarioId,
            ]
        );
        if (!$userBelisario->hasRole('sucursal')) {
            $userBelisario->assignRole('sucursal');
        }
        
        // Sucursal Cruz del Sur
        $userCruzDelSur = User::firstOrCreate(
            ['email' => 'sucursalcruzdsur@gmail.com'],
            [
                'name' => 'Sucursal Cruz del Sur',
                'password' => Hash::make('password'),
                'branch_id' => $branchCruzDelSurId,
            ]
        );
        if (!$userCruzDelSur->hasRole('sucursal')) {
            $userCruzDelSur->assignRole('sucursal');
        }

        // Sucursal Ejército
        $userEjercito = User::firstOrCreate(
            ['email' => 'sucursalejercito@gmail.com'],
            [
                'name' => 'Sucursal Ejército',
                'password' => Hash::make('password'),
                'branch_id' => $branchEjercitoId,
            ]
        );
        if (!$userEjercito->hasRole('sucursal')) {
            $userEjercito->assignRole('sucursal');
        }

        // Sucursal Minerva
        $userMinerva = User::firstOrCreate(
            ['email' => 'sucursalminerva@gmail.com'],
            [
                'name' => 'Sucursal Minerva',
                'password' => Hash::make('password'),
                'branch_id' => $branchMinervaId,
            ]
        );
        if (!$userMinerva->hasRole('sucursal')) {
            $userMinerva->assignRole('sucursal');
        }


        // Sucursal Revolución
        $userRevolucion = User::firstOrCreate(
            ['email' => 'sucursalrevolucion@gmail.com'],
            [
                'name' => 'Sucursal Revolución',
                'password' => Hash::make('password'),
                'branch_id' => $branchRevolucionId,
            ]
        );
        if (!$userRevolucion->hasRole('sucursal')) {
            $userRevolucion->assignRole('sucursal');
        }

        // Sucursal Río Nilo
        $userRioNilo = User::firstOrCreate(
            ['email' => 'sucursalrionilo@gmail.com'],
            [
                'name' => 'Sucursal Río Nilo',
                'password' => Hash::make('password'),
                'branch_id' => $branchRioNiloId,
            ]
        );
        if (!$userRioNilo->hasRole('sucursal')) {
            $userRioNilo->assignRole('sucursal');
        }

        // Sucursal Mesa del Norte
        $userMesaDelNorte = User::firstOrCreate(
            ['email' => 'sucursalmesadnorte@gmail.com'],
            [
                'name' => 'Sucursal Mesa del Norte',
                'password' => Hash::make('password'),
                'branch_id' => $branchMesadelNorteId,
            ]
        );
        if (!$userMesaDelNorte->hasRole('sucursal')) {
            $userMesaDelNorte->assignRole('sucursal');
        }

        // Sucursal Plutarco
        $userPlutarco = User::firstOrCreate(
            ['email' => 'sucursalplutarco@gmail.com'],
            [
                'name' => 'Sucursal Plutarco',
                'password' => Hash::make('password'),
                'branch_id' => $branchPlutarcobId,
            ]
        );
        if (!$userPlutarco->hasRole('sucursal')) {
            $userPlutarco->assignRole('sucursal');
        }

        // Sucursal Javier Mina
        $userJavierMina = User::firstOrCreate(
            ['email' => 'sucursaljaviermina@gmail.com'],
            [
                'name' => 'Sucursal Javier Mina',
                'password' => Hash::make('password'),
                'branch_id' => $branchJavierMinaId,
            ]
        );
        if (!$userJavierMina->hasRole('sucursal')) {
            $userJavierMina->assignRole('sucursal');
        }

        // Sucursal Legazpi
        $userLegazpi = User::firstOrCreate(
            ['email' => 'sucursallegazpi@gmail.com'],
            [
                'name' => 'Sucursal Legazpi',
                'password' => Hash::make('password'),
                'branch_id' => $branchLegazpiId,
            ]
        );
        if (!$userLegazpi->hasRole('sucursal')) {
            $userLegazpi->assignRole('sucursal');
        }

        // Sucursal Zona Industrial
        $userZonaIndustrial = User::firstOrCreate(
            ['email' => 'sucursalzonaindustrial@gmail.com'],
            [
                'name' => 'Sucursal Zona Industrial',
                'password' => Hash::make('password'),
                'branch_id' => $branchZonaIndustrialId,
            ]
        );
        if (!$userZonaIndustrial->hasRole('sucursal')) {
            $userZonaIndustrial->assignRole('sucursal');
        }

        // Sucursal Oblatos
        $userOblatos = User::firstOrCreate(
            ['email' => 'sucursaloblatos@gmail.com'],
            [
                'name' => 'Sucursal Oblatos',
                'password' => Hash::make('password'),
                'branch_id' => $branchOblatosId,
            ]
        );
        if (!$userOblatos->hasRole('sucursal')) {
            $userOblatos->assignRole('sucursal');
        }

        // Sucursal Calzada del Águila
        $userCalzadaAguila = User::firstOrCreate(
            ['email' => 'sucursalcalzadaaguila@gmail.com'],
            [
                'name' => 'Sucursal Calzada del águila',
                'password' => Hash::make('password'),
                'branch_id' => $branchCalzadadelAguilaId,
            ]
        );
        if (!$userCalzadaAguila->hasRole('sucursal')) {
            $userCalzadaAguila->assignRole('sucursal');
        }

        // Sucursal Los Charros
        $userCharros = User::firstOrCreate(
            ['email' => 'sucursalloscharros@gmail.com'],
            [
                'name' => 'Sucursal Los Charros',
                'password' => Hash::make('password'),
                'branch_id' => $branchCharrosId,
            ]
        );
        if (!$userCharros->hasRole('sucursal')) {
            $userCharros->assignRole('sucursal');
        }

        // Sucursal Zapopan Norte
        $userZapopanNorte = User::firstOrCreate(
            ['email' => 'sucursalzapopannorte@gmail.com'],
            [
                'name' => 'Sucursal Zapopan Norte',
                'password' => Hash::make('password'),
                'branch_id' => $branchZapopanNorteId,
            ]
        );
        if (!$userZapopanNorte->hasRole('sucursal')) {
            $userZapopanNorte->assignRole('sucursal');
        }

        // Sucursal La Tuzanía
        $userTuzania = User::firstOrCreate(
            ['email' => 'sucursaltusania@gmail.com'],
            [
                'name' => 'Sucursal La Tuzanía',
                'password' => Hash::make('password'),
                'branch_id' => $branchTuzaniaId,
            ]
        );
        if (!$userTuzania->hasRole('sucursal')) {
            $userTuzania->assignRole('sucursal');
        }

        // Sucursal San Isidro
        $userSanIsidro = User::firstOrCreate(
            ['email' => 'sucursalsanisidro@gmail.com'],
            [
                'name' => 'Sucursal San Isidro',
                'password' => Hash::make('password'),
                'branch_id' => $branchSanIsidroId,
            ]
        );
        if (!$userSanIsidro->hasRole('sucursal')) {
            $userSanIsidro->assignRole('sucursal');
        }

        // Sucursal Capital Norte
        $userCapitalNorte = User::firstOrCreate(
            ['email' => 'sucursalcapitalnorte@gmail.com'],
            [
                'name' => 'Sucursal Capital Norte',
                'password' => Hash::make('password'),
                'branch_id' => $branchCapitalNorteId,
            ]
        );
        if (!$userCapitalNorte->hasRole('sucursal')) {
            $userCapitalNorte->assignRole('sucursal');
        }

        // Sucursal Copérnico
        $userCopernico = User::firstOrCreate(
            ['email' => 'sucursalcopernico@gmail.com'],
            [
                'name' => 'Sucursal Copérnico',
                'password' => Hash::make('password'),
                'branch_id' => $branchCopernicoId,
            ]
        );
        if (!$userCopernico->hasRole('sucursal')) {
            $userCopernico->assignRole('sucursal');
        }

        // Sucursal Chapalita
        $userChapalita = User::firstOrCreate(
            ['email' => 'sucursalchapalita@gmail.com'],
            [
                'name' => 'Sucursal Chapalita',
                'password' => Hash::make('password'),
                'branch_id' => $branchChapalitaId,
            ]
        );
        if (!$userChapalita->hasRole('sucursal')) {
            $userChapalita->assignRole('sucursal');
        }

        // Sucursal Río Verde
        $userRioVerde = User::firstOrCreate(
            ['email' => 'sucursalrioverde@gmail.com'],
            [
                'name' => 'Sucursal Río Verde',
                'password' => Hash::make('password'),
                'branch_id' => $branchRioVerdeId,
            ]
        );
        if (!$userRioVerde->hasRole('sucursal')) {
            $userRioVerde->assignRole('sucursal');
        }

        // Sucursal Tlajomulco
        $userTlajomulco = User::firstOrCreate(
            ['email' => 'sucursaltlajomulco@gmail.com'],
            [
                'name' => 'Sucursal Tlajomulco',
                'password' => Hash::make('password'),
                'branch_id' => $branchTlajomulcoId,
            ]
        );
        if (!$userTlajomulco->hasRole('sucursal')) {
            $userTlajomulco->assignRole('sucursal');
        }

        // Sucursal El Salto
        $userElSalto = User::firstOrCreate(
            ['email' => 'sucursalsalto@gmail.com'],
            [
                'name' => 'Sucursal El Salto',
                'password' => Hash::make('password'),
                'branch_id' => $branchElSaltoId,
            ]
        );
        if (!$userElSalto->hasRole('sucursal')) {
            $userElSalto->assignRole('sucursal');
        }

        // Sucursal San Agustín
        $userSanAgustin = User::firstOrCreate(
            ['email' => 'sucursalsanagustin@gmail.com'],
            [
                'name' => 'Sucursal San Agustín',
                'password' => Hash::make('password'),
                'branch_id' => $branchSanAgustinId,
            ]
        );
        if (!$userSanAgustin->hasRole('sucursal')) {
            $userSanAgustin->assignRole('sucursal');
        }

        // Sucursal Tlaquepaque
        $userTlaquepaque = User::firstOrCreate(
            ['email' => 'sucursaltlaquepaque@gmail.com'],
            [
                'name' => 'Sucursal Tlaquepaque',
                'password' => Hash::make('password'),
                'branch_id' => $branchTlaquepaqueId,
            ]
        );
        if (!$userTlaquepaque->hasRole('sucursal')) {
            $userTlaquepaque->assignRole('sucursal');
        }


        // ------------------------------------------------
        // 5. USUARIO CLIENTE FINAL
        // ------------------------------------------------
        $clientUser = User::firstOrCreate(
            ['email' => 'cliente@gmail.com'],
            [
                'name' => 'Cliente Final',
                'password' => Hash::make('password'),
                'branch_id' => null, // Los clientes no están asociados a una sucursal
            ]
        );
        if (!$clientUser->hasRole('cliente')) {
            $clientUser->assignRole('cliente');
        }
    }
}