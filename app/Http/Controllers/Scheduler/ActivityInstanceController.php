<?php

namespace App\Http\Controllers\Scheduler;

use App\Exports\ActivityInstanceExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduler\ActivityInstanceRequest;
use App\Models\Activity;
use App\Models\ActivityGroup;
use App\Models\ActivityGroupType;
use App\Models\ActivityInstance;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Location;
use App\Models\Supplier;
use App\Services\ActivityInstanceService;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Rap2hpoutre\FastExcel\FastExcel;

class ActivityInstanceController extends Controller
{

    private $activityInstanceService;


    public function __construct(ActivityInstanceService $service){
        $this->activityInstanceService = $service;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    //Activities Vigilance
    public function indexAllVigilance()
    {

        $pageTitle = trans('scheduler.activity_instances_filter_global.All');
        $showVigilance = ActivityInstance::STATUS_ALL;
        $statuses = ActivityInstance::STATUS;
        $answers = ActivityInstance::ANSWERS;
        $export_all = false;
        if(!auth()->user()->hasRole(User::ROLE_SCHEDULER) && !auth()->user()->hasRole(User::ROLE_DOCTOR) && !auth()->user()->hasRole(User::ROLE_DOCTOR_ADMIN)){
            $export_all = true;
        }
        $activityGroupTypeId = ActivityGroupType::where('name',ActivityInstance::GROUP_TYPE_VIGILANCIA)->first();
        $activityGroupTypeVar =  $this->activityInstanceService->activityGroupType(ActivityInstance::GROUP_TYPE_VIGILANCIA);

        return view('scheduler.activity-instances.index-vigilance', compact('showVigilance', 'pageTitle', 'statuses', 'answers', 'export_all','activityGroupTypeId','activityGroupTypeVar'));
    }


    public function indexPendingVigilance()
    {
        $pageTitle = trans('scheduler.activity_instances_filter_global.Pending');
        $showVigilance = ActivityInstance::STATUS_TODO;
        $statuses = ActivityInstance::STATUS;
        $answers = ActivityInstance::ANSWERS;
        $export_all = false;
        $activityGroupTypeVar =  $this->activityInstanceService->activityGroupType(ActivityInstance::GROUP_TYPE_VIGILANCIA);

        return view('scheduler.activity-instances.index-vigilance', compact('showVigilance', 'pageTitle', 'statuses', 'answers', 'export_all','activityGroupTypeVar'));
    }

    public function indexInProgressVigilance()
    {
        $pageTitle = trans('scheduler.activity_instances_filter_global.InProgress');
        $showVigilance = ActivityInstance::STATUS_IN_PROGRESS;
        $statuses = ActivityInstance::STATUS;
        $answers = ActivityInstance::ANSWERS;
        $export_all = false;
        $activityGroupTypeVar =  $this->activityInstanceService->activityGroupType(ActivityInstance::GROUP_TYPE_VIGILANCIA);

        return view('scheduler.activity-instances.index-vigilance', compact('showVigilance', 'pageTitle', 'statuses', 'answers', 'export_all','activityGroupTypeVar'));
    }

    public function indexExpiredVigilance()
    {
        $pageTitle = trans('scheduler.activity_instances_filter_global.Expired');
        $showVigilance = ActivityInstance::STATUS_EXPIRED;
        $statuses = ActivityInstance::STATUS;
        $answers = ActivityInstance::ANSWERS;
        $export_all = false;
        $activityGroupTypeVar =  $this->activityInstanceService->activityGroupType(ActivityInstance::GROUP_TYPE_VIGILANCIA);

        return view('scheduler.activity-instances.index-vigilance', compact('showVigilance', 'pageTitle', 'statuses', 'answers', 'export_all','activityGroupTypeVar'));
    }
    //Activities E-diary
    public function indexAllEDiary()
    {

        $pageTitle = trans('scheduler.activity_instances_filter_global.All');
        $showEdiary = ActivityInstance::STATUS_ALL;
        $statuses = ActivityInstance::STATUS;
        $answers = ActivityInstance::ANSWERS;
        $export_all = false;
        if(!auth()->user()->hasRole(User::ROLE_SCHEDULER) && !auth()->user()->hasRole(User::ROLE_DOCTOR) && auth()->user()->hasRole(User::ROLE_DOCTOR_ADMIN)){
            $export_all = true;
        }
        $activityGroupTypeId = ActivityGroupType::where('name',ActivityInstance::GROUP_TYPE_EDIARY)->first();
        $activityGroupTypeVar =  $this->activityInstanceService->activityGroupType(ActivityInstance::GROUP_TYPE_EDIARY);


        return view('scheduler.activity-instances.index-eDiary', compact('showEdiary', 'pageTitle', 'statuses', 'answers', 'export_all','activityGroupTypeVar','activityGroupTypeId'));
    }

    public function indexPendingEDiary()
    {

        $pageTitle = trans('scheduler.activity_instances_filter_global.Pending');
        $showEdiary = ActivityInstance::STATUS_TODO;
        $statuses = ActivityInstance::STATUS;
        $answers = ActivityInstance::ANSWERS;
        $export_all = false;


        $activityGroupTypeVar =  $this->activityInstanceService->activityGroupType(ActivityInstance::GROUP_TYPE_EDIARY);

        return view('scheduler.activity-instances.index-eDiary', compact('showEdiary', 'pageTitle', 'statuses', 'answers', 'activityGroupTypeVar','export_all'));

    }

    public function indexExpiredEDiary()
    {
        $pageTitle = trans('scheduler.activity_instances_filter_global.Expired');
        $showEdiary = ActivityInstance::STATUS_EXPIRED;
        $statuses = ActivityInstance::STATUS;
        $answers = ActivityInstance::ANSWERS;
        $export_all = false;
        $activityGroupTypeVar =  $this->activityInstanceService->activityGroupType(ActivityInstance::GROUP_TYPE_EDIARY);


        return view('scheduler.activity-instances.index-eDiary', compact('showEdiary', 'pageTitle', 'statuses', 'answers','activityGroupTypeVar','export_all'));

    }

    public function indexInProgressEDiary()
    {
        $pageTitle = trans('scheduler.activity_instances_filter_global.InProgress');
        $showEdiary = ActivityInstance::STATUS_IN_PROGRESS;
        $statuses = ActivityInstance::STATUS;
        $answers = ActivityInstance::ANSWERS;
        $export_all = false;

        $activityGroupTypeVar =  $this->activityInstanceService->activityGroupType(ActivityInstance::GROUP_TYPE_EDIARY);

        return view('scheduler.activity-instances.index-eDiary', compact('showEdiary', 'pageTitle', 'statuses', 'answers','activityGroupTypeVar','export_all'));


    }

    //Activities Appointment
    public function indexAllAppointment()
    {

        $pageTitle = trans('scheduler.activity_instances_filter_global.All');
        $showAppointment = ActivityInstance::STATUS_ALL;
        $statuses = ActivityInstance::STATUS;
        $answers = ActivityInstance::ANSWERS;
        $export_all = false;
        if(!auth()->user()->hasRole(User::ROLE_SCHEDULER) && !auth()->user()->hasRole(User::ROLE_DOCTOR) && !auth()->user()->hasRole(User::ROLE_DOCTOR_ADMIN)){
            $export_all = true;
        }
        $activityGroupTypeId = ActivityGroupType::where('name',ActivityInstance::GROUP_TYPE_TURNOS)->first();
        $activityGroupTypeVar =  $this->activityInstanceService->activityGroupType(ActivityInstance::GROUP_TYPE_TURNOS);


        return view('scheduler.activity-instances.index-appointment', compact('showAppointment', 'pageTitle', 'statuses', 'answers', 'export_all','activityGroupTypeVar','activityGroupTypeId'));
    }

    public function indexPendingAppointment()
    {

        $pageTitle = trans('scheduler.activity_instances_filter_global.Pending');
        $showAppointment = ActivityInstance::STATUS_TODO;
        $statuses = ActivityInstance::STATUS;
        $answers = ActivityInstance::ANSWERS;
        $export_all = false;


        $activityGroupTypeVar =  $this->activityInstanceService->activityGroupType(ActivityInstance::GROUP_TYPE_TURNOS);

        return view('scheduler.activity-instances.index-appointment', compact('showAppointment', 'pageTitle', 'statuses', 'answers', 'activityGroupTypeVar','export_all'));

    }

    public function indexExpiredAppointment()
    {
        $pageTitle = trans('scheduler.activity_instances_filter_global.Expired');
        $showAppointment = ActivityInstance::STATUS_EXPIRED;
        $statuses = ActivityInstance::STATUS;
        $answers = ActivityInstance::ANSWERS;
        $export_all = false;
        $activityGroupTypeVar =  $this->activityInstanceService->activityGroupType(ActivityInstance::GROUP_TYPE_TURNOS);


        return view('scheduler.activity-instances.index-appointment', compact('showAppointment', 'pageTitle', 'statuses', 'answers','activityGroupTypeVar','export_all'));

    }

    public function indexInProgressAppointment()
    {
        $pageTitle = trans('scheduler.activity_instances_filter_global.InProgress');
        $showAppointment = ActivityInstance::STATUS_IN_PROGRESS;
        $statuses = ActivityInstance::STATUS;
        $answers = ActivityInstance::ANSWERS;
        $export_all = false;

        $activityGroupTypeVar =  $this->activityInstanceService->activityGroupType(ActivityInstance::GROUP_TYPE_TURNOS);

        return view('scheduler.activity-instances.index-appointment', compact('showAppointment', 'pageTitle', 'statuses', 'answers','activityGroupTypeVar','export_all'));

    }

    //End Methods index

    public function updateCheckbox(Request $request)
    {
        $arraysIds = $request->ids;
        $status = ActivityInstance::STATUS_IN_PROGRESS;
        foreach ( $arraysIds as $id )
        {
            if ($id != '')
            {
                $activitiesQuery = ActivityInstance::query();
                $activitiesQuery-> where('id','=', $id);
                $activitiesQuery->update(['status'=> $status, 'answer' => $request->answer]);
                $activityInstance = $activitiesQuery->first();
                $activityActionInProgress = $activityInstance->activity->activityActions()->where('activity_status_triggered',ActivityInstance::STATUS_IN_PROGRESS)->first();
                if($activityActionInProgress) {
                    $activityInstance->activityAction()->associate($activityActionInProgress)->save();
                }

            }
        }

    }

    public function create()
    {
        $action =  route("scheduler.activity-instances.store");
        $method = 'POST';

        $relatedModels['clients'] = Client::all();
        $locations = Location::all();
        foreach ($locations as $location){
            $relatedModels['activities'][$location->id] = Activity::leftJoin('activity_groups', 'activity_groups.id', '=', 'activities.activity_group_id')
                                                                   ->where('activity_groups.location_id', $location->id)
                                                                   ->where('activity_groups.deleted_at', null)
                                                                   ->where('activity_groups.type', ActivityGroup::ACTIVITY_GROUP_MANUAL)
                                                                   ->select('activities.id', 'activities.name')
                                                                   ->get()
                                                                   ->map(function ($data){
                                                                        return ['id'=>$data->id, 'text'=>$data->name];
                                                                   });
        }

        $activities = Activity::all();
        foreach ($activities as $activity){

            $doctor_conf = config('app.doctor_activity_actions_ids');
            $doctor_activity_actions_ids = explode(',',$doctor_conf);

            $data = $activity->activityActions->whereNotIn('id',$doctor_activity_actions_ids);

            foreach ($data as $activityAction){
                $relatedModels['activity_actions'][$activity->id][]=['id' => $activityAction->id, 'text' => $activityAction->name];
            }


            $relatedModels['activity_questions'][$activity->id] = $activity->question_name;
        }

        $statuses = [ActivityInstance::STATUS_IN_PROGRESS];
        $answers = ActivityInstance::ANSWERS;

        return view('scheduler.activity-instances.create', compact('action','method','relatedModels', 'answers', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ActivityInstanceRequest $request)
    {
        $this->activityInstanceService->create($request);

        return redirect()->route('scheduler.activity-instances.allVigilance');
    }


    public function update(Request $request, ActivityInstance $activityInstance)
    {
        return response()->json($this->activityInstanceService->update($activityInstance, $request));
    }

    public function export(Request $request){


        $now = now();

        return (new FastExcel($this->getActivitiesInstancesExportData($request)))->download('actividades-'.$now->format('YmdHis').'.xlsx',function ($activityInstance) {
            return [
                "Fecha"=>Carbon::parse($activityInstance->date)->format('d/m/Y'),
                "Voluntario"=>$activityInstance->supplier_name,
                "Estado"=>$activityInstance->status,
                "Grupo de Actividades"=>$activityInstance->group_name,
                "Actividad" => $activityInstance->activity_name,
                "Pregunta" => $activityInstance->question_name,
                "Respuesta" => $activityInstance->answer,
                "Acción" =>$activityInstance->action_name,
                "DNI" =>$activityInstance->supplier_dni,
                "EMAIL" =>$activityInstance->supplier_email,
                "Teléfono" =>$activityInstance->supplier_phone,
                "Turno" => $activityInstance->appointment_id,
                "Reclutador" =>$activityInstance->user_name,
                "Creado" =>Carbon::parse($activityInstance->created_at)->format(config('app.datetime_format')),
                "Actualizado" => Carbon::parse($activityInstance->updated_at)->format(config('app.datetime_format')),
                "Id"=> $activityInstance->id,
                "Actualizado por" =>$activityInstance->user_name_update
            ];
        });

    }

    private function getActivitiesInstancesExportData(Request $request){

        //TODO: Dejar de utilizar la logica en este controller y usar la del controller de exportación

        $idGroupType = $request->get('activityGroupTypeId');


        $activityInstancesQuery = ActivityInstance::Query();
        $activityInstancesQuery->leftJoin('appointments', 'appointments.id', '=', 'activity_instances.appointment_id');
        $activityInstancesQuery->leftJoin('appointment_actions', 'appointment_actions.id', '=', 'appointments.action_id');
        $activityInstancesQuery->leftJoin('suppliers', 'suppliers.id', '=', 'appointments.supplier_id');
        $activityInstancesQuery->leftJoin('docks', 'docks.id', '=', 'appointments.dock_id');
        $activityInstancesQuery->leftJoin('locations', 'locations.id', '=', 'docks.location_id');
        $activityInstancesQuery->leftJoin('activities', 'activities.id', '=', 'activity_instances.activity_id');
        $activityInstancesQuery->leftJoin('activity_actions', 'activity_actions.id', '=', 'activity_instances.activity_action_id');
        $activityInstancesQuery->leftJoin('activity_groups', 'activity_groups.id', '=', 'activities.activity_group_id');
        $activityInstancesQuery->leftJoin('activity_group_types', 'activity_group_types.id', '=', 'activity_groups.activity_group_type_id');
        $activityInstancesQuery->leftJoin('users', 'users.id', '=', 'activity_instances.created_by');
        $activityInstancesQuery->leftJoin('users as userc','userc.id','=','activity_instances.updated_by');
        $activityInstancesQuery->whereNull('activities.deleted_at');
        $activityInstancesQuery->whereNull('activity_groups.deleted_at');

        if (isset($idGroupType)){
            $activityInstancesQuery -> where('activity_group_types.id','=',$idGroupType);
        }

        $activityInstancesQuery ->where(function($queryAppointment){
            $queryAppointment->whereRaw('`suppliers`.`scheme_id` IN (select scheme_id from location_scheme where location_id = locations.id)')
                ->orWhere(function($query2){
                    $query2->whereNull('suppliers.scheme_id')
                        ->whereRaw(config('app.default_scheme').' IN (select scheme_id from location_scheme where location_id = locations.id)');
                });
        });
        $supervision_user = false;
        $supervised_users = User::where('supervisor_id', '=', auth()->user()->id)->get();
        if ($supervised_users->count()>0){$supervision_user = true;}

        if(auth()->user()->hasRole(User::ROLE_COORDINATOR) && $supervision_user){
            $activityInstancesQuery->whereIn('activity_instances.created_by',$supervised_users->pluck('id'));
        }

        $activityInstancesQuery->where('appointments.action_id', '!=', Appointment::STATUS_CANCELED);
        $activityInstancesQuery->where('suppliers.status','!=', Supplier::STATUS_TWO);

        $activityInstancesQuery->select('activity_instances.date as date',
            'activity_instances.id as id',
            'suppliers.wms_name as supplier_name',
            'activity_instances.status as status',
            'activity_groups.name as group_name',
            'activities.name as activity_name',
            'activities.question_name as question_name',
            'activity_instances.answer as answer',
            'activity_actions.name as action_name',
            'suppliers.wms_id as supplier_dni',
            'suppliers.email as supplier_email',
            'suppliers.phone as supplier_phone',
            'activity_instances.appointment_id as appointment_id',
            'users.name as user_name',
            'activity_instances.created_at as created_at',
            'activity_instances.updated_at as updated_at',
            'userc.name as user_name_update',
            'appointment_actions.name as appointment_action_name'

        );
       foreach ($activityInstancesQuery->cursor() as $activityInstance){
           yield $activityInstance;
       }
    }


    public function getActivityInstances(Request $request)
    {
        return  $this->activityInstanceService->getDataTables($request);
    }

    public function createInitial(Request $request)
    {

        $result = Appointment::where('action_id', 1)->skip($request->from)->take(500)->get();
        foreach ($result as $appointment){
            $this->activityInstanceService->bulkCreate($appointment,Activity::ANSWER_INIT);
        }

        return Response::json(["last_from"=>$request->from, "total"=> Appointment::where('action_id', 1)->select('count(*) as allcount')->count()]);
    }

    public function createFinish(Request $request)
    {
        $result = Appointment::where('action_id', 2)->where('next_step', 'Asignar próximo turno')->skip($request->from)->take(500)->get();
        foreach ($result as $appointment){
            $this->activityInstanceService->bulkCreate($appointment, Activity::ANSWER_FINISH);

        }

        return Response::json(["last_from"=>$request->from, "total"=> Appointment::where('action_id', 2)->where('next_step', 'Asignar próximo turno')->select('count(*) as allcount')->count()]);
    }


    public function getCounters(){
        $temp_all_ed = $temp_all_t = $temp_all_v = $temp2_all_t = 0;


        $countersQuery = ActivityInstance::query();
        $countersQuery->leftJoin('activities','activities.id','=','activity_instances.activity_id');
        $countersQuery->leftJoin('activity_groups','activity_groups.id','=','activities.activity_group_id');
        $countersQuery->leftJoin('activity_group_types','activity_groups.activity_group_type_id','=','activity_group_types.id');
        $countersQuery->leftJoin('appointments', 'activity_instances.appointment_id', '=', 'appointments.id');
        $countersQuery->leftJoin('docks', 'docks.id', '=', 'appointments.dock_id');
        $countersQuery->leftJoin('locations', 'locations.id', '=', 'docks.location_id');
        $countersQuery->leftJoin('suppliers', 'suppliers.id', '=', 'appointments.supplier_id');
        $countersQuery->whereNull('activities.deleted_at');
        $countersQuery->whereNull('activity_groups.deleted_at');
        $countersQuery->where('suppliers.status','!=', Supplier::STATUS_TWO);
        $countersQuery->where('appointments.action_id', '!=', Appointment::STATUS_CANCELED);
        $countersQuery->select('activity_group_types.name','activity_instances.status',DB::raw('count(*) as total'));
        $countersQuery->groupBy('activity_group_types.name', 'activity_instances.status');

        $countersQuery ->where(function($query1){
            $query1->whereRaw('`suppliers`.`scheme_id` IN (select scheme_id from location_scheme where location_id = locations.id)')
                ->orWhere(function($query2){
                    $query2->whereNull('suppliers.scheme_id')
                        ->whereRaw(config('app.default_scheme').' IN (select scheme_id from location_scheme where location_id = locations.id)');
                });
        });

        $supervision_user = false;
        $supervised_users = User::where('supervisor_id', '=', auth()->user()->id)->get();

        if ($supervised_users->count()>0){$supervision_user = true;}

        $countersQuery->when('activity_group_types.name' == ActivityInstance::GROUP_TYPE_TURNOS, function($q){
            return $q->where('activity_instances.created_by', auth()->user()->id);
        });


        if(auth()->user()->hasRole(User::ROLE_SCHEDULER) || auth()->user()->hasRole(User::ROLE_DOCTOR) ) {
            $countersQuery->where('activity_instances.created_by', auth()->user()->id);
        }else if(auth()->user()->hasRole(User::ROLE_COORDINATOR) && $supervision_user){
            $countersQuery->whereIn('activity_instances.created_by', $supervised_users->pluck('id'));
        }

        $countersQuery1 = clone $countersQuery;
        $countersQuery2 = clone $countersQuery;
        $countersQuery3 = clone $countersQuery;



        $countEDiaryInProgress = $countTurnosInProgress = $countVigilanciaInProgress = 0;
        foreach ($countersQuery1->get() as $counter){
            if($counter->name == ActivityInstance::GROUP_TYPE_EDIARY){
                $temp_all_ed += $counter->total;
                if($counter->status == ActivityInstance::STATUS_IN_PROGRESS) $countEDiaryInProgress = $counter->total;
            }
            if($counter->name == ActivityInstance::GROUP_TYPE_TURNOS){
                $temp_all_t += $counter->total;
                if($counter->status == ActivityInstance::STATUS_IN_PROGRESS) $countTurnosInProgress = $counter->total;
            }
            if($counter->name == ActivityInstance::GROUP_TYPE_VIGILANCIA){
                $temp_all_v += $counter->total;
                if($counter->status == ActivityInstance::STATUS_IN_PROGRESS) $countVigilanciaInProgress = $counter->total;
            }

        }
        $countTurnosAll = $temp_all_t;
        $countEDiaryAll=$temp_all_ed;
        $countVigilanciaAll = $temp_all_v;

        $countersQuery2->where('activity_instances.date', '=', now()->format('Y-m-d'));
        $countEDiaryPending = $countTurnosPending = $countVigilanciaPending = 0;
        foreach ($countersQuery2->get() as $counter){
            if($counter->name == ActivityInstance::GROUP_TYPE_EDIARY){
                if($counter->status == ActivityInstance::STATUS_TODO) $countEDiaryPending = $counter->total;
            }
            if($counter->name == ActivityInstance::GROUP_TYPE_TURNOS){
                if($counter->status == ActivityInstance::STATUS_TODO) $countTurnosPending = $counter->total;
            }
            if($counter->name == ActivityInstance::GROUP_TYPE_VIGILANCIA){
                if($counter->status == ActivityInstance::STATUS_TODO) $countVigilanciaPending = $counter->total;
            }
        }


        $countersQuery3->where('activity_instances.date', '<', now()->format('Y-m-d'));
        $countEDiaryExpired = $countTurnosExpired = $countVigilanciaExpired = 0;
        foreach ($countersQuery3->get() as $counter){
            if($counter->name == ActivityInstance::GROUP_TYPE_EDIARY){
                if($counter->status == ActivityInstance::STATUS_TODO) $countEDiaryExpired = $counter->total;
            }
            if($counter->name == ActivityInstance::GROUP_TYPE_TURNOS){
                if($counter->status == ActivityInstance::STATUS_TODO) $countTurnosExpired = $counter->total;
            }
            if($counter->name == ActivityInstance::GROUP_TYPE_VIGILANCIA){
                if($counter->status == ActivityInstance::STATUS_TODO) $countVigilanciaExpired = $counter->total;
            }
        }



        return Response::json([ "countEDiaryAll" => $countEDiaryAll,
                                "countEDiaryInProgress" => $countEDiaryInProgress,
                                "countEDiaryPending" => $countEDiaryPending,
                                "countEDiaryExpired" => $countEDiaryExpired,
                                "countTurnosAll"=>$countTurnosAll,
                                "countTurnosInProgress" => $countTurnosInProgress,
                                "countTurnosPending" => $countTurnosPending,
                                "countTurnosExpired" => $countTurnosExpired,
                                "countVigilanciaAll"=>$countVigilanciaAll,
                                "countVigilanciaInProgress"=>$countVigilanciaInProgress,
                                "countVigilanciaPending" => $countVigilanciaPending,
                                "countVigilanciaExpired" => $countVigilanciaExpired]);


    }

    public function updateActivityEdiary()
    {
        $now = now();
        $subDaysResult = $now->subDays(3)->format('Y-m-d');
        $activityEdiaryQuery = ActivityInstance::query();
        $activityEdiaryQuery->leftJoin('appointments', 'appointments.id', '=', 'activity_instances.appointment_id');
        $activityEdiaryQuery->leftJoin('appointment_actions', 'appointment_actions.id', '=', 'appointments.action_id');
        $activityEdiaryQuery->leftJoin('suppliers', 'suppliers.id', '=', 'appointments.supplier_id');
        $activityEdiaryQuery->leftJoin('activities', 'activities.id', '=', 'activity_instances.activity_id');
        $activityEdiaryQuery->leftJoin('activity_actions', 'activity_actions.id', '=', 'activity_instances.activity_id');
        $activityEdiaryQuery->leftJoin('activity_groups', 'activity_groups.id', '=', 'activities.activity_group_id');
        $activityEdiaryQuery->leftJoin('activity_group_types', 'activity_group_types.id', '=', 'activity_groups.activity_group_type_id');
        $activityEdiaryQuery->where('activity_group_types.name', '=', ActivityInstance::GROUP_TYPE_EDIARY );
        $activityEdiaryQuery->whereIn('activity_instances.status', [ActivityInstance::STATUS_IN_PROGRESS]);
        $activityEdiaryQuery->where('activity_instances.date','<=',$subDaysResult);
        $activityEdiaryQuery->where('suppliers.status','!=', Supplier::STATUS_TWO);
        $activityEdiaryQuery->select(['activity_instances.id','activity_instances.status','activity_instances.date']);

        $activityEdiarys = $activityEdiaryQuery->get();

        $actionEnv = config('app.action_cron_id');

        if(!empty($actionEnv))
        {
            //Recorremos las actividades que ya estan 3 dias en gestión
            foreach ($activityEdiarys as $activityEdiary)
            {
                $this->activityInstanceService->activityCronUpdate($activityEdiary,$actionEnv);
            }
        }


    }


}
