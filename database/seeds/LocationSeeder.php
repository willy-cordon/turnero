<?php

use App\Models\Location;
use Illuminate\Database\Seeder;


class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $location = Location::create([
            'name' => '1 - V1 Visita 1',
            'description' => ''
        ]);
    }
}
