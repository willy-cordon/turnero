<?php

namespace App\Services;

use App\Mail\ActivityActionSelected;
use App\Models\Activity;
use App\Models\ActivityAction;
use App\Models\ActivityGroup;
use App\Models\ActivityGroupType;
use App\Models\ActivityInstance;
use App\Models\ActivityInstanceChangeLog;
use App\Models\ActivityNotification;
use App\Models\Appointment;
use App\Models\Notification;
use App\Models\Supplier;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Arr;
use Throwable;
use Zendaemon\Services\Service;

final class ActivityInstanceService extends Service
{
     /**
     * Set model class name.
     *
     * @return void
     */
    protected function setModel(): void
    {
        $this->model = ActivityInstance::class;
    }

    public function activityGroupType($type)
    {
        $activityGroupTypeQuery = ActivityGroupType::query();
        $activityGroupTypeQuery ->where('name',$type);
        $activityGroupType = $activityGroupTypeQuery->first();
        return $activityGroupType;
    }

    public function activityCronUpdate(Model $activityInstance, $action)
    {
        $activityAction = ActivityAction::find($action);
        $status = $activityAction->activity_status_triggered;
        $activityInstance->update(['status'=>$status]);
        $activityInstance->activityAction()->associate($activityAction->id)->save();
    }

