<?php

use App\Models\Scheme;
use Illuminate\Database\Seeder;

class SchemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $scheme = Scheme::create([
            'name' => 'Esquema inicial',
            'description' => ''
        ]);
    }
}
