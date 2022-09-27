<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\Permission;

class PermissionTableSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $permissions = [
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            'permission-list',
            'permission-create',
            'permission-edit',
            'permission-delete',

            'user-list',
            'user-create',
            'user-edit',
            'user-delete',

            'report-list',
            'report-create',
            'report-edit',
            'report-delete',

            'customer-list',
            'customer-create',
            'customer-edit',
            'customer-delete',

            'customer_visit-list',
            'customer_visit-create',
            'customer_visit-edit',
            'customer_visit-delete',

            'schedule-list',
            'schedule-create',
            'schedule-edit',
            'schedule-delete',

            'sales-list',
            'sales-create',
            'sales-edit',
            'sales-delete',

            'products-list',
            'products-create',
            'products-edit',
            'products-delete',

            'what_can_do-list',
            'what_can_do-create',
            'what_can_do-edit',
            'what_can_do-delete',

            'parameter-list',
            'parameter-create',
            'parameter-edit',
            'parameter-delete',
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }
    }
}
