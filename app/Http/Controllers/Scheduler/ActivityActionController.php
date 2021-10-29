<?php

namespace App\Http\Controllers\Scheduler;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduler\ActivityActionRequest;
use App\Models\Activity;
use App\Models\ActivityAction;
use App\Models\ActivityGroup;
use App\Models\ActivityInstance;
use App\Services\ActivityActionService;
use Illuminate\Http\Request;

class ActivityActionController extends Controller
{
    private $activityActionService;

    public function __construct(ActivityActionService $service)
    {
        $this->activityActionService = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $activityActions = $this->activityActionService->all();
        return view('scheduler.activity-actions.index', compact('activityActions'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $activitiesQuery = Activity::query();
        $activitiesQuery ->leftJoin('activity_groups','activity_groups.id','=','activities.activity_group_id');
        $activitiesQuery ->where('activity_groups.type','=', ActivityGroup::ACTIVITY_GROUP_MANUAL);
        $activitiesQuery ->select(['activities.id','activities.name','activities.question_name']);
        $activitiesManuals = $activitiesQuery->get();

        $status = ActivityInstance::STATUS;
        $action =  route("scheduler.activity-actions.store");
        $method = 'POST';
        return view('scheduler.activity-actions.create_edit', compact('action','method','status','activitiesManuals'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ActivityActionRequest $request)
    {
        $this->activityActionService->create($request);
        return redirect()->route('scheduler.activity-actions.index');
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
    public function edit(ActivityAction $activityAction)
    {
        $activitiesQuery = Activity::query();
        $activitiesQuery ->leftJoin('activity_groups','activity_groups.id','=','activities.activity_group_id');
        $activitiesQuery ->where('activity_groups.type','=', ActivityGroup::ACTIVITY_GROUP_MANUAL);
        $activitiesQuery ->select(['activities.id','activities.name','activities.question_name']);
        $activitiesManuals = $activitiesQuery->get();



        $status = ActivityInstance::STATUS;
        $action = route("scheduler.activity-actions.update", [$activityAction->id]);
        $method = 'PUT';
        return view('scheduler.activity-actions.create_edit', compact('activityAction', 'action', 'method','status','activitiesManuals'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ActivityActionRequest $request, ActivityAction $activityAction)
    {
        $this->activityActionService->update($activityAction, $request);
        return redirect()->route('scheduler.activity-actions.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ActivityAction $activityAction)
    {
        $deleteActivityAction = $this->activityActionService->destroy($activityAction);
        return redirect()->route('scheduler.activity-actions.index')->with('status',$deleteActivityAction);
    }

    public function restore($id)
    {
        $this->activityActionService->restore($id);
        return redirect()->route('scheduler.activity-actions.index');
    }
}
