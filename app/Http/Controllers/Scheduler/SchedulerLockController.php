<?php

namespace App\Http\Controllers\Scheduler;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\SchedulerLock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SchedulerLockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $schedulerLocks = SchedulerLock::all();
        $locations = Location::all();
        return view('scheduler.locks.index', compact('schedulerLocks', 'locations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Location $location)
    {
        $action =  route("scheduler.locks.store");
        $method = 'POST';
        $currentLocks = SchedulerLock::where('location_id', "=", $location->id)->get()->map(function ($schedulerLock){
            return Carbon::createFromFormat(config('app.date_format'), $schedulerLock->lock_date);
        });

        return view('scheduler.locks.create_edit', compact('action','method', 'location', 'currentLocks'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $schedulerLock = SchedulerLock::create($request->all());
        $schedulerLock->location()->associate(Location::find($request->location_id))->save();
        return redirect()->route('scheduler.locks.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param   $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {   $schedulerLock = SchedulerLock::find($id);

        $action = route("scheduler.locks.update", [$schedulerLock->id]);
        $method = 'PUT';
        $currentLocks = SchedulerLock::where('location_id', "=", $schedulerLock->location_id)->get()->pluck('lock_date');
        $location = Location::find($schedulerLock->location_id);
        return view('scheduler.locks.create_edit', compact('schedulerLock', 'action','method', 'location', 'currentLocks'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $schedulerLock = SchedulerLock::find($id);
        $schedulerLock->update(['available_appointments'=>$request->available_appointments]);
        return redirect()->route('scheduler.locks.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        SchedulerLock::destroy($id);
        return redirect()->route('scheduler.locks.index');
    }
}
