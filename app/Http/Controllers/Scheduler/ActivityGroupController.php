<?php

namespace App\Http\Controllers\Scheduler;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduler\ActivityGroupRequest;
use App\Http\Requests\Scheduler\UpdateActivityGroupRequest;
use App\Models\ActivityGroup;
use App\Models\ActivityGroupType;
use App\Models\Location;
use App\Services\ActivityGroupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ActivityGroupController extends Controller
{

    private $activityGroupService;

    public function __construct(ActivityGroupService $service)
    {
        $this->activityGroupService = $service;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $activityGroups = $this->activityGroupService->all();

        $locations = Location::all();
        return view('scheduler.activity-groups.index', compact('activityGroups','locations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Location $location)
    {
        $action =  route("scheduler.activity-groups.store");
        $method = 'POST';
        $types  = ActivityGroup::ACTIVITY_GROUP_TYPES;
        $typeGroups = ActivityGroupType::all();

        return view('scheduler.activity-groups.create_edit', compact('action','method','location','types','typeGroups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ActivityGroupRequest $request)
    {

        $activitygroup = ActivityGroup::create($request->all());
        $activitygroup->location()->associate(Location::find($request->location_id))->save();
        return redirect()->route('scheduler.activity-groups.index');
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {

        $typeGroups = ActivityGroupType::all();
        $activityGroup = ActivityGroup::find($id);
        $action = route("scheduler.activity-groups.update", [$activityGroup->id]);
        $method = 'PUT';
        $location = Location::find($activityGroup->location_id);
        $types  = ActivityGroup::ACTIVITY_GROUP_TYPES;
        return view('scheduler.activity-groups.create_edit', compact('activityGroup', 'action','method', 'location', 'types','typeGroups'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ActivityGroupRequest $request, ActivityGroup $activityGroup)
    {

        $this->activityGroupService->update($activityGroup, $request);
        return redirect()->route('scheduler.activity-groups.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ActivityGroup $activityGroup)
    {
        $deleteStatus = $this->activityGroupService->destroy($activityGroup);
        return redirect()->route('scheduler.activity-groups.index')->with('status', $deleteStatus);
    }

    public function restore($id)
    {
        $this->activityGroupService->restore($id);
        return redirect()->route('scheduler.activity-groups.index');
    }


}
