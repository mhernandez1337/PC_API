<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use DB, Hash;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'first_name'    => 'Admin',
            'last_name'     => 'Admin',
            'email'         => 'mhernandez@nevada.unr.edu',
            'role'          => User::ROLE_ADMIN,
            'password'      =>  Hash::make('asdfasdf')
        ]);

        DB::table('users')->insert([
            'first_name'    => 'User',
            'last_name'     => 'User',
            'email'         => 'miggs_1337@yahoo.com',
            'role'          => User::ROLE_USER,
            'password'      =>  Hash::make('asdfasdf')
        ]);
    }
}