    public function update(Model $activityInstance, Request $request)
    {
        
        try {

            $activityActionOld = $activityInstance->activityAction;
            $activityStatusOld = $activityInstance->status;
            $activityAnswerOld = $activityInstance->answer;

            $request->get('status') != null || $request->get('status') != '' ? $status = $request->get('status') : $status = $activityInstance->status ;
            $request->get("answer") == null || $request->get("answer") == '' ? $answer = $activityInstance->answer : $answer = $request->get("answer");

            if ($request->get("action") != null || $request->get("action") != ''){
                $activityAction = ActivityAction::find($request->get("action"));

                $activityInstance->activityAction()->associate($activityAction)->save();

                if ($activityAction->activity_status_triggered != null || $activityAction->activity_status_triggered != '')
                {
                    $status = $activityAction->activity_status_triggered;
                }

               if ($activityAction->activity_fired != null ){

                   $supplierId = $activityInstance->appointment->supplier->id;
                   $activityFired = Activity::find($activityAction->activity_fired);
                   $activityFiredAction = $activityFired->activityActions()->first();

                   $appointmentQuery = Appointment::query();
                   $appointmentQuery ->leftJoin('suppliers','suppliers.id','=','appointments.supplier_id');
                   $appointmentQuery ->leftJoin('docks','docks.id','=','appointments.dock_id');
                   $appointmentQuery ->leftJoin('locations','locations.id','=','docks.location_id');
                   $appointmentQuery ->where('locations.id','=',$activityFired->activityGroup->location_id);
                   $appointmentQuery ->where('suppliers.id',$supplierId);
                   $appointmentQuery ->select(['appointments.id','appointments.created_by']);
                   $appointmentQuery->orderBy('appointments.created_at','desc');
                   $latestAppointment = $appointmentQuery->first();


                   $activityInstanceFired = ActivityInstance::create([
                       "date"=>Carbon::now()->format('Y-m-d'),
                       "status"=>ActivityInstance::STATUS_IN_PROGRESS,
                       "created_by"=> auth()->user()->id,
                       "fire_moment"=>NULL]);
                   $activityInstanceFired->appointment()->associate($latestAppointment->id)->save();
                   $activityInstanceFired->activity()->associate($activityFired)->save();
                   $activityInstanceFired->activityAction()->associate($activityFiredAction)->save();

                   /*notificación por mail*/
                   $bcc = ['mjuzt@celsur.com.ar', 'miguel@broobe.com','mlorenzatti@celsur.com.ar'];
                   //$bcc = ['miguel@broobe.com'];
                   $activityNotification = ActivityNotification::where('activity_action_id', $activityFiredAction->id)->
                   whereIn('trigger_status',[ActivityInstance::TRIGGER_ALL_CREATE,ActivityInstance::TRIGGER_ALL])->first();
                   if ($activityNotification) {
                       Log::debug('-NOTIFICACIÓN ALL CREATE-AUTOMATICA');
                       Log::debug($activityNotification);
                       $emails_to = explode(',', $activityNotification->emails_to);
                       $supplier = Supplier::find($activityInstance->appointment->supplier_id);
                       foreach ($emails_to as $email_to) {
                           Mail::to($email_to)->bcc($bcc)->send(new ActivityActionSelected($supplier, $activityNotification->subject.' - Estado: '.ActivityInstance::STATUS_IN_PROGRESS));
                       }
                   }
                   /*changelog de la actividad automatica*/
                   $value = $activityInstanceFired->answer.' | '.( $activityInstanceFired->activityAction ? $activityInstanceFired->activityAction->name : '')  .' | '.$activityInstanceFired->status;
                   $valueOld = "";
                   ActivityInstanceChangeLog::create(["value"=>$value, "value_old"=>$valueOld, "activity_instance_id"=>$activityInstance->id, "created_by"=>auth()->user()->id]);
               }
            }
            $activityInstance->update(["status" => $status, 'answer' => $answer]);


            /*Si vino desde el checkbox entonces busca una de las actions que tenga activity_status_triggered STATUS_IN_PROGRESS y se lo setea*/
            if(isset($request->setRadio)){
                $activityActionInProgress = $activityInstance->activity->activityActions()->where('activity_status_triggered',ActivityInstance::STATUS_IN_PROGRESS)->first();
                if($activityActionInProgress) {
                    $activityInstance->activityAction()->associate($activityActionInProgress)->save();
                }
            }


            /*changelog de la activityInstance*/
            $value = $activityInstance->answer.' | '.( $activityInstance->activityAction ? $activityInstance->activityAction->name : '')  .' | '.$activityInstance->status;
            $valueOld = $activityAnswerOld.' | '.( $activityActionOld ? $activityActionOld->name : '').' | '.$activityStatusOld;
            ActivityInstanceChangeLog::create(["value"=>$value, "value_old"=>$valueOld, "activity_instance_id"=>$activityInstance->id, "created_by"=>auth()->user()->id]);
            /**/

            $doctor_conf = config('app.doctor_activity_actions_ids');
            $doctor_activity_ids = explode(',',$doctor_conf);

            if (in_array($request->action,$doctor_activity_ids)){

                $data = ActivityAction::where('id','=',$request->action)->first();

                $queryUpdateSupplier = Appointment::query();
                $queryUpdateSupplier ->leftJoin('suppliers','suppliers.id' ,'=','appointments.supplier_id');
                $queryUpdateSupplier ->where('appointments.id' , '=', $activityInstance->appointment_id);
                $queryUpdateSupplier ->update(['suppliers.status'=>$data->name]);
            }

            $bcc = ['mjuzt@celsur.com.ar', 'miguel@broobe.com', 'mlorenzatti@celsur.com.ar'];
            //$bcc = ['willy@broobe.com'];

            $activityInstanceStatus = $request->get("status");

            Log::debug('-ACTIVITY NOTIFICATION UPDATE-');

            //Primero se fija si tiene configurado que mande siempre
            $activityNotification = ActivityNotification::where('activity_action_id', $request->get("action"))->
            where('trigger_status',ActivityInstance::TRIGGER_ALL)->first();
            if ($activityNotification) {
                Log::debug('-NOTIFICACIÓN ALL-');
                Log::debug($activityNotification);
                $emails_to = explode(',', $activityNotification->emails_to);
                $supplier = Supplier::find($activityInstance->appointment->supplier_id);
                foreach ($emails_to as $email_to) {
                    Mail::to($email_to)->bcc($bcc)->send(new ActivityActionSelected($supplier, $activityNotification->subject.' - Estado: '.$activityInstanceStatus));
                }
            }else{
                //En caso de que no encuentre nada,se fija si tiene configurado para mandar en el estado especifico
                $activityNotification = ActivityNotification::where('activity_action_id', $request->get("action"))->
                where('trigger_status',$activityInstanceStatus)->first();
                if ($activityNotification) {
                    Log::debug('-NOTIFICACIÓN '.$activityInstanceStatus.'-');
                    Log::debug($activityNotification);
                    $emails_to = explode(',', $activityNotification->emails_to);
                    $supplier = Supplier::find($activityInstance->appointment->supplier_id);
                    foreach ($emails_to as $email_to) {
                        Mail::to($email_to)->bcc($bcc)->send(new ActivityActionSelected($supplier, $activityNotification->subject));
                    }
                }else{
                    Log::debug('-NO MANDA NOTIFICACION - ID:'.$activityInstance->id);
                }
            }


        }catch (Throwable $e) {
            report($e);
            return ["status"=>"error", "message"=>$e->getMessage()];
        }
        return ["status"=>"ok"];
    }

