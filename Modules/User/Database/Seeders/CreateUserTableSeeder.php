<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\User;

class CreateUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        User::create([
            'name' => 'User Admin',
            'last_name' => 'teste',
            'phone' => '09855656522',
            'address' => 'av mensu 521',
            'doc_id' => '1234567',
            'email' => 'user@user.com',
            'password' => 'teste123',
            'status' => 1,
        ]);
    }
}
