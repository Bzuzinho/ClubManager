<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Criar permissões por módulo (usar guard 'api' para API)
        $modules = [
            'membros' => ['view', 'create', 'update', 'delete', 'manage_documents'],
            'desportivo' => ['view', 'create', 'update', 'delete'],
            'eventos' => ['view', 'create', 'update', 'delete'],
            'treinos' => ['view', 'create', 'update', 'delete'],
            'financeiro' => ['view', 'create', 'update', 'delete', 'approve'],
            'inventario' => ['view', 'create', 'update', 'delete'],
            'comunicacao' => ['view', 'create', 'update', 'delete', 'send'],
            'configuracao' => ['view', 'update'],
            'dashboard' => ['view'],
        ];

        $permissions = [];
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $permission = Permission::create([
                    'name' => "{$module}.{$action}",
                    'guard_name' => 'api'
                ]);
                $permissions["{$module}.{$action}"] = $permission;
            }
        }

        // Criar roles com guard 'api'
        $admin = Role::create(['name' => 'admin', 'guard_name' => 'api']);
        $admin->givePermissionTo(Permission::all());

        $secretaria = Role::create(['name' => 'secretaria', 'guard_name' => 'api']);
        $secretaria->givePermissionTo([
            $permissions['membros.view'],
            $permissions['membros.create'],
            $permissions['membros.update'],
            $permissions['desportivo.view'],
            $permissions['eventos.view'],
            $permissions['eventos.create'],
            $permissions['treinos.view'],
            $permissions['dashboard.view'],
        ]);

        $treinador = Role::create(['name' => 'treinador', 'guard_name' => 'api']);
        $treinador->givePermissionTo([
            $permissions['membros.view'],
            $permissions['desportivo.view'],
            $permissions['desportivo.create'],
            $permissions['desportivo.update'],
            $permissions['treinos.view'],
            $permissions['treinos.create'],
            $permissions['treinos.update'],
            $permissions['eventos.view'],
            $permissions['dashboard.view'],
        ]);

        $financeiro = Role::create(['name' => 'financeiro', 'guard_name' => 'api']);
        $financeiro->givePermissionTo([
            $permissions['membros.view'],
            $permissions['financeiro.view'],
            $permissions['financeiro.create'],
            $permissions['financeiro.update'],
            $permissions['financeiro.approve'],
            $permissions['dashboard.view'],
        ]);

        $inventario = Role::create(['name' => 'inventario', 'guard_name' => 'api']);
        $inventario->givePermissionTo([
            $permissions['inventario.view'],
            $permissions['inventario.create'],
            $permissions['inventario.update'],
            $permissions['inventario.delete'],
            $permissions['dashboard.view'],
        ]);

        $marketing = Role::create(['name' => 'marketing', 'guard_name' => 'api']);
        $marketing->givePermissionTo([
            $permissions['membros.view'],
            $permissions['comunicacao.view'],
            $permissions['comunicacao.create'],
            $permissions['comunicacao.update'],
            $permissions['comunicacao.send'],
            $permissions['dashboard.view'],
        ]);
    }
}
