<?php

namespace App\Http\Controllers\Scheduler;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduler\ActivityRequest;
use App\Models\Activity;
use App\Models\ActivityAction;
use App\Models\ActivityGroup;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ActivityController extends Controller
{
    private $activityService;

    public function __construct(ActivityService $service)
    {
        $this->activityService = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $activities = $this->activityService->all();
        return view('scheduler.activities.index', compact('activities'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $act = ActivityAction::all();
        $activityActions = $act ->pluck('name','id');
        $activityGroups = ActivityGroup::all();
        $answers = Activity::ANSWERS;
//        $activity = Activity::all();

        $action =  route("scheduler.activities.store");
        $method = 'POST';
        return view('scheduler.activities.create_edit', compact('activityActions','activityGroups', 'action', 'method','answers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ActivityRequest $request)
    {
        $this->activityService->create($request);
        return redirect()->route('scheduler.activities.index');
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Activity $activity)
    {
        $action = route("scheduler.activities.update",[$activity->id]);
        $method = 'PUT';
        $activityActions = ActivityAction::all()->pluck('name','id');
        $answers = Activity::ANSWERS;
        $activityGroups = ActivityGroup::all();
        return view('scheduler.activities.create_edit', compact('activity','activityGroups','activityActions','action','method','answers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ActivityRequest $request, Activity $activity)
    {
        $this->activityService->update($activity, $request);
        return redirect()->route('scheduler.activities.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Activity $activity)
    {
        $deleteActivity = $this->activityService->destroy($activity);
        return redirect()->route('scheduler.activities.index')->with('status',$deleteActivity);
    }

    public function restore($id)
    {
        $this->activityService->restore($id);
        return redirect()->route('scheduler.activities.index');
    }
}
