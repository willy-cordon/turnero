<?php

namespace App\Http\Controllers\Scheduler;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduler\SchedulerCellLockRequest;
use App\Models\Dock;
use App\Models\Location;
use App\Models\SchedulerCellLock;
use App\Services\SchedulerCellLockService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SchedulerCellLockController extends Controller
{

    private $schedulerCellLocksService;

    public function __construct(SchedulerCellLockService $service)
    {
        $this->schedulerCellLocksService = $service;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cellLocks = SchedulerCellLock::all();
        $locations = Location::all();

        return view('scheduler.cell-locks.index', compact('cellLocks','locations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Location $location)
    {

        $locksTypes = SchedulerCellLock::TYPES;
        $hours = $this->schedulerCellLocksService->getHours($location);
        $docks = $this->schedulerCellLocksService->getDocks($location);
        $action = route('scheduler.cell-locks.store');
        $method = 'POST';

        return view('scheduler.cell-locks.create', compact('location','method', 'action','locksTypes','docks', 'hours'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SchedulerCellLockRequest $request)
    {
//dd($request);

       $this->schedulerCellLocksService->create($request);
       return redirect()->route('scheduler.cell-locks.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getCellLocks(Request $request)
    {

        return $this->schedulerCellLocksService->dataTablesCellLocks($request);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $deleteStatus = SchedulerCellLock::find($id);
        $deleteStatus ->delete();
        return redirect()->route('scheduler.cell-locks.index')->with('status', $deleteStatus);
    }
}
