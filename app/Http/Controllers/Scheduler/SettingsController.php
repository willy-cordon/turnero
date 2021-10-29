<?php

namespace App\Http\Controllers\Scheduler;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {

        $hours = [  "0"=>"00:00", "0.5"=>"00:30",
                    "1"=>'01:00', "1.5"=>'01:30',
                    "2"=>'02:00', "2.5"=>'02:30',
                    "3"=>'03:00', "3.5"=>'03:30',
                    "4"=>'04:00', "4.5"=>'04:30',
                    "5"=>'05:00', "5.5"=>'05:30',
                    "6"=>'06:00', "6.5"=>'06:30',
                    "7"=>'07:00', "7.5"=>'07:30',
                    "8"=>'08:00', "8.5"=>'08:30',
                    "9"=>'09:00', "9.5"=>'09:30',
                    "10"=>'10:00', "10.5"=>'10:30',
                    "11"=>'11:00', "11.5"=>'11:30',
                    "12"=>'12:00', "12.5"=>'12:30',
                    "13"=>'13:00', "13.5"=>'13:30',
                    "14"=>'14:00', "14.5"=>'14:30',
                    "15"=>'15:00', "15.5"=>'15:30',
                    "16"=>'16:00', "16.5"=>'16:30',
                    "17"=>'17:00', "17.5"=>'17:30',
                    "18"=>'18:00', "18.5"=>'18:30',
                    "19"=>'19:00', "19.5"=>'19:30',
                    "20"=>'20:00', "20.5"=>'20:30',
                    "21"=>'21:00', "21.5"=>'21:30',
                    "22"=>'22:00', "22.5"=>'22:30',
                    "23"=>'23:00', "23.5"=>'23:30',
            ];

        $spots = ["30"=>"30 min",
                  "60"=>"1 hora",
                  "90"=>"1.5 horas",
                  "120"=>"2 horas",
                  "150"=>"2.5 horas",
                  "180"=>"3 horas",
                  "210"=>"3.5 horas",
                  "240"=>"4 horas"];

        $init_hour = Settings::get('init_hour', 8);
        $end_hour = Settings::get('end_hour', 18);
        $appointment_init_minutes_size = Settings::get('appointment_init_minutes_size', 30);

        return view('scheduler.settings.create', compact('init_hour', 'end_hour','appointment_init_minutes_size', 'hours', 'spots'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        Settings::set('init_hour', $request->init_hour);
        Settings::set('end_hour', $request->end_hour);
        Settings::set('appointment_init_minutes_size', $request->appointment_init_minutes_size);

        return redirect()->route('scheduler.settings.create');
    }
}
