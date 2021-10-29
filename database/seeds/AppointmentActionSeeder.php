<?php

use App\Models\AppointmentAction;
use Illuminate\Database\Seeder;

class AppointmentActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AppointmentAction::Create(['name'=>'1 – Confirmado']);
        AppointmentAction::Create(['name'=>'3 – Cumplido']);
        AppointmentAction::Create(['name'=>'5 – Retraso temporal']);
        AppointmentAction::Create(['name'=>'4 – Cancelado']);
        AppointmentAction::Create(['name'=>'2 – En Sitio']);
    }
}
