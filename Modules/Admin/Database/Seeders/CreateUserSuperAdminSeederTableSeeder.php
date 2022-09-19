<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\SuperUser;

//spatie
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateUserSuperAdminSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $user = SuperUser::create([
            'name' => 'Super User', 
            'last_name' => 'superadmin', 
            'doc_id' => '1234567', 
            'email' => 'admin@admin.com',
            'password' => 'teste123',
            'status' => 1,
            'email_verified_at' => now(),
        ]);
    
        $role = Role::create([
            'name' => 'SuperAdmin',
            'guard_name' => 'admin',
            'system_role' => 1,
            'idReference' => $this->generateUniqueCodeRole()
        ],);
        
        $permissions = Permission::where('guard_name', '=', 'admin')->pluck('id', 'id')->all();
        $role->syncPermissions($permissions);
        $user->syncRoles(['SuperAdmin']);
        $user->assignRole([$role->id]);
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