    public function create(Request $request)
    {
        $activityAction = ActivityAction::find($request->get("action"));



        $appointment = Appointment::find($request->get('appointment'));
        $activityInstanceDate = Carbon::createFromFormat(config('app.date_format'), $request->get('date'));
        $data = ['date'=> $activityInstanceDate, 'created_by'=>$appointment->created_by];
        if($activityInstanceDate->isToday()){
            $data['status'] =$request->get('status');
            $data['answer'] =$request->get('answer');
            $data['status'] =$request->get('status');
        }else{
            $data['status'] = ActivityInstance::STATUS_TODO;
        }

        $activityInstance = ActivityInstance::create($data);
        $activityInstance->appointment()->associate($appointment)->save();
        $activityInstance->activity()->associate(Activity::find($request->get('activity')))->save();

        Log::debug('-ACTIVITY NOTIFICATION CREATE-');
        if($activityInstanceDate->isToday()){
            $activityInstance->activityAction()->associate($activityAction)->save();

            //MAIL NOTIFICATIONS
            $activityInstanceStatus = $request->get("status");
            $bcc = ['mjuzt@celsur.com.ar', 'miguel@broobe.com','mlorenzatti@celsur.com.ar'];
            //$bcc = ['miguel@broobe.com'];

            //Primero se fija si tiene configurado que mande siempre en la creacion
            $activityNotification = ActivityNotification::where('activity_action_id', $request->get("action"))->
            whereIn('trigger_status',[ActivityInstance::TRIGGER_ALL_CREATE,ActivityInstance::TRIGGER_ALL])->first();
            if ($activityNotification) {
                Log::debug('-NOTIFICACIÓN ALL CREATE-');
                Log::debug($activityNotification);
                $emails_to = explode(',', $activityNotification->emails_to);
                $supplier = Supplier::find($activityInstance->appointment->supplier_id);
                foreach ($emails_to as $email_to) {
                    Mail::to($email_to)->bcc($bcc)->send(new ActivityActionSelected($supplier, $activityNotification->subject.' - Estado: '.$activityInstanceStatus));
                }
            }else{
                //En caso de que no encuentre nada,se fija si tiene configurado para mandar en el estado especifico
                $activityNotification = ActivityNotification::where('activity_action_id', $request->get("action"))->
                where('trigger_status',$activityInstanceStatus)->first();
                if ($activityNotification) {
                    Log::debug('-NOTIFICACIÓN '.$activityInstanceStatus.'-');
                    Log::debug($activityNotification);
                    $emails_to = explode(',', $activityNotification->emails_to);
                    $supplier = Supplier::find($activityInstance->appointment->supplier_id);


                    foreach ($emails_to as $email_to) {
                        Mail::to($email_to)->bcc($bcc)->send(new ActivityActionSelected($supplier, $activityNotification->subject));
                    }
                }else{
                    Log::debug('-NO MANDA NOTIFICACION - ID:'.$activityInstance->id);
                }
            }

            $value = $request->get("answer").' | '.$activityAction->name.' | '.$request->get("status");
            ActivityInstanceChangeLog::create(["value"=>$value, "value_old"=>"", "activity_instance_id"=>$activityInstance->id, "created_by"=>auth()->user()->id]);
        }else{
            Log::debug('-NO MANDA NOTIFICACION PORQUE NO ES TODAY - ID:'.$activityInstance->id);
        }


        return $activityInstance;
    }


    public function bulkCreate(Appointment $appointment, $fireMoment)
    {

         DB::transaction(function() use($appointment,$fireMoment) {
              $activitiesQuery = Activity::Query();
              $activitiesQuery->leftJoin('activity_groups', 'activity_groups.id', '=', 'activities.activity_group_id');
              $activitiesQuery->where('activity_groups.location_id', $appointment->dock->location_id);
              $activitiesQuery->where('activity_groups.deleted_at', null);
              $activitiesQuery->where('activities.fire_moment', $fireMoment);
              $activitiesQuery->where('activity_groups.type', ActivityGroup::ACTIVITY_GROUP_AUTOMATIC);


              if($appointment->activityInstances()->count()>0){
                  $activitiesQuery->whereNotIn('activities.id', $appointment->activityInstances->pluck('activity_id'));
              }

              $activities = $activitiesQuery->select('activities.*')->get();
              $appointmentDate =  Carbon::createFromFormat(config('app.datetime_format'), $appointment->start_date);

              foreach ($activities as $activity){
                  $activityInstance = ActivityInstance::create(["date"=>Carbon::parse($appointmentDate->clone()->addDays($activity->days_from_appointment))->format('Y-m-d'), "status"=>ActivityInstance::STATUS_TODO, "created_by"=>$appointment->created_by, "fire_moment"=>$fireMoment]);
                  $activityInstance->appointment()->associate($appointment)->save();
                  $activityInstance->activity()->associate(Activity::find($activity->id))->save();
              }
        });

    }

    public function bulkUpdate(Appointment $appointment){
        $totalInstances = $appointment->activityInstances()->get();
        $appointmentDate =  Carbon::createFromFormat(config('app.datetime_format'), $appointment->start_date);
        foreach ($totalInstances as $totalInstance){

            if ($totalInstance->fire_moment == Activity::ANSWER_INIT && $totalInstance->date >= now()){
                $updateInstaceQuery = ActivityInstance::query();
                $updateInstaceQuery ->where('activity_instances.id','=',$totalInstance->id);
                $updateInstaceQuery ->update(["date"=>Carbon::parse($appointmentDate->clone()->addDays($totalInstance->activity->days_from_appointment))->format('Y-m-d')]);
            }
        }

    }

