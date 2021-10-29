<?php

namespace App\Http\Controllers\Scheduler;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduler\StoreLocationRequest;
use App\Http\Requests\Scheduler\UpdateLocationRequest;
use App\Models\AppointmentAction;
use App\Models\Location;
use App\Models\Scheme;
use App\Models\Sequence;
use App\Services\LocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    /**
     * @var $locationService
     */
    private $locationService;
    private $hours = [  "0"=>"00:00", "0.5"=>"00:30",
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

    private $spots = [  "15"=>"15 min",
                        "30"=>"30 min",
                        "60"=>"1 hora",
                        "90"=>"1.5 horas",
                        "120"=>"2 horas",
                        "150"=>"2.5 horas",
                        "180"=>"3 horas",
                        "210"=>"3.5 horas",
                        "240"=>"4 horas"];

    public function __construct(LocationService $service)
    {
        $this->locationService = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $locations = $this->locationService->all();
        $hours = $this->hours;
        $spots = $this->spots;
        return view('scheduler.locations.index', compact('locations', 'hours', 'spots'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $action =  route("scheduler.locations.store");
        $method = 'POST';
        $hours = $this->hours;
        $spots = $this->spots;
        $locations = Location::all();
        $actions = AppointmentAction::all();
        $schemes = Scheme::all()->pluck('name','id');
        $sequences = Sequence::all();
        return view('scheduler.locations.create_edit', compact('action','method', 'hours', 'spots', 'locations', 'actions','schemes','sequences'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreLocationRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreLocationRequest $request)
    {
        Log::debug($request);
        $this->locationService->create($request);
        return redirect()->route('scheduler.locations.index');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Location $location)
    {
        $action = route("scheduler.locations.update", [$location->id]);
        $method = 'PUT';
        $hours = $this->hours;
        $spots = $this->spots;
        $locations = Location::where('id','!=',$location->id)->get();
        $actions = AppointmentAction::all();
        $schemes = Scheme::all()->pluck('name','id');
        $sequences = Sequence::all();
        return view('scheduler.locations.create_edit', compact('location', 'action', 'method','hours', 'spots', 'locations', 'actions','schemes','sequences'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param UpdateLocationRequest $request
     * @param Location $location
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateLocationRequest $request, Location $location)
    {

        Log::debug($request);
        $this->locationService->update($location, $request);
        return redirect()->route('scheduler.locations.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Location $location
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Location $location)
    {
        $deleteStatus = $this->locationService->destroy($location);
        return redirect()->route('scheduler.locations.index')->with('status', $deleteStatus);
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        $this->locationService->restore($id);
        return redirect()->route('scheduler.locations.index');
    }
}
