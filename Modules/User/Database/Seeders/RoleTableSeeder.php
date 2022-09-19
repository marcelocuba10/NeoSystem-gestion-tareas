<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

//spatie
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Model::unguard();

        //Create role
        $role = Role::create([
            'name' => 'Funcionario',
            'guard_name' => 'web',
            'system_role' => '1',
            'idReference' => 000001
        ]);

        //Assign default permissions
        $role->givePermissionTo('user-list');
        $role->givePermissionTo('customer-list');
        $role->givePermissionTo('customer-create');
        $role->givePermissionTo('customer-edit');
        $role->givePermissionTo('role-list');
        $role->givePermissionTo('role-create');
        $role->givePermissionTo('role-edit');
        $role->givePermissionTo('report-list');
    }
}
