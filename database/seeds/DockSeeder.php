<?php

use App\Models\Dock;
use Illuminate\Database\Seeder;

class DockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0; $i<10; $i++){
            Dock::create(
                [
                    'name' => 'Circuito '.($i+1),
                    'description' => '',
                    'location_id' => 1
                ]
            );
        }
    }
}