    public function getDataTables(Request $request){
        $usersNames = User::all()->mapWithKeys(function ($user){return [$user->id=>$user->name];});
        ## Read value
        $draw               = $request->get('draw');
        $start              = $request->get("start");
        $rowperpage         = $request->get("length");
        $columnIndex_arr    = $request->get('order');
        $columnName_arr     = $request->get('columns');
        $order_arr          = $request->get('order');
        $search_arr         = $request->get('search');
        $show               = $request->get('show');
        $showEdiary         = $request->get('showEdiary');
        $showVigilance         = $request->get('showVigilance');
        $showAppointment        = $request->get('showAppointment');
        $activityGroupType  = $request->get('activityGroupType');
        $columnIndex        = $columnIndex_arr[0]['column'];
        $activityGroupTypeQuery = ActivityGroupType::find($activityGroupType);

        $dateNow = now()->format('Y-m-d');

        $totalColumn = [];
        if ( isset( $columnName_arr ) ) {
            for ( $i=0, $ien=count($columnName_arr) ; $i<$ien ; $i++ ) {
                $requestColumn = $request['columns'][$i];

                if(!$requestColumn['search']['value'] == NULL){
                    $totalColumn [$requestColumn['data']] =$requestColumn['search']['value'];
                }
            }
        }

        $columnName = str_replace('-', '.',$columnName_arr[$columnIndex]['data']);
        $columnSortOrder = $order_arr[0]['dir'];


        $totalRecordsQuery =  ActivityInstance::query();
        $totalRecordsQuery->select('count(*) as allcount');
        if($show == 'pending'){
            $totalRecordsQuery->where('activity_instances.date', '<=', $dateNow );
            $totalRecordsQuery->whereIn('activity_instances.status', [ActivityInstance::STATUS_TODO, ActivityInstance::STATUS_IN_PROGRESS] )->count();
        }

//        $supplierStatusQuery = Supplier::

        // Fetch records
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
        $activityInstancesQuery->leftJoin('supplier_groups', 'supplier_groups.id', '=', 'suppliers.supplier_group_id');
        $activityInstancesQuery->whereNull('activities.deleted_at');
        $activityInstancesQuery->whereNull('activity_groups.deleted_at');
        $activityInstancesQuery->whereNull('activity_actions.deleted_at');

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



        if(auth()->user()->hasRole(User::ROLE_SCHEDULER) || auth()->user()->hasRole(User::ROLE_DOCTOR)) {
            $activityInstancesQuery->where('activity_instances.created_by', auth()->user()->id);
            $totalRecordsQuery->where('activity_instances.created_by', auth()->user()->id);
        }else if(auth()->user()->hasRole(User::ROLE_COORDINATOR) && $supervision_user){
            $activityInstancesQuery->whereIn('activity_instances.created_by', $supervised_users->pluck('id'));
            $totalRecordsQuery->whereIn('activity_instances.created_by',  $supervised_users->pluck('id'));
        }
        $totalRecords = $totalRecordsQuery->count();

        foreach ($totalColumn as $column => $value ){
            $db_column = str_replace('-', '.',$column);
            if ($db_column == 'suppliers.is_intervened') {
                if (strtolower($value) == 'si') {
                    $value = 1;
                }
                if (strtolower($value) == 'no') {
                    $value = 0;
                }
            }

            if($db_column == 'activity_instances.date' || $db_column == 'activity_instances.updated_at'){
                $db_column = DB::raw("DATE_FORMAT(".$db_column.",'%d/%m/%Y')");
            }
            $activityInstancesQuery->where($db_column, 'like', '%' .$value . '%');
        }

        $eDiaryVarGlobal = false;
        $eDiaryExpired = false;
        $eDiaryPending = false;
        $vigilanceVarGlobal = false;
        $appointmentVarGlobal = false;
        if ($activityGroupTypeQuery->name == ActivityInstance::GROUP_TYPE_EDIARY ) {$eDiaryVarGlobal = true;}
        if ($activityGroupTypeQuery->name == ActivityInstance::GROUP_TYPE_VIGILANCIA){$vigilanceVarGlobal = true;}
        if ($activityGroupTypeQuery->name == ActivityInstance::GROUP_TYPE_TURNOS){$appointmentVarGlobal = true;}
        if ($eDiaryVarGlobal == true || $vigilanceVarGlobal == true || $appointmentVarGlobal == true )
        {

            if ($showEdiary == ActivityInstance::STATUS_ALL || $showVigilance == ActivityInstance::STATUS_ALL || $showAppointment == ActivityInstance::STATUS_ALL )
            {
                $activityInstancesQuery->where('activity_group_types.id', '=', $activityGroupTypeQuery->id );
            }
            if ($showEdiary == ActivityInstance::STATUS_TODO || $showVigilance == ActivityInstance::STATUS_TODO || $showAppointment == ActivityInstance::STATUS_TODO)
            {
                $eDiaryPending = true;
                $activityInstancesQuery->where('activity_group_types.id', '=', $activityGroupTypeQuery->id );
                $activityInstancesQuery->where('activity_instances.date', '=', $dateNow );
                $activityInstancesQuery->whereIn('activity_instances.status', [ActivityInstance::STATUS_TODO]);
            }
            if ($showEdiary == ActivityInstance::STATUS_EXPIRED || $showVigilance == ActivityInstance::STATUS_EXPIRED || $showAppointment == ActivityInstance::STATUS_EXPIRED )
            {
                $eDiaryExpired=true;
                $activityInstancesQuery->where('activity_group_types.id', '=', $activityGroupTypeQuery->id );
                $activityInstancesQuery->where('activity_instances.date', '<', $dateNow );
                $activityInstancesQuery->whereIn('activity_instances.status', [ActivityInstance::STATUS_TODO]);
            }
            if ($showEdiary == ActivityInstance::STATUS_IN_PROGRESS || $showVigilance == ActivityInstance::STATUS_IN_PROGRESS || $showAppointment == ActivityInstance::STATUS_IN_PROGRESS )
            {
                $activityInstancesQuery->where('activity_group_types.id', '=', $activityGroupTypeQuery->id );
                $activityInstancesQuery->whereIn('activity_instances.status', [ActivityInstance::STATUS_IN_PROGRESS]);
            }
        }



        $activityInstancesQuery->where('appointments.action_id', '!=', Appointment::STATUS_CANCELED);
        $activityInstancesQuery->where('suppliers.status','!=', Supplier::STATUS_TWO);




        $activityInstancesQuery->select('activity_instances.*');
        $activityInstancesQuery->orderBy($columnName,$columnSortOrder);
        $totalRecordsWithFilter = $activityInstancesQuery->count();

        if($rowperpage != -1){
            $activityInstancesQuery->skip($start);
            $activityInstancesQuery->take($rowperpage);
        }
        $activityInstances = $activityInstancesQuery->get();
//        die();

        $data_arr = array();
        $data_arrEdiary = array();
        $data_arrVigilance = array();
        $data_arrAppointment = array();
        $sno = $start+1;

        $buttonSelectActivityAction = '';
        $selectActionAppointment = '';
        $buttonRadioActivity = '';

        foreach($activityInstances as $activityInstance){

//            Log::debug($activityInstance->appointment->supplier->scheme_id);
            $classEnabled = '';
            $buttonRadioActivity = '';
            $buttonSelectActivityAction = '';
            //Reglas generales
            if ($activityInstance->status == ActivityInstance::STATUS_DONE || $activityInstance->status == ActivityInstance::STATUS_CANCEL)
            {
                $buttonSelectActivityAction = 'disabled';
                $selectActionAppointment = 'disabled';
            }
            // E-Diary
            if ($eDiaryVarGlobal){
                if ($activityInstance->status == ActivityInstance::STATUS_TODO && $activityInstance->date <= $dateNow)
                {
                    //Log::debug($activityInstance->status.'-'.$activityInstance->date);
                    $classEnabled='enableClass';
                    $buttonRadioActivity = '';
                    $buttonSelectActivityAction = '';

                }else{
                    $buttonRadioActivity = 'disabled';
                    if ($activityInstance->status == ActivityInstance::STATUS_IN_PROGRESS ){
                        $buttonSelectActivityAction = '';
                    }else{
                        $buttonSelectActivityAction = 'disabled';
                    }
                }
                if($eDiaryExpired){
                    $buttonRadioActivity = '';
                    $buttonSelectActivityAction = '';
                }
                if($eDiaryPending){
                    $buttonRadioActivity = '';
                    $buttonSelectActivityAction = '';
                }
            }

            // Turnos
            if($appointmentVarGlobal){
                if ($activityInstance->date <= $dateNow && ($activityInstance->status == ActivityInstance::STATUS_TODO || $activityInstance->status == ActivityInstance::STATUS_IN_PROGRESS ) ){
                    $selectActionAppointment = '';
                }else{
                    $selectActionAppointment = 'disabled';
                }
            }

            if(!empty($activityInstance->appointment) && !empty($activityInstance->appointment->supplier)) {

                if ($activityInstance->date <= $dateNow && ($activityInstance->status == ActivityInstance::STATUS_TODO || $activityInstance->status == ActivityInstance::STATUS_IN_PROGRESS)) {

                    if($activityInstance->activity->activityGroup->type == ActivityGroup::ACTIVITY_GROUP_MANUAL && auth()->user()->hasRole(User::ROLE_SCHEDULER)){
                        $buttonEdit = '';
                    }else {
                        $buttonEdit = '<button type="button" class="btn btn-xs btn-success" onclick="showActivityInstanceEditDialog(' . $activityInstance->id . ')" title=" ' . trans('global.edit') . ' "><i class=\'fas fa-pen\'></i> </button> ';
                    }

                } else if (auth()->user()->can('scheduler_admin') || auth()->user()->can('scheduler_coordinator') && ($activityInstance->status == ActivityInstance::STATUS_DONE || $activityInstance->status == ActivityInstance::STATUS_CANCEL)) {
                    $buttonEdit = '<button type="button" class="btn btn-xs btn-success" onclick="showActivityInstanceEditDialog(' . $activityInstance->id . ')" title=" ' . trans('global.edit') . ' "><i class=\'fas fa-pen\'></i> </button> ';
                } else {
                    $buttonEdit = '';

                }

                $supplier_name = $activityInstance->appointment->supplier->wms_name;

                $supplier_name_link = '<a class="table-link" href=" ' . route('scheduler.suppliers.show', $activityInstance->appointment->supplier->id) . '" target="_blank">
                                        ' . $supplier_name . '  <i class="fas fa-external-link-square-alt"></i>
                                   </a>';

                $appointment_link = '<a class="table-link" href=" ' . route('scheduler.appointments.show', $activityInstance->appointment_id) . '" target="_blank">
                                        ' . $activityInstance->appointment_id . '  <i class="fas fa-external-link-square-alt"></i>
                                   </a>';



                $activityActionsEdit = '';
                $doctor_conf = config('app.doctor_activity_actions_ids');
                $doctor_activity_ids = explode(',',$doctor_conf);

                if(auth()->user()->hasRole(User::ROLE_DOCTOR) || auth()->user()->hasRole(User::ROLE_DOCTOR_ADMIN)){
                    $activityActions = $activityInstance->activity->activityActions->map(function ($activityAction) {
                        return ['id' => $activityAction->id, 'text' => $activityAction->name];
                    });
                }else{
                    $activityActions = $activityInstance->activity->activityActions->whereNotIn('id',$doctor_activity_ids)->map(function ($activityAction) {
                        return ['id' => $activityAction->id, 'text' => $activityAction->name];
                    });
                }

                $intervened_class = '';
                if (!auth()->user()->hasRole(User::ROLE_SCHEDULER) && !auth()->user()->hasRole(User::ROLE_DOCTOR) && auth()->user()->hasRole(User::ROLE_DOCTOR_ADMIN) && $activityInstance->appointment->supplier->is_intervened) {
                    $intervened_class = ' is_intervened';
                }

                $activityInstance->appointment->supplier->is_intervened != false ? $supplierIntervened = 'SI' : $supplierIntervened= 'NO';

                $supplier_dni = $activityInstance->appointment->supplier->wms_id;
                $supplier_phone = $activityInstance->appointment->supplier->phone;
                $supplier_email = $activityInstance->appointment->supplier->email;
                $status = $activityInstance->status;
                $buttonWsp = '<a target="_blank" class="btn btn-xs btn-success " href="https://wa.me/54'.$supplier_phone.'"><i style="font-size: 14px; padding: 2px"  class="fab fa-whatsapp"></i></a>';

                $actionButton = '';
                if ($eDiaryVarGlobal){
                    if (auth()->user()->hasRole(USER::ROLE_ADMIN) || auth()->user()->hasRole(USER::ROLE_COORDINATOR) || auth()->user()->hasRole(USER::ROLE_SCHEDULER_ADMIN)){
                        $actionButton = $buttonWsp.' '.$buttonEdit;
                    }else{
                        $actionButton = $buttonWsp;
                    }
                }
                if ($vigilanceVarGlobal){$actionButton = $buttonEdit;}
                if ($appointmentVarGlobal){
                    if (auth()->user()->hasRole(USER::ROLE_ADMIN) || auth()->user()->hasRole(USER::ROLE_COORDINATOR) || auth()->user()->hasRole(USER::ROLE_SCHEDULER_ADMIN)){
                        $actionButton = $buttonWsp.' '.$buttonEdit;
                    }else{
                        $actionButton = $buttonWsp;
                    }
//                    $actionButton = $buttonWsp;
                }
//                $actionButton = $buttonEdit.$buttonWsp;


                $activityInstance->answer == 'Si' ? $checkedYes='checked':$checkedYes = '';
                $activityInstance->answer == 'No' ? $checkedNot='checked':$checkedNot = '';

                $checkedYesValue = '';
                if($checkedYes == 'checked'){
                    $checkedYesValue = 'SI';
                }

                //-------------- inputs vista Ediary ----------------------
                $routeupdateInput = route('scheduler.activity-instances.update', [$activityInstance->id]);

                $inputRadio = '<form class="radioForm "><input data-activity-id = "'.$activityInstance->id.'" class="radioCheck m-1 activityRadio" type="radio"  name="radioName" value= "Si" '.$checkedYes.' '.$buttonRadioActivity.'  />
                                <label for="radio01-01">Si</label>
                               </form>';

                $actionSelectedValueName='';
                $inputSelect = '<form class="selectForm">';
                $inputSelect .='<select data-id-activity = "'.$activityInstance->id.'"  style="width:auto" name="client"  class="form-control to-select2 mySelect" '.$buttonSelectActivityAction.' '.$selectActionAppointment.'>';
                $inputSelect .= '<option ></option>';
                foreach ($activityActions as $activityData)
                {
                    $activityData['id'] == $activityInstance->activity_action_id ? $selectedSelect = 'selected' : $selectedSelect = '';
                    if($selectedSelect == 'selected'){
                        $actionSelectedValueName = $activityInstance->activityAction->name;
                    }
                    $inputSelect .=   '<option value="'.$activityData['id'].'" '.$selectedSelect.'>'.$activityData['text'].'</option>';
                }
                $inputSelect .= '</select>';
                $inputSelect .= '</form>';

                $data_arrEdiary[] = array(
                    "any" => '',
                    "anyTwo" => $actionButton,
                    "activity_instances-date" => Carbon::parse($activityInstance->date)->format('d/m/Y'),
                    "activity_instances-updated_at" => Carbon::parse($activityInstance->updated_at)->format(config('app.datetime_format')),
                    "suppliers-wms_name" => $supplier_name_link,
                    "activities-question_name" => $activityInstance->activity->question_name,
                    "activity_instances-status" => '<span class="ai-status ai-status-' . array_search($status, ActivityInstance::STATUS) . '">' . $status . '</span>',
                    "inputRadio" => $inputRadio,
                    "enableClass" => $classEnabled,
                    "inputSelect" => $inputSelect,
                    "activity_groups-name" => $activityInstance->activity->activityGroup->name,
                    "activities-name" => $activityInstance->activity->name,
                    "suppliers-wms_id" => $supplier_dni,
                    "suppliers-email" => $supplier_email,
                    "suppliers-phone" => $supplier_phone,
                    "supplier_groups-name" => $activityInstance->appointment->supplier->supplierGroup->name ?? '',
                    "appointments-id" => $appointment_link,
                    "appointment_actions-name" => $activityInstance->appointment->action->name,
                    "users-name" =>  Arr::exists($usersNames,$activityInstance->created_by) ? $usersNames[$activityInstance->created_by] : '',
                    "activity_instances-appointment_id" => $activityInstance->appointment_id ?? '',
                    "appointment_id" => $activityInstance->appointment_id,
                    "supplier_name" => $supplier_name,
                    "activity_instance_id" => $activityInstance->id,
                    "activity_actions" => json_encode(array_values($activityActions->toArray())),
                    "activity_instance_action" => $activityInstance->activity_action_id,
                    "activity_instance_answer" => $activityInstance->answer,
                    "activity_question_name" => $activityInstance->activity->question_name,
                    "activity_instance_status" => $status,
                    "edit_title" => $activityInstance->activity->activityGroup->name . " | " . $activityInstance->activity->name . " | " . Carbon::parse($activityInstance->date)->format('d/m/Y'),
                    "edit_data" => "<b>VOLUNTARIO:</b> " . $supplier_name . " | <b>DNI:</b> " . $supplier_dni . " | <b>EMAIL:</b> " . $supplier_email . " | <b>TEL.:</b> " . $supplier_phone,
                    "update_url" => route('scheduler.activity-instances.update', [$activityInstance->id]),
                    "is_intervened" => $intervened_class,
                    "action_selected_value_name"=>$actionSelectedValueName,
                    "checked_yes_value" =>$checkedYesValue
                );

                // -------------- array 2 -------------- //
//                $scheme_default = config('app.default_scheme');
//
//                $supplier_scheme= $activityInstance->appointment->supplier->scheme_id;




                $data_arrVigilance[] = array(
                    "any" => '',
                    "anyTwo" => $actionButton,
                    "activity_instances-date" => Carbon::parse($activityInstance->date)->format('d/m/Y'),
                    "suppliers-wms_name" => $supplier_name_link,
                    "suppliers-wms_id" => $supplier_dni,
                    "activity_groups-name" => $activityInstance->activity->activityGroup->name,
                    "activities-name" => $activityInstance->activity->name,
                    "activities-question_name" => $activityInstance->activity->question_name,
                    "activity_instances-answer" => $activityInstance->answer,
                    "activity_actions-name" => $activityInstance->activityAction ? $activityInstance->activityAction->name : '',
                    "activity_instances-status" => '<span class="ai-status ai-status-' . array_search($status, ActivityInstance::STATUS) . '">' . $status . '</span>',
                    "supplier_groups-name" => $activityInstance->appointment->supplier->supplierGroup->name ?? '',
                    "suppliers-email" => $supplier_email,
                    "suppliers-phone" => $supplier_phone,
                    "appointments-id" => $appointment_link,
                    "appointment_actions-name" => $activityInstance->appointment->action->name ?? '',
                    "users-name" =>  isset($usersNames[$activityInstance->created_by]) ? $usersNames[$activityInstance->created_by] : '',
                    "activity_instances-appointment_id" => $activityInstance->appointment_id ?? '',
                    "appointment_id" => $activityInstance->appointment_id,
                    "supplier_name" => $supplier_name,
                    "activity_instance_id" => $activityInstance->id,
                    "activity_actions" => json_encode(array_values($activityActions->toArray())),
                    "activity_instance_action" => $activityInstance->activity_action_id,
                    "activity_instance_answer" => $activityInstance->answer,
                    "activity_question_name" => $activityInstance->activity->question_name,
                    "activity_instance_status" => $status,
                    "edit_title" => $activityInstance->activity->activityGroup->name . " | " . $activityInstance->activity->name . " | " . Carbon::parse($activityInstance->date)->format('d/m/Y'),
                    "edit_data" => "<b>VOLUNTARIO:</b> " . $supplier_name . " | <b>DNI:</b> " . $supplier_dni . " | <b>EMAIL:</b> " . $supplier_email . " | <b>TEL.:</b> " . $supplier_phone,
                    "update_url" => route('scheduler.activity-instances.update', [$activityInstance->id]),
                    "is_intervened" => $intervened_class,
                    "suppliers-is_intervened" => $supplierIntervened
//                    "is_supplier_status" => $statusSupplier
                );

                $data_arrAppointment[] = array(
                    "any" => '',
                    "anyTwo" => $actionButton,
                    "activity_instances-date" => Carbon::parse($activityInstance->date)->format('d/m/Y'),
                    "suppliers-wms_name" => $supplier_name_link,
//                    "activities-question_name" => $activityInstance->activity->question_name,
                    "activity_instances-status" => '<span class="ai-status ai-status-' . array_search($status, ActivityInstance::STATUS) . '">' . $status . '</span>',
                    "activity_groups-name" => $activityInstance->activity->activityGroup->name,
                    "activities-name" => $activityInstance->activity->name,
                    "inputSelect" => $inputSelect,
                    "activity_instances-appointment_id" => $activityInstance->appointment_id ?? '',
                    "appointment_id" => $activityInstance->appointment_id,
                    "supplier_name" => $supplier_name,
                    "suppliers-wms_id" => $supplier_dni,
                    "suppliers-email" => $supplier_email,
                    "suppliers-phone" => $supplier_phone,
                    "appointments-id" => $appointment_link,
                    "appointment_actions-name" => $activityInstance->appointment->action->name,
                    "users-name" =>  isset($usersNames[$activityInstance->created_by]) ? $usersNames[$activityInstance->created_by] : '',
                    "activity_instance_id" => $activityInstance->id,
                    "activity_actions" => json_encode(array_values($activityActions->toArray())),
                    "activity_instance_action" => $activityInstance->activity_action_id,
                    "activity_instance_answer" => $activityInstance->answer,
                    "activity_question_name" => $activityInstance->activity->question_name,
                    "activity_instance_status" => $status,
                    "edit_title" => $activityInstance->activity->activityGroup->name . " | " . $activityInstance->activity->name . " | " . Carbon::parse($activityInstance->date)->format('d/m/Y'),
                    "edit_data" => "<b>VOLUNTARIO:</b> " . $supplier_name . " | <b>DNI:</b> " . $supplier_dni . " | <b>EMAIL:</b> " . $supplier_email . " | <b>TEL.:</b> " . $supplier_phone,
                    "update_url" => route('scheduler.activity-instances.update', [$activityInstance->id]),
                    "is_intervened" => $intervened_class,
                );
             }


        }

        if ($eDiaryVarGlobal ){$data_arr = $data_arrEdiary;}
        if ($vigilanceVarGlobal ){$data_arr = $data_arrVigilance;}
        if ($appointmentVarGlobal ){$data_arr = $data_arrAppointment;}


        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordsWithFilter,
            "iTotalDisplayRecords" => $totalRecordsWithFilter,
            "aaData" => $data_arr
        );

       return $response;
    }

}
