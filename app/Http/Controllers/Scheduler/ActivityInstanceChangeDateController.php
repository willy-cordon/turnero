<?php

namespace App\Http\Controllers\Scheduler;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduler\ActivityInstanceChangeDateRequest;
use App\Models\ActivityGroupType;
use App\Models\ActivityInstance;
use App\Services\ActivityInstanceChangeDateService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ActivityInstanceChangeDateController extends Controller
{
    private $activityInstanceChangeDateService;

    public function __construct(ActivityInstanceChangeDateService $service){
        $this->activityInstanceChangeDateService = $service;
    }

    public function index()
    {

        $dateChangeActivity = $this->activityInstanceChangeDateService->getDateChangeActivity();
        return view('scheduler.activity-instance-change-date.change_date',compact('dateChangeActivity'));
    }

    public function activityInstanceChangeDay(ActivityInstanceChangeDateRequest $request)
    {

        $changeDayQuery = ActivityInstance::query();
        $changeDayQuery ->leftJoin('appointments','appointments.id','=','activity_instances.appointment_id');
        $changeDayQuery ->leftJoin('suppliers', 'suppliers.id','=','appointments.supplier_id');
        $changeDayQuery ->leftJoin('activities','activities.id','=','activity_instances.activity_id');
        $changeDayQuery ->leftJoin('activity_groups','activity_groups.id','=','activities.activity_group_id');
        $changeDayQuery ->leftJoin('activity_group_types','activity_group_types.id','=','activity_groups.activity_group_type_id');
        $changeDayQuery ->where('activity_instances.status','=',ActivityInstance::STATUS_TODO);
        $changeDayQuery ->where('activity_instances.date','>=',now());
        $changeDayQuery ->where('activity_group_types.id','=', $request->activity_type_id);
        $changeDayQuery ->where('suppliers.id','=',$request->supplier_id);

        $changeDayQuery->select(['activity_instances.id','activity_instances.date']);

        $activityInstances = $changeDayQuery->get();
        $day = $request->get('day');
        Log::debug(" ################################################");
        foreach ($activityInstances as $activityInstance){
            $originDate = $activityInstance->date;

            $oldDate = Carbon::parse($activityInstance->date);
            $newDate = Carbon::parse($activityInstance->date);

            $nexDate = $newDate->next($day);
            $prevDate = $oldDate->previous($day);

            $nextDiff = $nexDate->diffInDays($originDate);
            $prevDiff = $prevDate->diffInDays($originDate);

            Log::debug("originDate: ".$originDate);
            Log::debug("nexDate: ".$nexDate." / diff:".$nextDiff);
            Log::debug("prevDate: ".$prevDate." / diff:".$prevDiff);

            if ($nextDiff < $prevDiff)
            {
                Log::debug(" nextDiff < prevDiff");
                Log::debug("Fecha Anterior: ".$originDate." / Fecha a setear:".$nexDate);
                $activityChangeDayQuery = ActivityInstance::query();
                $activityChangeDayQuery ->where('id','=',$activityInstance->id);
                $activityChangeDayQuery ->update(['date'=>$nexDate]);

            }elseif ($prevDiff < $nextDiff){

                Log::debug("prevDiff < nextDiff");

                $now = Carbon::now();

                if ($prevDate <= $now){
                    $prevDate = $nexDate;
                    Log::debug("prevDate <= now");
                }
                Log::debug("Fecha Anterior: ".$originDate." / Fecha a setear:".$prevDate);
                $activityChangeDayQuery = ActivityInstance::query();
                $activityChangeDayQuery ->where('id','=',$activityInstance->id);
                $activityChangeDayQuery ->update(['date'=>$prevDate]);
            }
            Log::debug(" ==================================================================");
        }
        $dateChangeActivity = $this->activityInstanceChangeDateService->getDateChangeActivity();


        return redirect()->route('scheduler.activity-instance-change-date.index',compact('dateChangeActivity'))->with('message', 'Día cambiado satisfactoriamente');

    }

}
