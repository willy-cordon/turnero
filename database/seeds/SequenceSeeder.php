<?php

use App\Models\Sequence;
use Illuminate\Database\Seeder;

class SequenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sequence = Sequence::create([
            'name' => 'Secuencia inicial',
            'description' => '',
            'show_in_workflow' => true
        ]);
    }
}
