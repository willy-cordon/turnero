<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::create(['name' => 'administrador']);
        $role->givePermissionTo('users_manage');
        $role->givePermissionTo('scheduler_admin');
        $role->givePermissionTo('scheduler_user');
        $role->givePermissionTo('scheduler_coordinator');

        $role = Role::create(['name' => 'turnista admin']);
        $role->givePermissionTo('scheduler_admin');
        $role->givePermissionTo('scheduler_user');

        $role = Role::create(['name' => 'turnista simple']);
        $role->givePermissionTo('scheduler_user');

        $role = Role::create(['name' => 'turnista coordinador']);
        $role->givePermissionTo('scheduler_user');
        $role->givePermissionTo('scheduler_coordinator');

        $role = Role::create(['name' => 'médico']);
        $role->givePermissionTo('scheduler_user');
        $role->givePermissionTo('scheduler_doctor');

        $role = Role::create(['name' => 'médico admin']);
        $role->givePermissionTo('scheduler_user');
        $role->givePermissionTo('scheduler_doctor');
    }
}