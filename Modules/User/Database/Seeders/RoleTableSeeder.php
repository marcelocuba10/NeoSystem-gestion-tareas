<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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
            'idReference' => $this->generateUniqueCodeRole()
        ]);

        //Assign default permissions
        $role->givePermissionTo('user-list');
        $role->givePermissionTo('customer-list');
        $role->givePermissionTo('customer-create');
        $role->givePermissionTo('customer-edit');

        $role->givePermissionTo('visit_customer-list');
        $role->givePermissionTo('visit_customer-create');
        $role->givePermissionTo('visit_customer-edit');

        $role->givePermissionTo('schedule-list');
        $role->givePermissionTo('schedule-create');
        $role->givePermissionTo('schedule-edit');

        $role->givePermissionTo('sales-list');
        $role->givePermissionTo('sales-create');

        $role->givePermissionTo('products-list');

        $role->givePermissionTo('what_can_do-list');
        $role->givePermissionTo('what_can_do-create');
        $role->givePermissionTo('what_can_do-edit');

        $role->givePermissionTo('role-list');
        $role->givePermissionTo('role-create');
        $role->givePermissionTo('role-edit');
        $role->givePermissionTo('report-list');
    }

    public function generateUniqueCodeRole()
    {
        do {
            $idReference = random_int(100000, 999999);
        } while (
            DB::table('roles')->where("idReference", "=", $idReference)->first()
        );

        return $idReference;
    }
}
