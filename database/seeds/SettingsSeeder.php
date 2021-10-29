<?php

use App\Models\Settings;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Settings::set('init_hour', 8);
        Settings::set('end_hour', 20);
        Settings::set('appointment_init_minutes_size', 90);

    }
}
