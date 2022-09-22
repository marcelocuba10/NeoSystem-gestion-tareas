<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Entities\Permission;

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
            'role-sa-list',
            'role-sa-create',
            'role-sa-edit',
            'role-sa-delete',

            'permission-sa-list',
            'permission-sa-create',
            'permission-sa-edit',
            'permission-sa-delete',

            'seller-sa-list',
            'seller-sa-create',
            'seller-sa-edit',
            'seller-sa-delete',

            'super_user-sa-list',
            'super_user-sa-create',
            'super_user-sa-edit',
            'super_user-sa-delete',

            'financial-sa-list',
            'financial-sa-create',
            'financial-sa-edit',
            'financial-sa-delete',

            'report-sa-list',
            'report-sa-create',
            'report-sa-edit',
            'report-sa-delete',

            'customer-sa-list',
            'customer-sa-create',
            'customer-sa-edit',
            'customer-sa-delete',

            'product-sa-list',
            'product-sa-create',
            'product-sa-edit',
            'product-sa-delete',
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }
    }
}
