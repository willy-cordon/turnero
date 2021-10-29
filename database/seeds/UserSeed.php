<?php

use Illuminate\Database\Seeder;
use App\User;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create(['name' => 'SGTC ADMIN', 'email' => 'admin@sgtc-celsur.com', 'dni' => '99999999', 'phone'=> '15-9999-9999', 'password' => bcrypt('sgtc2020!*')]); $user->assignRole('administrador');
        $user = User::create(['name'=>'MARIANO JUZT', 'email'=>'mjuzt@celsur.com.ar', 'dni'=>'1131809338', 'phone'=>'1131809338', 'password'=> bcrypt('mjuzt2021')]); $user->assignRole('administrador');
        $user = User::create(['name'=>'MARCOS LORENZATTI', 'email'=>'mlorenzatti@celsur.com.ar', 'dni'=>'1154836131', 'phone'=>'1154836131', 'password'=> bcrypt('mlorenzatti2021')]); $user->assignRole('administrador');
    }
}
