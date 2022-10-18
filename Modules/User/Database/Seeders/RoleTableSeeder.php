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
            'name' => 'Agente',
            'guard_name' => 'web',
            'system_role' => '1',
            'idReference' => $this->generateUniqueCodeRole()
        ]);

        //Assign default permissions
        $role->givePermissionTo('user-list');

        $role->givePermissionTo('customer-list');
        $role->givePermissionTo('customer-create');
        $role->givePermissionTo('customer-edit');

        $role->givePermissionTo('customer_visit-list');
        $role->givePermissionTo('customer_visit-create');
        $role->givePermissionTo('customer_visit-edit');

        $role->givePermissionTo('report-list');
        $role->givePermissionTo('report-create');
        $role->givePermissionTo('report-edit');

        $role->givePermissionTo('multimedia-list');

        $role->givePermissionTo('sales-list');
        $role->givePermissionTo('sales-create');
        $role->givePermissionTo('sales-edit');

        $role->givePermissionTo('appointment-list');
        $role->givePermissionTo('appointment-create');
        $role->givePermissionTo('appointment-edit');

        $role->givePermissionTo('products-list');

        $role->givePermissionTo('what_can_do-list');
        $role->givePermissionTo('what_can_do-create');
        $role->givePermissionTo('what_can_do-edit');

        $role->givePermissionTo('role-list');
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
