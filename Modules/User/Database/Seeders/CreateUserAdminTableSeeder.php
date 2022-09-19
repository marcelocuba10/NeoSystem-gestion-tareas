<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\User\Entities\User;

//spatie
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateUserAdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $user =  User::create([
            'idReference' => $this->generateUniqueCodeUser(),
            'name' => 'User Admin',
            'last_name' => 'teste',
            'phone' => '09855656522',
            'address' => 'av mensu 521',
            'doc_id' => '1234567',
            'email' => 'user@user.com',
            'password' => 'teste123',
            'company_name' => 'empresa teste',
            'status' => 1,
            'main_user' => 1,
        ]);

        $role = Role::create([
            'name' => 'Admin',
            'guard_name' => 'web',
            'system_role' => 1,
            'idReference' => $this->generateUniqueCodeRole()
        ],);

        $permissions = Permission::where('guard_name', '=', 'web')->pluck('id', 'id')->all();
        $role->syncPermissions($permissions);
        $user->syncRoles(['Admin']);
        $user->assignRole([$role->id]);
    }

    public function generateUniqueCodeUser()
    {
        do {
            $idReference = random_int(100000, 999999);
        } while (
            DB::table('users')->where("idReference", "=", $idReference)->first()
        );

        return $idReference;
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
