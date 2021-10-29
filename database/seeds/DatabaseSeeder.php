<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionSeed::class);
        $this->call(RoleSeed::class);
        $this->call(UserSeed::class);
        $this->call(SettingsSeeder::class);
        $this->call(SchemeSeeder::class);
        $this->call(SequenceSeeder::class);
        $this->call(LocationSeeder::class);
        $this->call(DockSeeder::class);
        $this->call(AppointmentActionSeeder::class);
    }
}
