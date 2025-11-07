<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
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
        // Resetear roles y permisos cacheados, necesario después de cambios de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Crear Roles Principales
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleClient = Role::firstOrCreate(['name' => 'client']);

        // 2. Crear Permisos (Permiso clave para el Módulo 3 de gestión)
        // Este permiso controlará quién puede crear, editar o eliminar recompensas.
        Permission::firstOrCreate(['name' => 'manage rewards']);
        // Este permiso podría usarse para mostrar un dashboard de administración.
        Permission::firstOrCreate(['name' => 'view dashboard']); 

        // 3. Asignar Permisos a Roles
        // Solo el administrador puede gestionar las recompensas
        $roleAdmin->givePermissionTo(['manage rewards', 'view dashboard']);
        // El cliente solo tiene el rol básico y podrá usar las APIs públicas de canje/consulta.

        // 4. Crear un Usuario Admin de prueba
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@rewards.app'],
            [
                'name' => 'Admin de Recompensas',
                'password' => Hash::make('password'), 
            ]
        );
        // Asegurar que el rol 'admin' esté asignado
        if (!$adminUser->hasRole('admin')) {
            $adminUser->assignRole('admin');
        }

        // 5. Crear un Usuario Cliente de prueba
        $clientUser = User::firstOrCreate(
            ['email' => 'cliente@rewards.app'],
            [
                'name' => 'Cliente de Prueba',
                'password' => Hash::make('password'), 
            ]
        );
        // Asegurar que el rol 'client' esté asignado
        if (!$clientUser->hasRole('client')) {
            $clientUser->assignRole('client');
        }
    }
}