<?php

namespace App\Services;

use App\Mail\SupplierInterventionAdvice;
use App\Models\ActivityInstance;
use App\Models\Appointment;
use App\Models\AppointmentAction;
use App\Models\AppointmentChangeLog;
use App\Models\Client;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Scheme;
use App\Models\Sequence;
use App\Models\Supplier;
use App\Models\SupplierGroup;
use App\Models\SupplierInterventionLog;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use League\CommonMark\Extension\TableOfContents\TableOfContentsGenerator;
use Throwable;
use Zendaemon\Services\Service;
use Zendaemon\Services\Traits\DestroyModel;



final class SupplierService extends Service
{
    use DestroyModel;

    const STATUS_FILTER_EXPIRED = 'VENCIDA';
    const STATUS_FILTER_IN_RANGE = 'EN RANGO';
    const STATUS_FILTER_TO_EXPIRED = 'A VENCER';
    const STATUS_FILTER_PENDING = 'PENDIENTE';
    const STATUS_FILTER_COMPLETE_IN_RANGE = 'COMPLETADO EN RANGO';
    const STATUS_FILTER_COMPLETE_OUT_RANGE = 'COMPLETADO FUERA DE RANGO';
    const STATUS_FILTER_STATELESS = 'SIN ESTADO';



    /**
     * Set model class name.
     *
     * @return void
     */
    protected function setModel(): void
    {
        $this->model = Supplier::class;
    }

    /**
     * @return Supplier[]|\Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        if(auth()->user()->can('scheduler_admin') || auth()->user()->can('scheduler_coordinator') ){
            return $this->model::all();
        }else{
            return $this->model::where('created_by', auth()->user()->id)->get();
        }
    }

    public function update(Model $model, Request $request): Model
    {
//        Log::debug($model);
        //Log::debug($request);
        $addresses = [
            ['old'=>$model->address, 'new'=> $request->address],
            ['old'=>$model->validate_address, 'new'=> $request->validate_address]
        ];

        $appointmentsQuery = Appointment::query();
        $appointmentsQuery ->where('supplier_id',$model->id);
        $appointmentsQuery ->whereNotIn('action_id',[2,4]);
        $appointments = $appointmentsQuery->get();

        foreach ($addresses as $address) {
            if($address['old'] != $address['new']) {
                foreach ($appointments as $appointment) {
                    AppointmentChangeLog::create
                    (
                        [
                        'field_name' => 'Dirección',
                        'field_value_text' => $address['new'],
                        'field_value_text_old' => $address['old'],
                        'appointment_id' => $appointment->id,
                        'created_by' => auth()->user()->id
                        ]
                    );
                }
            }
        }
        $model->update($request->except(['client_id']));
        if (!config('app.single_name')){
            $model->wms_name =  ucwords(strtolower($request->lastname)).', '.ucwords(strtolower($request->name));
            $model->save();
        }
        return $model;

    }
    public function create(Request $request): Model
    {

        $supplier = $this->model::create($request->all());
        if (!config('app.single_name')){
            $supplier->wms_name =  ucwords(strtolower($request->lastname)).', '.ucwords(strtolower($request->name));
            $supplier->save();
        }
        $supplier->client()->associate(Client::find($request->client_id))->save();

        return $supplier;
    }

    public function toggleIntervention(Request $request)
    {
        $supplier_id = trim($request->supplier_id);
        if (empty($supplier_id)) {
            return  ["status"=>"error", "message"=>'missing parameter - supplier_id'];
        }
        $intervention_status = trim($request->intervention_status);
        if (empty($intervention_status)) {
            return  ["status"=>"error", "message"=>'missing parameter - intervention_status'];
        }
        try {
            $supplier = Supplier::find($supplier_id);

            $appointments = Appointment::where('supplier_id', $supplier->id)->get();

            $activityInstancesQuery = ActivityInstance::Query();
            $activityInstancesQuery->leftJoin('appointments', 'appointments.id', '=', 'activity_instances.appointment_id');
            $activityInstancesQuery->where('appointments.supplier_id', $supplier->id);
            $activityInstances = $activityInstancesQuery->select('activity_instances.*')->get();

            if($intervention_status === 'true'){
                $supplier->update(['created_by'=>auth()->user()->id, 'is_intervened'=>true]);
                $user_id = auth()->user()->id;
                $recruiter = User::find($supplier->recruiter_id);
//                $bcc = ['willy@broobe.com'];
                $bcc = ['mjuzt@celsur.com.ar'];
                if(!empty($recruiter->supervisor_id)){
                    $supervisor = User::find($recruiter->supervisor_id);
                    $bcc[]=$supervisor->email;
                }
                Mail::to($recruiter->email)->bcc($bcc)->send(new SupplierInterventionAdvice($supplier));

                $subject = 'Voluntario intervenido por: ' . auth()->user()->name;
                $notificationCreate = Notification::create([
                    'email_subject' =>$subject,
                    'type' => Notification::INTERVENTION,
                    'created_by' => $supplier->recruiter_id
                ]);
                $notificationCreate->supplier()->associate(Supplier::find($supplier->id))->save();

            }else{
                $subject = 'Voluntario desintervenido por: ' . auth()->user()->name;
                //Creamos la notificacion
                $notificationCreate = Notification::create([
                    'email_subject' => $subject,
                    'type' => Notification::NO_INTERVENTION,
                    'created_by' => $supplier->recruiter_id
                ]);
                $notificationCreate->supplier()->associate(Supplier::find($supplier->id))->save();


                $supplier->update(['created_by'=>$supplier->recruiter_id, 'is_intervened'=>false]);
                $user_id = $supplier->recruiter_id;

                $description = '<b> '.auth()->user()->name.'</b>'.' desintervino al voluntario ';

                $interventionLog = SupplierInterventionLog::create([
                    'description' => $description,
                    'intervention_reason' => SupplierInterventionLog::NO_INTERVENTION
                ]);
                $interventionLog->supplier()->associate(Supplier::find($supplier_id))->save();

                //Pasamos las actividades Ediary a canceladas segun la fecha
                $interventionLogQuery = SupplierInterventionLog::query();
                $interventionLogQuery->where('supplier_id',$supplier_id);
                $interventionLogQuery->where('intervention_reason','=',SupplierInterventionLog::REASON_ONE);
                $interventionLogQuery->orderBy('created_at','desc');
                $interventionResult = $interventionLogQuery->first();

                $dateIntervention = $interventionResult->created_at;
                $dateDesIntervention = now();


                $activityInstancesQuery = ActivityInstance::query();
                $activityInstancesQuery ->leftJoin('appointments','appointments.id','=','activity_instances.appointment_id');
                $activityInstancesQuery ->leftJoin('suppliers','suppliers.id','=','appointments.supplier_id');
                $activityInstancesQuery->leftJoin('activities', 'activities.id', '=', 'activity_instances.activity_id');
                $activityInstancesQuery->leftJoin('activity_actions', 'activity_actions.id', '=', 'activity_instances.activity_action_id');
                $activityInstancesQuery->leftJoin('activity_groups', 'activity_groups.id', '=', 'activities.activity_group_id');
                $activityInstancesQuery->leftJoin('activity_group_types', 'activity_group_types.id', '=', 'activity_groups.activity_group_type_id');
                $activityInstancesQuery ->where('suppliers.id','=', $supplier_id);
                $activityInstancesQuery ->where('activity_group_types.name','=',ActivityInstance::GROUP_TYPE_EDIARY);
                $activityInstancesQuery ->where('activity_instances.status','=',ActivityInstance::STATUS_TODO);
                $activityInstancesQuery ->whereBetween('activity_instances.date',[$dateIntervention,$dateDesIntervention]);
                $activityInstancesQuery ->update(['activity_instances.status' => ActivityInstance::STATUS_CANCEL]);


            }
            foreach ($appointments as $appointment) {
                $appointment->update(['created_by'=>$user_id]);
            }

            foreach ($activityInstances as $activityInstance) {
                $activityInstance->update(['created_by'=>$user_id]);
            }

        }catch (Throwable $e) {
            report($e);
            return  ["status"=>"error", "message"=>$e->getMessage()];
        }
        return ["status"=>"ok"];
    }

    public function getDataTables(Request $request)
    {
        $usersNames = User::all()->mapWithKeys(function ($user){return [$user->id=>$user->name];});
        $schemeNames = Scheme::all()->mapWithKeys(function ($scheme){return [$scheme->id=>$scheme->name];});
        $datetime_columns = ['suppliers.created_at'];
        ## Read value
        $draw            = $request->get('draw');
        $start           = $request->get("start");
        $rowperpage      = $request->get("length");
        $columnIndex_arr = $request->get('order');
        $columnName_arr  = $request->get('columns');
        $order_arr       = $request->get('order');
        $search_arr      = $request->get('search');
        $columnIndex     = $columnIndex_arr[0]['column'];

        $searchColumns = [];
        if ( isset( $columnName_arr ) ) {
            for ( $i=0, $ien=count($columnName_arr) ; $i<$ien ; $i++ ) {
                $requestColumn = $request['columns'][$i];

                if(!$requestColumn['search']['value'] == NULL){
                    $searchColumns[$requestColumn['data']] =$requestColumn['search']['value'];
                }
            }
        }

        $columnName = str_replace('-', '.',$columnName_arr[$columnIndex]['data']);
        $columnSortOrder = $order_arr[0]['dir'];

        // Total records
        $totalRecords = Supplier::select('count(*) as allcount')->count();

        $supervision_user = false;
        $supervised_users = User::where('supervisor_id', '=', auth()->user()->id)->get();
        if ($supervised_users->count()>0){$supervision_user = true;}
        // Fetch records
        $suppliersQuery = Supplier::Query();
        $suppliersQuery->leftJoin('users', 'users.id', '=', 'suppliers.created_by');
        $suppliersQuery->leftJoin('users as recruiter', 'recruiter.id', '=', 'suppliers.recruiter_id');
        $suppliersQuery->leftJoin('schemes', 'schemes.id', '=', 'suppliers.scheme_id');
        $suppliersQuery->leftJoin('supplier_groups', 'supplier_groups.id', '=', 'suppliers.supplier_group_id');


        if(auth()->user()->hasRole(User::ROLE_SCHEDULER)) {
            $suppliersQuery->where(function($query) {
                $query->where('suppliers.created_by', auth()->user()->id)
                    ->orWhere('suppliers.recruiter_id', auth()->user()->id);
            });
        }else if(auth()->user()->hasRole(User::ROLE_COORDINATOR) && $supervision_user){
            $suppliersQuery->whereIn('suppliers.recruiter_id', $supervised_users->pluck('id'));
        }

        foreach ($searchColumns as $column => $value ){

            $db_column = str_replace('-', '.',$column);
            $setColumnAddress =false;
            if($db_column == 'suppliers.validate_address'){
                $setColumnAddress = true;
               if (strtolower($value) == 'si'){$suppliersQuery->whereNotNull('validate_address');$value='';}
               if (strtolower($value) == 'no'){$suppliersQuery->whereNull('validate_address');$value='';}
            }

            if($db_column == 'suppliers.is_intervened'){
                if(strtolower($value) == 'no intervenido'){ $value = 0; }
                if(strtolower($value) == 'intervenido'){ $value = 1; }
            }

            if ($db_column == 'suppliers.comorbidity') {
                if (strtolower($value) == 'si') {
                    $value = 1;
                }
                if (strtolower($value) == 'no') {
                    $value = 0;
                }
            }

            if(in_array($db_column, $datetime_columns)){
                $db_column = DB::raw("DATE_FORMAT(".$db_column.",'%d/%m/%Y %H:%i')");
            }

            if ($setColumnAddress == false && $db_column != 'suppliers.wms_age' )// TODO: Ver como buscar por una columna generada
            {
                $suppliersQuery->where($db_column, 'like', '%' .$value . '%');
            }
        }


        $suppliersQuery->select('suppliers.*');
//        Log::debug($$suppliersQuery);
        $suppliersQuery->orderBy($columnName,$columnSortOrder);

        $totalRecordswithFilter = $suppliersQuery->get()->count();
        if($rowperpage != -1){
            $suppliersQuery->skip($start);
            $suppliersQuery->take($rowperpage);
        }
        $suppliers = $suppliersQuery->get();

        $data_arr = array();
        $sno = $start+1;


        foreach($suppliers as $supplier){
            $isValidate = '';
            if ($supplier->validate_address){
                $isValidate = 'SI';
            }else{
                $isValidate = 'NO';
            }

            $buttonEdit = '<a class="btn btn-xs btn-success mr-1" title="'.trans('global.edit').'" href="'.route('scheduler.suppliers.edit', $supplier->id).'"><i class="fas fa-pen"></i></a>';

            $buttonEye ='<a class="btn btn-xs btn-primary" title="'.trans('global.show').'" href=" '.route('scheduler.suppliers.show', $supplier->id).'"><i class="fas fa-eye"></i></a>';
            $buttonIntervene = '';
            if((auth()->user()->hasRole(User::ROLE_DOCTOR) || auth()->user()->hasRole(User::ROLE_DOCTOR_ADMIN)) && $supplier->status == Supplier::STATUS_ONE){

                if($supplier->is_intervened == true){
                    $buttonIntervene = '<button type="button" class="btn btn-xs btn-success" onclick="disableSupplierIntervention(' . $supplier->id . ')" >Desintervenir </button> ';
                }else{
                    $buttonIntervene = '<button type="button" class="btn btn-xs btn-danger" onclick="enableSupplierIntervention(' . $supplier->id . ')" >Intervenir</button> ';
                }
            }
            $deleteButton = '';
            if(count($supplier->appointments) == 0 && auth()->user()->hasRole(User::ROLE_ADMIN)){
                $deleteButton = '<button type="button"  class="btn btn-xs btn-danger" onclick="$(\'#delete_form\').attr(\'action\', \' ' . route('scheduler.suppliers.destroy', $supplier->id) . '\' );$(\'.delete-confirm-submit\').modal(\'show\')" title=" ' . trans('global.delete') . ' "><i class=\'fas fa-trash\'></i> </button> ';
            }


            $status_text = $supplier->is_intervened == 1 ? 'Intervenido' : 'NO Intervenido';

            $status = '<span class="ai-status supplier-status-'. $supplier->is_intervened.'">'.$status_text.'</span>';

            $actionButton = $buttonIntervene.$buttonEdit.$deleteButton.$buttonEye;

            if ($supplier->is_intervened && !auth()->user()->hasRole(User::ROLE_DOCTOR_ADMIN) && !auth()->user()->hasRole(User::ROLE_DOCTOR)){
                $actionButton = '';
            }

            $intervened_class = '';
            if( !auth()->user()->hasRole(User::ROLE_DOCTOR)&& $supplier->is_intervened){
                $intervened_class = ' is_intervened';
            }elseif (auth()->user()->hasRole(User::ROLE_DOCTOR)&& $supplier->is_intervened){
                $intervened_class = ' is_intervened';

            }
            $status_outside = '';
            $button_style_outside = '';
            $statusOutside ='';
            if($supplier->status == Supplier::STATUS_TWO)
            {
                $status_outside = 'status-outside';
                $statusOutside = '<span class="ai-status supplier-status-2">'.$supplier->status.'</span>';
            }else{
                $statusOutside = '<span class="ai-status supplier-status-0">'.$supplier->status.'</span>';
            }


            $data_arr[] = array(
                "any" => '',
                "suppliers-wms_id"     => $supplier->wms_id ?? '',
                "suppliers-wms_name"   => $supplier->wms_name ?? '',
                "suppliers-wms_date"   => $supplier->wms_date != '' ? Carbon::parse($supplier->getOriginal('wms_date'))->format('d/m/Y') : '',
                "suppliers-wms_age"    => $supplier->wms_date != '' ? Carbon::parse($supplier->getOriginal('wms_date'))->age : '',
                "suppliers-wms_gender" => $supplier->wms_gender ?? '',
                "suppliers-email"      => $supplier->email ?? '',
                "suppliers-address"    => $supplier->address ?? '',
                "suppliers-aux5"       => $supplier->aux5 ?? '',
                "suppliers-aux4"       => $supplier->aux4 ?? '',
                "suppliers-phone"      => $supplier->phone ?? '',
                "suppliers-contact"    => $supplier->contact ?? '',
                "suppliers-aux1"       => $supplier->aux1 ?? '',
                "suppliers-aux2"       => $supplier->aux2 ?? '',
                "suppliers-aux3"       => $supplier->aux3 ?? '',
                "suppliers-created_at" => $supplier->created_at ?? '',
                "suppliers-id"         => $supplier->id ?? '',
                "suppliers-comorbidity"=> $supplier->comorbidity == 1 ? 'SI' : 'NO',
                "suppliers-validate_address" => $isValidate,
                "users-name" => Arr::exists($usersNames,$supplier->created_by) ? $usersNames[$supplier->created_by] : '',
                "recruiter-name" => Arr::exists($usersNames,$supplier->recruiter_id) ?  $usersNames[$supplier->recruiter_id] : '',
                "schemes-name" => $schemeNames[$supplier->scheme_id] ?? '',
                "supplier_groups-name" => $supplier->supplierGroup->name ?? '',
                "suppliers-is_intervened"=>$status,
                "is_intervened_text" =>$status_text,
                "suppliers-status"   => $statusOutside,
                "anyTwo"             => $actionButton,
                "is_intervened" => $intervened_class,
                "status_outside" => $status_outside,
                "supplier_status_text" => $supplier->status,
                "edit_data" => "<b>VOLUNTARIO:</b> ".$supplier->wms_name." | <b>DNI:</b> ".$supplier->wms_id." | <b>EMAIL:</b> ".$supplier->email." | <b>TEL.:</b> ".$supplier->phone
            );
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );
        return $response;
    }

    public function getWorkflowDataTables(Request $request)
    {
        $usersNames = User::all()->mapWithKeys(function ($user){return [$user->id=>$user->name];});
        $supplierGroup = SupplierGroup::all()->mapWithKeys(function ($supplierGroup){return [$supplierGroup->id=>$supplierGroup->name];});
        $datetime_columns = ['suppliers.created_at'];
        ## Read value
        $draw            = $request->get('draw');
        $start           = $request->get("start");
        $rowperpage      = $request->get("length");
        $columnName_arr  = $request->get('columns');

        $searchColumns = [];
        $queryColumns = ['suppliers-wms_id', 'suppliers-wms_name','recruiter-name', 'supplier_groups-name'];

        if ( isset( $columnName_arr ) ) {
            for ( $i=0, $ien=count($columnName_arr) ; $i<$ien ; $i++ ) {
                $requestColumn = $request['columns'][$i];
                if(!$requestColumn['search']['value'] == NULL){
                    $searchColumns [$requestColumn['data']] = $requestColumn['search']['value'];
                }
            }
        }

        /*User Role Filter*/
        $supervision_user = false;
        $supervised_users = User::where('supervisor_id', '=', auth()->user()->id)->get();
        if ($supervised_users->count()>0){$supervision_user = true;}
        $suppliersQuery = Supplier::Query();
        $suppliersQuery->leftJoin('users', 'users.id', '=', 'suppliers.created_by');
        $suppliersQuery->leftJoin('users as recruiter', 'recruiter.id', '=', 'suppliers.recruiter_id');
        $suppliersQuery->leftJoin('supplier_groups', 'supplier_groups.id', '=', 'suppliers.supplier_group_id');
        $suppliersQuery->where('suppliers.status','!=',Supplier::STATUS_TWO);

        if(auth()->user()->hasRole(User::ROLE_SCHEDULER)) {
            $suppliersQuery->where(function($query) {
                $query->where('suppliers.created_by', auth()->user()->id)
                    ->orWhere('suppliers.recruiter_id', auth()->user()->id);
            });
        }else if(auth()->user()->hasRole(User::ROLE_COORDINATOR) && $supervision_user){
            $suppliersQuery->whereIn('suppliers.recruiter_id', $supervised_users->pluck('id'));
        }
        /*End User Role Filter*/

        /*Query Filter*/
        foreach ($searchColumns as $column => $value ){
            if (in_array($column,$queryColumns))
            {
                $db_column = str_replace('-', '.',$column);
                if($db_column == 'suppliers.is_intervened'){
                    if(strtolower($value) == 'no intervenido'){ $value = 0; }
                    if(strtolower($value) == 'intervenido'){ $value = 1; }
                }
                if(in_array($db_column, $datetime_columns)){
                    $db_column = DB::raw("DATE_FORMAT(".$db_column.",'%d/%m/%Y %H:%i')");
                }
//                Log::debug($db_column);
                $suppliersQuery->where($db_column, 'like', '%' .$value . '%');

            }
        }
        /*End Query filter*/

        /*Exclude ids Location */

        /*Add Appointment list. Appointment separator ;. Appointment data separator | */
        $suppliersQuery->addSelect(['last_appointment' => function ($query)  {
            $query->select(DB::raw("GROUP_CONCAT(CONCAT_WS('|', IFNULL(appointments.id, ' '), IFNULL(appointment_actions.name, ' '), IFNULL(locations.name,' '), IFNULL(appointments.next_step, ' '), IFNULL(appointments.start_date, ' '), IFNULL(locations.id, ' '), IFNULL(appointment_actions.id, ' '),IFNULL(appointments.date_range_status, ' '), IFNULL(locations.sequence_id, ' ')) SEPARATOR ';')"))
                ->from('appointments')
                ->leftJoin('appointment_actions', 'appointment_actions.id', '=', 'appointments.action_id' )
                ->leftJoin('docks', 'docks.id', '=', 'appointments.dock_id' )
                ->leftJoin('locations', 'locations.id', '=', 'docks.location_id' )
                ->whereColumn('supplier_id', 'suppliers.id')
                ->where('appointments.action_id', "!=", 4 )
                ->where('appointments.deleted_at', null)


                ->where(function($appointmentSubQuery){
                    $appointmentSubQuery->whereRaw('`suppliers`.`scheme_id` IN (select scheme_id from location_scheme where location_id = locations.id)')
                        ->orWhere(function($appointmentSubQuery2){
                            $appointmentSubQuery2->whereNull('suppliers.scheme_id')
                                ->whereRaw(config('app.default_scheme').' IN (select scheme_id from location_scheme where location_id = locations.id)');
                        });
                })

                ->orderBy('appointments.created_at', 'DESC');
        }]);

        /**/
        $suppliersQuery->orderBy('suppliers.wms_name', 'ASC');
        $suppliers = [];
        DB::transaction(function () use ($suppliersQuery, &$suppliers) {
            //El limite del group_concat es 1024
            DB::statement('SET GLOBAL group_concat_max_len = 1000000');
            $suppliers = $suppliersQuery->get();
        });

        $data_arr = [];
        $data_arr[self::STATUS_FILTER_EXPIRED] = [];
        $data_arr[self::STATUS_FILTER_IN_RANGE] = [];
        $data_arr[self::STATUS_FILTER_TO_EXPIRED] = [];
        $data_arr[self::STATUS_FILTER_PENDING] = [];
        $data_arr[self::STATUS_FILTER_COMPLETE_IN_RANGE] = [];
        $data_arr[self::STATUS_FILTER_COMPLETE_OUT_RANGE] = [];
        $data_arr[self::STATUS_FILTER_STATELESS] = [];

        $locations =  Location::all();
        $sequencesShow = Sequence::all();
        foreach($suppliers as $supplier){
            //Inicializamos la variable de cambio de estado.
            $statusDateValidation = self::STATUS_FILTER_STATELESS;

            if($supplier->is_intervened == 1){
                $interventionStatusText = 'Intervenido';
                $intervened_class = ' is_intervened';
            }else{
                $interventionStatusText = 'NO Intervenido';
                $intervened_class = ' ';
            }
            $interventionStatus = '<span class="ai-status supplier-status-'. $supplier->is_intervened.'">'.$interventionStatusText.'</span>';

            if($supplier->status == Supplier::STATUS_TWO){
                $supplier_status_class = 'status-outside';
                $supplierStatus = '<span class="ai-status supplier-status-2">'.$supplier->status.'</span>';
            }else{
                $supplier_status_class = '';
                $supplierStatus = '<span class="ai-status supplier-status-0">'.$supplier->status.'</span>';
            }


            $appointment_id = $appointment_status = $appointment_current_visit = $appointment_next_step = $appointment_next_step_text = $appointment_start_date = $next_location_data = $supplier_link = $appointment_link = $range = $formatted_appointment_date = $appointment_current_visit_link = $appointment_status_id ='';
            $dataBySequence = [];

            if(!empty($supplier->last_appointment)) {

                $allSuppliersAppointments = explode(';', $supplier->last_appointment);

                $lastAppointmentsBySequence = [];
                $supplierAppointmentsDatesByLocation = [];
                foreach ($allSuppliersAppointments as $supplierAppointment){
                    $supplierAppointmentData = explode('|', $supplierAppointment);
                    if(!empty($supplierAppointmentData[5]) && !empty( $supplierAppointmentData[4])) {
                        $supplierAppointmentsDatesByLocation[$supplierAppointmentData[5]] = $supplierAppointmentData[4];
                    }
                    if(!empty($supplierAppointmentData[8])){
                        $lastAppointmentsBySequence[$supplierAppointmentData[8]] = $supplierAppointmentData;
                    }else{
                        $lastAppointmentsBySequence[0] = $supplierAppointmentData;
                    }
                }


                foreach ($lastAppointmentsBySequence as $sequence=>$lastAppointmentData) {
                    $appointment_id = $lastAppointmentData[0];
                    $appointment_status = $lastAppointmentData[1];
                    $appointment_current_visit = $lastAppointmentData[2];
                    $appointment_next_step = $lastAppointmentData[3];
                    $appointment_start_date = $lastAppointmentData[4];
                    $appointment_current_location_id = $lastAppointmentData[5];
                    $appointment_status_id = $lastAppointmentData[6];
                    $appointment_date_range_status = $lastAppointmentData[7];

                    $appointment_date = null;
                    if (!empty($appointment_start_date)) {
                        $appointment_date = Carbon::parse($appointment_start_date);
                        $formatted_appointment_date = $appointment_date->format(config('app.date_format'));
                    }

                    //LOGICA PARA EN RANGO O FUERA DE RANGO: Con respecto al turno actual dado
                    if ($appointment_status_id == Appointment::STATUS_CONFIRM) {
                        //Asignamos el valor a la variable de cambio de estado segun el rango dado
                        if ($appointment_date_range_status == Appointment::IN_RANGE) {
                            $statusDateValidation = self::STATUS_FILTER_COMPLETE_IN_RANGE;
                        }
                        if ($appointment_date_range_status == Appointment::OUT_RANGE) {
                            $statusDateValidation = self::STATUS_FILTER_COMPLETE_OUT_RANGE;
                        }
                    }

                    if ($appointment_next_step == AppointmentService::ASSIGN_NEXT && $appointment_status_id == Appointment::STATUS_ACCOMPLISH) {

                        $next_location = $locations->where('prev_location_id_workflow', $appointment_current_location_id)->first();

                        if ($next_location != null) {

                            $appointment_next_step = "Asignar " . $next_location->name;
                            if (!empty($supplierAppointmentsDatesByLocation[$next_location->prev_location_id])) {

                                $appointmentLocationPrevDate = $supplierAppointmentsDatesByLocation[$next_location->prev_location_id];
                                if (!empty($appointmentLocationPrevDate)) {

                                    $dateFromPrevLocationParse = Carbon::parse($appointmentLocationPrevDate);
                                    $range = '';
                                    if (!empty($dateFromPrevLocationParse)) {

                                        $date_from = $date_to = '';
                                        $appointment_date_1 = $dateFromPrevLocationParse->clone();
                                        $appointment_date_2 = $dateFromPrevLocationParse->clone();

                                        if (!empty($next_location->prev_days_from)) {
                                            $date_from = $appointment_date_1->addDays($next_location->prev_days_from)->format(config('app.date_format'));
                                        }
                                        if (!empty($next_location->prev_days_to)) {
                                            $date_to = $appointment_date_2->addDays($next_location->prev_days_to)->format(config('app.date_format'));
                                        }
                                        $range = $date_from . ' al ' . $date_to;
                                    }
                                }
                            }



                            $statusPending1 = Carbon::createFromFormat('d/m/Y', $date_from);
                            $dateRangeUpper = Carbon::createFromFormat('d/m/Y', $date_to);
                            $statusPending2 = Carbon::parse($statusPending1)->subDays(7);
//                            $rangeStatus2 = false;

                            if (now() < $statusPending2) {
                                $statusDateValidation = self::STATUS_FILTER_PENDING;

                            }
                            if (now() >= $statusPending2 && now() <= $statusPending1) {
//                                $rangeStatus2 = true;
                                $statusDateValidation = self::STATUS_FILTER_TO_EXPIRED;

                            }
                            //En Rango: Si no se realizó la próxima acción y la fecha actual es mayor al rango inferior y menor/igual al rango superior
                            if (now() > $statusPending1 && now() <= $dateRangeUpper ){

                                $statusDateValidation = self::STATUS_FILTER_IN_RANGE;
                            }
                            //Vencida: Si no se realizó la próxima acción y la fecha actual es mayor al rango superior
                            if (now() > $dateRangeUpper ) {

                                $statusDateValidation = self::STATUS_FILTER_EXPIRED;
                            }

                            $add_button = '';

                            if(auth()->user()->hasRole(User::ROLE_DOCTOR)) {
                                if ($supplier->status != Supplier::STATUS_TWO && $supplier->created_by == auth()->user()->id) {
                                    $add_button = ' <a class="btn btn-xs btn-success" href=" ' . route('scheduler.appointments.create', $next_location->id) . '" target="_blank">
                                                    <i class="fas fa-plus-square"></i>
                                                </a> ';
                                }
                            }else{
                                if ($supplier->status != Supplier::STATUS_TWO && $supplier->is_intervened == 0) {
                                    $add_button = ' <a class="btn btn-xs btn-success" href=" ' . route('scheduler.appointments.create', $next_location->id) . '" target="_blank">
                                                    <i class="fas fa-plus-square"></i>
                                                </a> ';
                                }
                            }
                            $appointment_next_step_text = $appointment_next_step;
                            $appointment_next_step = $add_button . $appointment_next_step;
                        }


                    }

                    $appointment_current_visit_link = '<a class="table-link" title=" ' . trans('global.view') . '" href=" ' . route('scheduler.appointments.show', $appointment_id) . '" target="_blank">
                                        <i class="fas fa-external-link-square-alt"></i>
                                    </a> ' . $appointment_current_visit;

                    $dataBySequence[$sequence]=[
                        'appointment_current_visit_link'=>$appointment_current_visit_link,
                        'appointment_next_step'=>$appointment_next_step,
                        'appointment_next_step_text'=>$appointment_next_step_text,
                        'statusDateValidation'=>$statusDateValidation,
                        'appointment_id'=> $appointment_id,
                        'range'=>$range,
                        'appointment_status' => $appointment_status,
                        'appointment_current_visit' => $appointment_current_visit,
                        'appointment_status_id' => $appointment_status_id,
                        'appointment_date' => $formatted_appointment_date,

                    ];
                }
            }


            $supplier_link = ' <a class="table-link" href=" ' . route('scheduler.suppliers.show', $supplier->id) . '" target="_blank">
                                <i class="fas fa-external-link-square-alt"></i>
                                </a>';
            $appointment_supplier_and_history_link = $supplier_link . ' <a class="table-link" href=" ' . route('scheduler.supplier.timeline', $supplier->id) . '" target="_blank"><i class="fas fa-history"></i></a> '.$supplier->wms_name;

            foreach ($dataBySequence as $key => $workflowDataBySequence) {
                $sequenceShowWorkflow = $sequencesShow->where('id',$key)->first();

                if($key > 0) { //Estos son los locations sin secuencia. Por ahora no se muestran
                    if (!empty($sequenceShowWorkflow->show_in_workflow) > 0) {

                        //Chequear si cumple con los 4 filtros
                        $validFilters = 0;
                        $requestFilters = 0;
                        if (!empty($searchColumns['appointment_current_visit_link'])){
                            $requestFilters ++;
                            if (Str::contains(Str::lower($workflowDataBySequence['appointment_current_visit_link']),Str::lower($searchColumns['appointment_current_visit_link']))){
                                $validFilters ++;
                            }
                        }
                        if (!empty($searchColumns['appointment_status'])){
                            $requestFilters ++;
                            if (Str::contains(Str::lower($workflowDataBySequence['appointment_status']),Str::lower($searchColumns['appointment_status']))){
                                $validFilters ++;
                            }
                        }
                        if (!empty($searchColumns['appointment_next_step'])){
                            $requestFilters ++;
                            if (Str::contains(Str::lower($workflowDataBySequence['appointment_next_step']),Str::lower($searchColumns['appointment_next_step']))){
                                $validFilters ++;
                            }
                        }
                        if (!empty($searchColumns['status_validation'])){
                            $requestFilters ++;
                            if (Str::contains(Str::lower($workflowDataBySequence['statusDateValidation']),Str::lower($searchColumns['status_validation']))){
                                $validFilters ++;
                            }
                        }


                        if (!empty($searchColumns['appointment_date'])){
                            $requestFilters ++;
                            if (Str::contains(Str::lower($workflowDataBySequence['appointment_date']),Str::lower($searchColumns['appointment_date']))){
                                $validFilters ++;
                            }
                        }

                        if ($requestFilters == $validFilters) {
                            $data_arr[$workflowDataBySequence['statusDateValidation']][] = array(
                                "any" => '',
                                "suppliers-wms_id" => $supplier->wms_id ?? '',
                                "suppliers-wms_name" => $appointment_supplier_and_history_link,
                                "suppliers-id" => $supplier->id ?? '',
                                "recruiter-name" => Arr::exists($usersNames , $supplier->recruiter_id) ? $usersNames[$supplier->recruiter_id] : '',
                                "supplier_groups-name" =>Arr::exists($supplierGroup , $supplier->supplier_group_id) ? $supplierGroup[$supplier->supplier_group_id] : '',
                                "last_appointment" => $supplier->last_appointment,
                                "appointment_id" => $workflowDataBySequence['appointment_id'],
                                "appointment_status" => $workflowDataBySequence['appointment_status'],
                                "appointment_current_visit_link" => $workflowDataBySequence['appointment_current_visit_link'],
                                "appointment_next_step" => $workflowDataBySequence['appointment_status_id'] == Appointment::STATUS_ACCOMPLISH ? $workflowDataBySequence['appointment_next_step'] : '',
                                "appointment_next_step_text" => $workflowDataBySequence['appointment_status_id'] == Appointment::STATUS_ACCOMPLISH ? $workflowDataBySequence['appointment_next_step_text'] : '',
                                "appointment_date" => $workflowDataBySequence['appointment_date'],
                                "appointment_next_step_range" => $workflowDataBySequence['range'],
                                "suppliers-is_intervened" => $interventionStatus,
                                "is_intervened_text" => $interventionStatusText,
                                "suppliers-status" => $supplierStatus,
                                "is_intervened" => $intervened_class,
                                "supplier_status_class" => $supplier_status_class,
                                "supplier_textname" => $supplier->wms_name ?? '',
                                "appointment_current_visit_text" => $workflowDataBySequence['appointment_current_visit'],
                                "supplier_textstatus" => $supplier->status ?? '',
                                "status_validation" => $workflowDataBySequence['statusDateValidation'] == self::STATUS_FILTER_STATELESS ? '' : '<span class="data-status-validation">' . $workflowDataBySequence['statusDateValidation'] . '</span>',
                                "status_validation_text" => $workflowDataBySequence['statusDateValidation'] == self::STATUS_FILTER_STATELESS ? '' : $workflowDataBySequence['statusDateValidation']
                            );
                        }
                    }
                }
            }

        }

        $array1 = $data_arr[self::STATUS_FILTER_EXPIRED];
        $array2 = $data_arr[self::STATUS_FILTER_TO_EXPIRED];
        $array3 = $data_arr[self::STATUS_FILTER_PENDING];
        $array4 = $data_arr[self::STATUS_FILTER_COMPLETE_IN_RANGE];
        $array5 = $data_arr[self::STATUS_FILTER_COMPLETE_OUT_RANGE];
        $array6 = $data_arr[self::STATUS_FILTER_STATELESS];
        $array7 = $data_arr[self::STATUS_FILTER_IN_RANGE];

        $all_data = array_merge($array1,$array2,$array3,$array4,$array5,$array6,$array7);
        $page_data = $all_data;
        if($rowperpage != -1){
            $page_data = array_slice($all_data, $start, $rowperpage);
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => count($all_data),
            "iTotalDisplayRecords" => count($all_data),
            "aaData" =>$page_data
        );
        return $response;
    }

    public function getCPs(){
        return[
            '0 - CAPITAL FEDERAL',
            '1657 - 11 DE SEPTIEMBRE',
            '2701 - 12 DE AGOSTO',
            '6501 - 12 DE OCTUBRE',
            '7313 - 16 DE JULIO',
            '8129 - 17 DE AGOSTO',
            '1761 - 20 DE JUNIO',
            '6660 - 25 DE MAYO',
            '6405 - 30 DE AGOSTO',
            '2707 - 4 DE NOVIEMBRE',
            '1776 - 9 DE ABRIL',
            '6500 - 9 DE JULIO',
            '1903 - ABASTO',
            '7228 - ABBOTT',
            '6450 - ABEL',
            '8170 - ABRA DE HINOJO',
            '1640 - ACASSUSO',
            '7003 - ACEILAN',
            '2717 - ACEVEDO',
            '6627 - ACHUPALLAS',
            '7136 - ADELA',
            '8000 - ADELA CORTI',
            '8129 - ADELA SAENZ',
            '6430 - ADOLFO ALSINA',
            '7513 - ADOLFO GONZALES CHAVES',
            '1846 - ADROGUE',
            '1802 - AEROPUERTO EZEIZA',
            '6608 - AGOTE',
            '8105 - AGUARA',
            '2701 - AGUAS CORRIENTES',
            '7112 - AGUAS VERDES',
            '2915 - AGUIRREZABALA',
            '6667 - AGUSTIN MOSCONI',
            '6001 - AGUSTIN ROCA',
            '6001 - AGUSTINA',
            '6463 - ALAGON',
            '6437 - ALAMOS',
            '6703 - ALASTUEY',
            '6405 - ALBARI',
            '6034 - ALBERDI',
            '6634 - ALBERTI',
            '8126 - ALDEA SAN ANDRES',
            '7406 - ALDECON',
            '1770 - ALDO BONZI',
            '1987 - ALEGRE',
            '1864 - ALEJANDRO KORN',
            '1808 - ALEJANDRO PETION',
            '6437 - ALFA',
            '6555 - ALFALAD',
            '8117 - ALFEREZ SAN MARTIN',
            '6533 - ALFREDO DEMARCHI',
            '6531 - ALGARROBO',
            '2935 - ALGARROBO',
            '8136 - ALGARROBO',
            '2751 - ALMACEN CASTRO',
            '2751 - ALMACEN EL CRUCE',
            '2752 - ALMACEN EL DESCANSO',
            '2740 - ALMACEN LA COLINA',
            '2711 - ALMACEN PIATTI',
            '1629 - ALMIRANTE IRIZAR',
            '8109 - ALMIRANTE SOLIER',
            '2938 - ALSINA',
            '8170 - ALTA VISTA',
            '6601 - ALTAMIRA',
            '1986 - ALTAMIRANO',
            '2814 - ALTO LOS CARDALES',
            '2801 - ALTO VERDE',
            '7303 - ALTONA',
            '7267 - ALVAREZ DE TOLEDO',
            '1921 - ALVAREZ JONTE',
            '7403 - ALVARO BARROS',
            '7021 - ALZAGA',
            '6516 - AMALIA',
            '8508 - AMBROSIO P LEZICA',
            '6237 - AMERICA',
            '1862 - AMERICA UNIDA',
            '6607 - ANASAGASTI',
            '6451 - ANCON',
            '6555 - ANDANT',
            '6621 - ANDERSON',
            '6634 - ANDRES VACCAREZZA',
            '7011 - ANEQUE GRANDE',
            '7243 - ANTONIO CARBONI',
            '7305 - ANTONIO DE LOS HEROS',
            '8158 - APARICIO',
            '1909 - ARANA',
            '6443 - ARANO',
            '6643 - ARAUJO',
            '6557 - ARBOLEDA',
            '1915 - ARBUCO',
            '6005 - ARENALES',
            '6075 - ARENAZA',
            '7243 - AREVALO',
            '8134 - ARGERICH',
            '7301 - ARIEL',
            '8164 - ARQUEDAS',
            '2740 - ARRECIFES',
            '6007 - ARRIBE',
            '2805 - ARROYO ',
            '2800 - ARROYO ',
            '8174 - ARROYO AGUAS BLANCAS',
            '2800 - ARROYO AGUILA NEGRA',
            '2805 - ARROYO ALELI',
            '2800 - ARROYO BOTIJA FALSA',
            '2935 - ARROYO BURGOS',
            '1625 - ARROYO CANELON',
            '2805 - ARROYO CARABELITAS',
            '7011 - ARROYO CHICO',
            '8172 - ARROYO CORTO',
            '2813 - ARROYO DE LA CRUZ',
            '2752 - ARROYO DE LUNA',
            '1923 - ARROYO DEL PESCADO',
            '2743 - ARROYO DULCE',
            '2805 - ARROYO EL AHOGADO',
            '6437 - ARROYO EL CHINGOLO',
            '7174 - ARROYO GRANDE',
            '1923 - ARROYO LA MAZA',
            '2805 - ARROYO LAS CRUCES',
            '2805 - ARROYO LAS ROSAS',
            '1625 - ARROYO LAS ROSAS',
            '7301 - ARROYO LOS HUESOS',
            '2805 - ARROYO LOS TIGRES',
            '2800 - ARROYO NEGRO',
            '8111 - ARROYO PAREJA',
            '2805 - ARROYO PESQUERIA',
            '2805 - ARROYO TAJIBER',
            '6437 - ARROYO VENADO',
            '2805 - ARROYO ZANJON',
            '1895 - ARTURO SEGUI',
            '6433 - ARTURO VATTEONE',
            '6640 - ASAMBLEA',
            '6003 - ASCENCION',
            '6469 - ASTURIAS',
            '6471 - ATAHUALPA',
            '1913 - ATALAYA',
            '2808 - ATUCHA',
            '1870 - AVELLANEDA',
            '8183 - AVESTRUZ',
            '7150 - AYACUCHO',
            '6721 - AZCUENAGA',
            '8181 - AZOPARDO',
            '7300 - AZUL',
            '1727 - B LOS AROMOS SAN PATRICIO',
            '1727 - B NUESTRA SE',
            '1727 - B SARMIENTO DON ROLANDO',
            '1727 - B STA CATALINA HORNERO LA LOMA',
            '6516 - BACACAY',
            '6403 - BADANO',
            '8000 - BAHIA BLANCA',
            '8506 - BAHIA SAN BLAS',
            '6013 - BAIGORRITA',
            '8115 - BAJO HONDO',
            '7620 - BALCARCE',
            '7607 - BALNEARIO ATLANTIDA',
            '7607 - BALNEARIO CAMET NORTE',
            '8132 - BALNEARIO CHAPALCO',
            '7505 - BALNEARIO CLAROMECO',
            '7607 - BALNEARIO FRENTE MAR',
            '7607 - BALNEARIO LA BALIZA',
            '7609 - BALNEARIO LA CALETA',
            '7641 - BALNEARIO LOS ANGELES',
            '7609 - BALNEARIO MAR CHIQUITA',
            '7609 - BALNEARIO MAR DE COBO',
            '7511 - BALNEARIO OCEANO',
            '7511 - BALNEARIO ORENSE',
            '8153 - BALNEARIO ORIENTE',
            '8109 - BALNEARIO PARADA',
            '7607 - BALNEARIO PLAYA DORADA',
            '8132 - BALNEARIO SAN ANTONIO',
            '7609 - BALNEARIO SANTA ELENA',
            '8153 - BALNEARIO SAUCE GRANDE',
            '6070 - BALSA',
            '6244 - BANDERALO',
            '1828 - BANFIELD',
            '2942 - BARADERO',
            '7005 - BARKER',
            '7247 - BARRIENTOS',
            '1814 - BARRIO 1 DE MAYO',
            '7601 - BARRIO BATAN',
            '6000 - BARRIO CAROSIO',
            '7605 - BARRIO CHAPADMALAL',
            '1625 - BARRIO EL CAZADOR',
            '1915 - BARRIO EL PORTE',
            '7600 - BARRIO EMIR RAMON JUAREZ',
            '1623 - BARRIO GARIN NORTE',
            '7600 - BARRIO GASTRONOMICO',
            '6000 - BARRIO GENERAL SAN MARTIN',
            '6400 - BARRIO INDIO TROMPA',
            '6500 - BARRIO JULIO DE VEDIA',
            '1980 - BARRIO LA DOLLY',
            '7400 - BARRIO LA LUISA',
            '1980 - BARRIO LAS MANDARINAS',
            '6450 - BARRIO OBRERO',
            '7607 - BARRIO OESTE',
            '7607 - BARRIO PARQUE BRISTOL',
            '1623 - BARRIO PARQUE LAMBARE',
            '1713 - BARRIO PARQUE LELOIR',
            '7109 - BARRIO PEDRO ROCCO',
            '7600 - BARRIO PUEBLO NUEVO',
            '1629 - BARRIO SAN ALEJO',
            '2800 - BARRIO SAN JACINTO',
            '1862 - BARRIO SAN PABLO',
            '1862 - BARRIO SANTA MAGDALENA',
            '7600 - BARRIO TIERRA DE ORO',
            '7600 - BARRIO TIRO FEDERAL',
            '2700 - BARRIO TROCHA',
            '6000 - BARRIO VILLA ORTEGA',
            '7260 - BARRIO VILLA SALADILLO',
            '8107 - BASE AERONAVAL CMTE ESPORA',
            '1919 - BASE AERONAVAL PUNTA INDIO',
            '7301 - BASE NAVAL AZOPARDO',
            '1929 - BASE NAVAL RIO SANTIAGO',
            '8113 - BATERIAS',
            '7540 - BATHURST ESTACION',
            '6643 - BAUDRIX',
            '6078 - BAYAUCA',
            '1643 - BECCAR',
            '1625 - BELEN DE ESCOBAR',
            '1661 - BELLA VISTA',
            '6535 - BELLOCQ',
            '1621 - BENAVIDEZ',
            '6632 - BENITEZ',
            '7020 - BENITO JUAREZ',
            '1884 - BERAZATEGUI',
            '2743 - BERDIER',
            '1923 - BERISSO',
            '6071 - BERMUDEZ',
            '1876 - BERNAL ESTE',
            '1876 - BERNAL OESTE',
            '7313 - BERNARDO VERA Y PINTADO',
            '8124 - BERRAONDO',
            '6424 - BERUTI',
            '6561 - BLANCA GRANDE',
            '6032 - BLANDENGUES',
            '6065 - BLAQUIER',
            '6661 - BLAS DURA',
            '2805 - BLONDEAU',
            '1911 - BME BAVIO GRAL MANSILLA',
            '6348 - BOCAYUBA',
            '6550 - BOLIVAR',
            '6439 - BONIFACIO',
            '7223 - BONNEMENT',
            '8187 - BORDENAVE',
            '7620 - BOSCH',
            '1889 - BOSQUES',
            '1609 - BOULOGNE',
            '6640 - BRAGADO',
            '6411 - BRAVO DEL DOS',
            '1903 - BUCHANAN',
            '1852 - BURZACO',
            '8183 - CA',
            '2740 - CA',
            '1814 - CA',
            '6625 - CA',
            '6105 - CA',
            '6700 - CA',
            '8134 - CABEZA DE BUEY',
            '8118 - CABILDO',
            '2703 - CABO SAN FERMIN',
            '7214 - CACHARI',
            '6535 - CADRET',
            '6339 - CAILOMUTA',
            '8101 - CALDERON',
            '7400 - CALERA AVELLANEDA',
            '7613 - CALFUCURA',
            '8154 - CALVO',
            '7116 - CAMARON CHICO',
            '6516 - CAMBACERES',
            '7612 - CAMET',
            '7300 - CAMINERA AZUL',
            '7020 - CAMINERA JUAREZ',
            '6700 - CAMINERA LUJAN',
            '7007 - CAMINERA NAPALEOFU',
            '7130 - CAMINERA SAMBOROMBON',
            '1896 - CAMINO CENTENARIO KM 11500',
            '7613 - CAMPAMENTO',
            '2804 - CAMPANA',
            '6474 - CAMPO ARISTIMU',
            '2700 - CAMPO BUENA VISTA',
            '6015 - CAMPO COLIQUEO',
            '2754 - CAMPO CRISOL',
            '1659 - CAMPO DE MAYO',
            '8185 - CAMPO DEL NORTE AMERICANO',
            '7247 - CAMPO FUNKE',
            '2752 - CAMPO LA ELISA',
            '8150 - CAMPO LA LIMA',
            '2764 - CAMPO LA NENA',
            '7623 - CAMPO LA PLATA',
            '6015 - CAMPO LA TRIBU',
            '8185 - CAMPO LA ZULEMA',
            '7623 - CAMPO LEITE',
            '1980 - CAMPO LOPE SECO',
            '8185 - CAMPO LOS AROMOS',
            '6013 - CAMPO MAIPU',
            '6605 - CAMPO PE',
            '7623 - CAMPO PELAEZ',
            '7303 - CAMPO ROJAS',
            '7245 - CAMPO SABATE',
            '8185 - CAMPO SAN JUAN',
            '7305 - CAMPODONICO',
            '1625 - CAMPOMAR VI',
            '7114 - CANAL 15 CERRO DE LA GLORIA',
            '2800 - CANAL MARTIN IRIGOYEN',
            '2805 - CANAL N ALEM 1A SEC',
            '2805 - CANAL N ALEM 2A SEC',
            '1987 - CANCHA DEL POLLO',
            '7153 - CANGALLO',
            '1804 - CANNING',
            '1804 - CANNING',
            '8185 - CANONIGO GORRITI',
            '7000 - CANTERA AGUIRRE',
            '7000 - CANTERA ALBION',
            '7000 - CANTERA LA AURORA',
            '7000 - CANTERA LA FEDERACION',
            '7000 - CANTERA LA MOVEDIZA',
            '7000 - CANTERA MONTE CRISTO',
            '7000 - CANTERA SAN LUIS',
            '8504 - CANTERA VILLALONGA',
            '7401 - CANTERAS DE GREGORINI',
            '6612 - CAPDEPONT',
            '2812 - CAPILLA DEL SE',
            '6461 - CAPITAN CASTRO',
            '2752 - CAPITAN SARMIENTO',
            '2703 - CARABELAS',
            '1605 - CARAPACHAY',
            '8506 - CARDENAL CAGLIERO',
            '6430 - CARHUE',
            '7119 - CARI LARQUEA',
            '7167 - CARILO',
            '7247 - CARLOS BEGUERIE',
            '6530 - CARLOS CASARES',
            '6701 - CARLOS KEEN',
            '2812 - CARLOS LEMEE',
            '6515 - CARLOS MARIA NAON',
            '6453 - CARLOS SALAS',
            '1812 - CARLOS SPEGAZZINI',
            '6455 - CARLOS TEJEDOR',
            '6725 - CARMEN DE ARECO',
            '8504 - CARMEN DE PATAGONES',
            '7225 - CASALINS',
            '6417 - CASBAS',
            '7547 - CASCADA',
            '1678 - CASEROS',
            '6417 - CASEY',
            '1712 - CASTELAR',
            '7114 - CASTELLI',
            '6616 - CASTILLA',
            '7265 - CAZON',
            '6535 - CENTENARIO',
            '1893 - CENTRO AGRICOLA EL PATO',
            '7114 - CENTRO GUERRERO',
            '6237 - CERRITO',
            '7403 - CERRO AGUILA',
            '7101 - CERRO DE LA GLORIA CANAL 15',
            '7000 - CERRO DE LOS LEONES',
            '7403 - CERRO NEGRO',
            '7403 - CERRO SOTUYO',
            '6740 - CHACABUCO',
            '2700 - CHACRA EXPERIMENTAL INTA',
            '7406 - CHALA QUILCA',
            '6017 - CHANCAY',
            '7203 - CHAPALEUFU',
            '7020 - CHAPAR',
            '6341 - CHAPI TALO',
            '7223 - CHAS',
            '7130 - CHASCOMUS',
            '8117 - CHASICO',
            '2764 - CHENAUT',
            '6476 - CHICLANA',
            '7311 - CHILLAR',
            '6620 - CHIVILCOY',
            '8000 - CHOIQUE',
            '1657 - CHURRUCA',
            '1896 - CITY BELL',
            '1778 - CIUDAD EVITA',
            '1684 - CIUDAD JARDIN DEL PALOMAR',
            '1768 - CIUDAD MADERO',
            '1702 - CIUDADELA',
            '7005 - CLARAZ',
            '7515 - CLAUDIO C MOLINA',
            '7163 - CLAVERIE',
            '1849 - CLAYPOLE',
            '7612 - COBO',
            '8118 - COCHRANE',
            '2752 - COLEGIO SAN PABLO',
            '6743 - COLIQUEO',
            '7201 - COLMAN',
            '2720 - COLON',
            '6034 - COLONIA ALBERDI',
            '8142 - COLONIA BARGA',
            '6441 - COLONIA BARON HIRSCH',
            '1921 - COLONIA BEETHOVEN',
            '8000 - COLONIA BELLA VISTA',
            '8136 - COLONIA CUARENTA Y TRES',
            '7609 - COLONIA DE VAC CHAPADMALAL',
            '8180 - COLONIA DR GDOR UDAONDO',
            '6403 - COLONIA EL BALDE',
            '8142 - COLONIA EL GUANACO',
            '8181 - COLONIA EL PINCEN',
            '7136 - COLONIA ESCUELA ARGENTINA',
            '7172 - COLONIA FERRARI',
            '7318 - COLONIA HINOJO',
            '8181 - COLONIA HIPOLITO YRIGOYEN',
            '1727 - COLONIA HOGAR R GUTIERREZ',
            '6667 - COLONIA INCHAUSTI',
            '6003 - COLONIA LA BEBA',
            '8136 - COLONIA LA CATALINA',
            '8508 - COLONIA LA CELINA',
            '6531 - COLONIA LA ESPERANZA',
            '8185 - COLONIA LA ESTRELLA',
            '8142 - COLONIA LA GRACIELA',
            '2751 - COLONIA LA INVERNADA',
            '8105 - COLONIA LA MERCED',
            '2751 - COLONIA LA NENA',
            '2751 - COLONIA LA NORIA',
            '2751 - COLONIA LA REINA',
            '2711 - COLONIA LA VANGUARDIA',
            '8183 - COLONIA LA VASCONGADA',
            '2751 - COLONIA LABORDEROY',
            '8185 - COLONIA LAPIN',
            '6513 - COLONIA LAS YESCAS',
            '8185 - COLONIA LEVEN',
            '8142 - COLONIA LOS ALAMOS',
            '8132 - COLONIA LOS ALFALFARES',
            '6018 - COLONIA LOS BOSQUES',
            '6007 - COLONIA LOS HORNOS',
            '6018 - COLONIA LOS HUESOS',
            '2751 - COLONIA LOS TOLDOS',
            '2760 - COLONIA LOS TRES USARIS',
            '6531 - COLONIA MAURICIO',
            '8508 - COLONIA MIGUEL ESTEVERENA',
            '8144 - COLONIA MONTE LA PLATA',
            '6341 - COLONIA MURATURE',
            '6708 - COLONIA NAC DE ALIENADOS',
            '1727 - COLONIA NACIONAL DE MENORES',
            '6341 - COLONIA NAVIERA',
            '7318 - COLONIA NIEVES',
            '8134 - COLONIA OCAMPO',
            '6643 - COLONIA PALANTELEN',
            '8185 - COLONIA PHILLIPSON N 1',
            '8144 - COLONIA PUEBLO RUSO',
            '7318 - COLONIA RUSA',
            '6646 - COLONIA SAN EDUARDO',
            '8132 - COLONIA SAN ENRIQUE',
            '8142 - COLONIA SAN FRANCISCO',
            '6017 - COLONIA SAN FRANCISCO',
            '8164 - COLONIA SAN MARTIN',
            '7403 - COLONIA SAN MIGUEL',
            '8164 - COLONIA SAN PEDRO',
            '6437 - COLONIA SAN RAMON',
            '6535 - COLONIA SANTA MARIA',
            '8185 - COLONIA SANTA MARIANA',
            '1816 - COLONIA SANTA ROSA',
            '8181 - COLONIA SANTA ROSA',
            '6459 - COLONIA SERE',
            '2751 - COLONIA STEGMAN',
            '8142 - COLONIA TAPATTA',
            '2933 - COLONIA VELEZ',
            '6628 - COLONIA ZAMBUNGO',
            '6601 - COMAHUE OESTE',
            '7135 - COMANDANTE GIRIBONE',
            '7603 - COMANDANTE NICANOR OTAMENDI',
            '6641 - COMODORO PY',
            '6233 - CONDARCO',
            '7639 - COOPER',
            '7511 - COPETONAS',
            '6465 - CORACEROS',
            '6405 - CORAZZI',
            '6507 - CORBETT',
            '7208 - CORONEL BOERR',
            '1980 - CORONEL BRANDSEN',
            '6223 - CORONEL CHARLONE',
            '8150 - CORONEL DORREGO',
            '8118 - CORONEL FALCON',
            '6062 - CORONEL GRANADA',
            '2747 - CORONEL ISLE',
            '6555 - CORONEL MARCELINO FREYRE',
            '6628 - CORONEL MON',
            '7530 - CORONEL PRINGLES',
            '7313 - CORONEL RODOLFO BUNGE',
            '6628 - CORONEL SEGUI',
            '7540 - CORONEL SUAREZ',
            '7174 - CORONEL VIDAL',
            '8118 - CORTI',
            '6712 - CORTINES',
            '7112 - COSTA AZUL',
            '7631 - COSTA BONITA BALNEARIO',
            '2914 - COSTA BRAVA',
            '7108 - COSTA DEL ESTE',
            '7305 - COVELLO',
            '7503 - CRISTIANO MUERTO',
            '1913 - CRISTINO BENAVIDEZ',
            '7307 - CROTTO',
            '1870 - CRUCESITA',
            '1987 - CUARTEL 2',
            '7136 - CUARTEL 6',
            '7135 - CUARTEL 8',
            '6700 - CUARTEL CUATRO',
            '7300 - CUARTEL II',
            '6032 - CUARTEL IV',
            '7110 - CUARTEL IV',
            '1744 - CUARTEL V',
            '6000 - CUARTEL V',
            '6050 - CUARTEL VII',
            '6746 - CUCHA CUCHA',
            '6723 - CUCULLU',
            '6231 - CUENCA',
            '7548 - CURA MALAL',
            '6451 - CURARU',
            '7541 - D ORBIGNY',
            '6555 - DAIREAUX',
            '1987 - DANTAS',
            '8183 - DARREGUEIRA',
            '6348 - DE BARY',
            '6031 - DE BRUYN',
            '7013 - DE LA CANAL',
            '7515 - DE LA GARMA',
            '7521 - DEFERRARI',
            '7265 - DEL CARRIL',
            '6509 - DEL VALLE',
            '1669 - DEL VISO',
            '8185 - DELFIN HUERGO',
            '6007 - DELGADO',
            '6516 - DENNEHY',
            '7531 - DESPE',
            '1925 - DESTILERIA FISCAL',
            '7000 - DESVIO AGUIRRE',
            '6031 - DESVIO EL CHINGOLO',
            '6509 - DESVIO GARBARINI',
            '6503 - DESVIO KILOMETRO 234',
            '1981 - DESVIO KILOMETRO 55',
            '6007 - DESVIO KILOMETRO 95',
            '8180 - DESVIO SAN ALEJO',
            '8109 - DESVIO SANDRINI',
            '2812 - DIEGO GAYNOR',
            '7603 - DIONISIA',
            '1623 - DIQUE LUJAN',
            '1925 - DOCK CENTRAL',
            '1871 - DOCK SUD',
            '6708 - DOCTOR DOMINGO CABRED',
            '7212 - DOCTOR DOMINGO HAROSTEGUY',
            '7100 - DOLORES',
            '1984 - DOMSELAAR',
            '1876 - DON BOSCO',
            '7135 - DON CIPRIANO',
            '1611 - DON TORCUATO',
            '7116 - DON VICENTE',
            '6042 - DOS HERMANOS',
            '7007 - DOS NACIONES',
            '1980 - DOYHENARD',
            '2935 - DOYLE',
            '6242 - DRABBLE',
            '6455 - DRYSDALE',
            '8170 - DUCOS',
            '6505 - DUDIGNAC',
            '8164 - DUFAUR',
            '2764 - DUGGAN',
            '6405 - DUHAU',
            '7401 - DURA',
            '6050 - DUSSAUD',
            '6030 - EDMUNDO PERKINS',
            '6064 - EDUARDO COSTA',
            '7013 - EGA',
            '7100 - EL 60',
            '7225 - EL ALBA',
            '7249 - EL ARAZA',
            '2721 - EL ARBOLITO',
            '8504 - EL BAGUAL',
            '7507 - EL BOMBERO',
            '7601 - EL BOQUERON',
            '7150 - EL BOQUERON',
            '6537 - EL CAMOATI',
            '7135 - EL CARBON',
            '2754 - EL CARMEN',
            '7203 - EL CARMEN DE LANGUEYU',
            '6537 - EL CARPINCHO',
            '7500 - EL CARRETERO',
            '7607 - EL CENTINELA',
            '7163 - EL CHAJA',
            '7201 - EL CHALAR',
            '7007 - EL CHEIQUE',
            '7530 - EL CHELFORO',
            '7263 - EL CHUMBIAO',
            '8117 - EL CORTAPIE',
            '7503 - EL CRISTIANO',
            '7620 - EL CRUCE',
            '2935 - EL DESCANSO',
            '7116 - EL DESTINO',
            '6241 - EL DIA',
            '7531 - EL DIVISORIO',
            '6031 - EL DORADO',
            '1735 - EL DURAZNO',
            '2946 - EL ESPINILLO',
            '7130 - EL EUCALIPTUS',
            '2804 - EL FENIX',
            '7000 - EL GALLO',
            '7200 - EL GUALICHO',
            '7007 - EL HERVIDERO',
            '6531 - EL JABALI',
            '2707 - EL JAGUEL',
            '1842 - EL JAGUEL',
            '7620 - EL JUNCO',
            '2916 - EL JUPITER',
            '7635 - EL LENGUARAZ',
            '1657 - EL LIBERTADOR',
            '7513 - EL LUCERO',
            '7313 - EL LUCHADOR',
            '7260 - EL MANGRULLO',
            '7607 - EL MARQUESADO',
            '7305 - EL MIRADOR',
            '7623 - EL MORO',
            '2740 - EL NACIONAL',
            '6437 - EL NILO',
            '1684 - EL PALOMAR',
            '1865 - EL PAMPERO',
            '2916 - EL PARAISO',
            '8144 - EL PARAISO',
            '7263 - EL PARCHE',
            '6341 - EL PARQUE',
            '2720 - EL PELADO',
            '7531 - EL PENSAMIENTO',
            '6053 - EL PEREGRINO',
            '1907 - EL PINO',
            '7607 - EL PITO',
            '7641 - EL PITO',
            '6550 - EL PORVENIR',
            '2751 - EL QUEMADO',
            '6474 - EL RECADO',
            '7612 - EL REFUGIO',
            '2741 - EL RETIRO',
            '6017 - EL RETIRO',
            '7130 - EL RINCON',
            '8146 - EL RINCON',
            '1921 - EL ROSARIO',
            '6463 - EL SANTIAGO',
            '7303 - EL SAUCE',
            '7223 - EL SIASGO',
            '2752 - EL SILENCIO',
            '2715 - EL SOCORRO',
            '7600 - EL SOLDADO',
            '1617 - EL TALAR',
            '2801 - EL TATU',
            '6515 - EL TEJAR',
            '6437 - EL TREBA',
            '7500 - EL TRIANGULO',
            '7207 - EL TRIGO',
            '6467 - EL TRIO',
            '6073 - EL TRIUNFO',
            '7116 - EL VENCE',
            '7620 - EL VERANO',
            '7174 - EL VIGILANTE',
            '7620 - EL VOLANTE',
            '8151 - EL ZORRO',
            '1727 - ELIAS ROMERO',
            '6242 - ELORDI',
            '7243 - ELVIRA',
            '7260 - EMILIANO REYNOSO',
            '6628 - EMILIO AYERZA',
            '6241 - EMILIO BUNGE',
            '8505 - EMILIO LAMARCA',
            '6634 - EMITA',
            '7263 - EMMA',
            '1633 - EMPALME',
            '7000 - EMPALME CERRO CHATO',
            '7249 - EMPALME LOBOS',
            '1913 - EMPALME MAGDALENA',
            '8117 - EMPALME PIEDRA ECHADA',
            '7401 - EMPALME QUERANDIES',
            '6077 - ENCINA',
            '7641 - ENERGIA',
            '1741 - ENRIQUE FYNN',
            '6467 - ENRIQUE LAVALLE',
            '1925 - ENSENADA',
            '6443 - EPUMER',
            '2903 - EREZCANO',
            '8181 - ERIZE',
            '6665 - ERNESTINA',
            '1927 - ESC NAV MILITAR RIO SANT',
            '2801 - ESCALADA',
            '7135 - ESCRIBANO P NICOLAS',
            '1815 - ESCUELA AGRICOLA DON BOSCO',
            '7174 - ESCUELA AGRICOLA RURAL',
            '6509 - ESCUELA AGRICOLA SALESIANA',
            '6003 - ESCUELA AGRICOLA SALESIANA',
            '7163 - ESPADA',
            '1987 - ESPARTILLAR',
            '8171 - ESPARTILLAR',
            '7135 - ESPARTILLAR',
            '6561 - ESPIGAS',
            '8107 - ESPORA',
            '6601 - ESPORA',
            '7100 - ESQUINA DE CROTTO',
            '6706 - EST JAUREGUI VA FLANDRIA',
            '7505 - EST SAN FRANCISCO BELLOQ',
            '1629 - ESTABLECIMIENTO SAN MIGUEL',
            '6003 - ESTACION ASCENSION',
            '2942 - ESTACION BARADERO',
            '7500 - ESTACION BARROW',
            '6339 - ESTACION CAIOMUTA',
            '7536 - ESTACION CORONEL PRINGLES',
            '6005 - ESTACION GENERAL ARENALES',
            '1903 - ESTACION GOMEZ',
            '6431 - ESTACION LAGO EPECUEN',
            '7300 - ESTACION LAZZARINO',
            '6070 - ESTACION LINCOLN',
            '1901 - ESTACION MORENO',
            '6501 - ESTACION PROVINCIAL',
            '7020 - ESTANCIA CHAPAR',
            '2723 - ESTANCIA LAS GAMAS',
            '8148 - ESTANCIA LAS ISLETAS',
            '6075 - ESTANCIA MITIKILI',
            '6075 - ESTANCIA SAN ANTONIO',
            '6537 - ESTANCIA SAN CLAUDIO',
            '7130 - ESTANCIA SAN RAFAEL',
            '2761 - ESTANCIA SANTA CATALINA',
            '7225 - ESTANCIA VIEJA',
            '2909 - ESTANCIAS',
            '8185 - ESTEBAN A GASCON',
            '6475 - ESTEBAN DE LUCA',
            '6607 - ESTEBAN DIAZ',
            '8127 - ESTELA',
            '7260 - ESTHER',
            '8118 - ESTOMBA',
            '6725 - ESTRELLA NACIENTE',
            '7207 - ESTRUGAMOU',
            '6703 - ETCHEGOYEN',
            '2812 - EXALTACION DE LA CRUZ',
            '1804 - EZEIZA',
            '1882 - EZPELETA ESTE',
            '1882 - EZPELETA OESTE',
            '7153 - FAIR',
            '8150 - FARO',
            '7165 - FARO QUERANDI',
            '7103 - FARO SAN ANTONIO',
            '8504 - FARO SEGUNDA BARRANCOSA',
            '1633 - FATIMA ESTACION EMPALME',
            '6430 - FATRALO',
            '6500 - FAUZON',
            '8129 - FELIPE SOLA',
            '6223 - FERNANDO MARTI',
            '6003 - FERRE',
            '2763 - FLAMENCO',
            '1888 - FLORENCIO VARELA',
            '6064 - FLORENTINO AMEGHINO',
            '1602 - FLORIDA',
            '1602 - FLORIDA OESTE',
            '2700 - FONTEZUELA',
            '6031 - FORTIN ACHA',
            '8160 - FORTIN CHACO',
            '7316 - FORTIN IRENE',
            '7404 - FORTIN LAVALLE',
            '8148 - FORTIN MERCEDES',
            '7406 - FORTIN NECOCHEA',
            '6403 - FORTIN OLAVARRIA',
            '6417 - FORTIN PAUNERO',
            '6001 - FORTIN TIBURCIO',
            '8148 - FORTIN VIEJO',
            '6073 - FORTIN VIGILANCIA',
            '1746 - FRANCISCO ALVAREZ',
            '2700 - FRANCISCO AYERZA',
            '7221 - FRANCISCO BERRA',
            '1808 - FRANCISCO CASAL',
            '6230 - FRANCISCO CASAL',
            '6403 - FRANCISCO DE VITORIA',
            '7301 - FRANCISCO J MEEKS',
            '6472 - FRANCISCO MADERO',
            '6475 - FRANCISCO MAGNANO',
            '6341 - FRANCISCO MURATURE',
            '6614 - FRANKLIN',
            '6516 - FRENCH',
            '1923 - FRIGORIFICO ARMOUR',
            '2800 - FRIGORIFICO LAS PALMAS',
            '8160 - FUERTE ARGENTINO',
            '1925 - FUERTE BARRAGAN',
            '7007 - FULTON',
            '7220 - FUNKE',
            '2745 - GAHAN',
            '7203 - GALERA DE TORRES',
            '6513 - GALO LLORENTE',
            '7136 - GANDARA',
            '8162 - GARCIA DEL RIO',
            '7003 - GARDEY',
            '1619 - GARIN',
            '6411 - GARRE',
            '8103 - GARRO',
            '7607 - GENERAL ALVARADO',
            '7263 - GENERAL ALVEAR',
            '6005 - GENERAL ARENALES',
            '7223 - GENERAL BELGRANO',
            '7101 - GENERAL CONESA',
            '2907 - GENERAL CONESA',
            '7118 - GENERAL GUIDO',
            '1739 - GENERAL HORNOS',
            '7406 - GENERAL LAMADRID',
            '1741 - GENERAL LAS HERAS',
            '7103 - GENERAL LAVALLE',
            '7163 - GENERAL MADARIAGA',
            '1911 - GENERAL MANSILLA',
            '6646 - GENERAL O BRIEN',
            '1617 - GENERAL PACHECO',
            '6050 - GENERAL PINTO',
            '7172 - GENERAL PIRAN',
            '6614 - GENERAL RIVAS',
            '1748 - GENERAL RODRIGUEZ',
            '2905 - GENERAL ROJO',
            '8124 - GENERAL RONDEAU',
            '1650 - GENERAL SAN MARTIN',
            '7503 - GENERAL VALDEZ',
            '6015 - GENERAL VIAMONTE',
            '6230 - GENERAL VILLEGAS',
            '6507 - GERENTE CILLEY',
            '1870 - GERLI',
            '1824 - GERLI',
            '6053 - GERMANIA',
            '8151 - GIL',
            '6407 - GIRODIAS',
            '6451 - GIRONDO',
            '1856 - GLEW',
            '8129 - GLORIALDO',
            '6451 - GNECCO',
            '7163 - GO',
            '2764 - GOBERNADOR ANDONAEGHI',
            '6531 - GOBERNADOR ARIAS',
            '2946 - GOBERNADOR CASTRO',
            '1888 - GOBERNADOR COSTA',
            '1981 - GOBERNADOR OBLIGADO',
            '7260 - GOBERNADOR ORTIZ DE ROSAS',
            '7221 - GOBERNADOR UDAONDO',
            '6621 - GOBERNADOR UGARTE',
            '7163 - GOBOS',
            '6614 - GOLDNEY',
            '1983 - GOMEZ',
            '1983 - GOMEZ  DE LA VEGA',
            '6241 - GONDRA',
            '1759 - GONZALEZ CATAN',
            '6239 - GONZALEZ MORENO',
            '6605 - GONZALEZ RISOS',
            '7226 - GORCHS',
            '2717 - GORNATTI',
            '7163 - GOROSO',
            '6632 - GOROSTIAGA',
            '6727 - GOUIN',
            '6608 - GOWLAND',
            '8175 - GOYENA',
            '7220 - GOYENECHE',
            '6335 - GRACIARENA',
            '8105 - GRAL DANIEL CERRI',
            '1615 - GRAND BOURG',
            '1925 - GRAND DOCK',
            '1757 - GREGORIO DE LAFERRERE',
            '6740 - GREGORIO VILLAFA',
            '6627 - GRISOLIA',
            '8101 - GRUMBEIN',
            '6435 - GUAMINI',
            '6476 - GUANACO',
            '7220 - GUARDIA DEL MONTE',
            '1862 - GUERNICA',
            '7116 - GUERRERO',
            '2717 - GUERRICO',
            '2707 - GUIDO SPANO',
            '1885 - GUILLERMO E HUDSON',
            '6053 - GUNTHER',
            '1706 - HAEDO',
            '6064 - HALCEY',
            '6511 - HALE',
            '6005 - HAM',
            '7174 - HARAS 1 DE MAYO',
            '7223 - HARAS CHACABUCO',
            '7605 - HARAS CHAPADMALAL',
            '6627 - HARAS EL CARMEN',
            '6050 - HARAS EL CATORCE',
            '2701 - HARAS EL CENTINELA',
            '7020 - HARAS EL CISNE',
            '7631 - HARAS EL MORO',
            '2916 - HARAS EL OMBU',
            '7245 - HARAS EL SALASO',
            '6612 - HARAS LA ELVIRA',
            '7011 - HARAS LA LULA',
            '2752 - HARAS LOS CARDALES',
            '7631 - HARAS NACIONAL',
            '7620 - HARAS OJO DEL AGUA',
            '7263 - HARAS R DE LA PARVA',
            '7136 - HARAS SAN IGNACIO',
            '2705 - HARAS SAN JACINTO',
            '6075 - HARAS TRUJUI',
            '6723 - HEAVY',
            '6465 - HENDERSON',
            '6621 - HENRY BELL',
            '6233 - HEREFORD',
            '6557 - HERRERA VEGAS',
            '8142 - HILARIO ASCASUBI',
            '7163 - HINOJALES',
            '7318 - HINOJO',
            '7172 - HOGAR MARIANO ORTIZ BASUALDO',
            '1739 - HORNOS',
            '6537 - HORTENSIA',
            '7630 - HOSPITAL NECOCHEA',
            '2700 - HOSPITAL SAN ANTONIO DE LA LLA',
            '7545 - HUANGUELEN',
            '7500 - HUESO CLAVADO',
            '6511 - HUETEL',
            '2707 - HUNTER',
            '1686 - HURLINGHAM',
            '6455 - HUSARES',
            '7223 - IBA',
            '8512 - IGARZABAL',
            '1909 - IGNACIO CORREAS ARANA',
            '6623 - INDACOCHEA',
            '7114 - INDIA MUERTA',
            '7501 - INDIO RICO',
            '2747 - INES INDART',
            '1612 - INGENIERO ADOLFO SOURDEAUX',
            '1891 - INGENIERO ALLAN',
            '6051 - INGENIERO BALBIN',
            '6457 - INGENIERO BEAUGEY',
            '6651 - INGENIERO DE MADRID',
            '1623 - INGENIERO MASCHWITZ',
            '2935 - INGENIERO MONETA',
            '6743 - INGENIERO SILVEYRA',
            '6337 - INGENIERO THOMPSON',
            '8103 - INGENIERO WHITE',
            '6603 - INGENIERO WILLIAMS',
            '6451 - INOCENCIO SOSA',
            '7163 - INVERNADAS',
            '6013 - IRALA',
            '7009 - IRAOLA',
            '7507 - IRENE',
            '2943 - IRENEO PORTELA',
            '6042 - IRIARTE',
            '1765 - ISIDRO CASANOVA',
            '8111 - ISLA CATARELLI',
            '2931 - ISLA LOS LAURELES',
            '1601 - ISLA MARTIN GARCIA',
            '1929 - ISLA PAULINO',
            '1929 - ISLA SANTIAGO',
            '8146 - ISLA VERDE',
            '6667 - ISLAS',
            '7163 - ISONDU',
            '6557 - ITURREGUI',
            '1714 - ITUZAINGO',
            '8508 - JARRILLA',
            '6706 - JAUREGUI JOSE MARIA',
            '1986 - JEPPENER',
            '1896 - JOAQUIN GORINA',
            '8156 - JOSE A GUISASOLA',
            '8506 - JOSE B CASAS',
            '1665 - JOSE CLEMENTE PAZ',
            '1905 - JOSE FERRARI',
            '1702 - JOSE INGENIEROS',
            '1655 - JOSE LEON SUAREZ',
            '7263 - JOSE M MICHEO',
            '6409 - JOSE MARIA BLANCO',
            '1846 - JOSE MARMOL',
            '7260 - JOSE SOJO',
            '8142 - JUAN A PRADERE',
            '7245 - JUAN ATUCHA',
            '6034 - JUAN BAUTISTA ALBERDI',
            '7267 - JUAN BLAQUIER',
            '8136 - JUAN COUSTE',
            '7517 - JUAN E BARRA',
            '6551 - JUAN F IBARRA',
            '2909 - JUAN G PUJOL',
            '6603 - JUAN JOSE ALMEYRA',
            '6474 - JUAN JOSE PASO',
            '1890 - JUAN MARIA GUTIERREZ',
            '7011 - JUAN N FERNANDEZ',
            '7247 - JUAN TRONCONI',
            '6430 - JUAN V CILLEY',
            '6663 - JUAN VELA',
            '1894 - JUAN VUCETICH EX DR R LEVENE',
            '2717 - JUANA A DE LA PE',
            '7169 - JUANCHO',
            '1913 - JULIO ARDITI',
            '6000 - JUNIN',
            '2745 - KENNY',
            '2763 - KILOMETRO 102',
            '1915 - KILOMETRO 103',
            '6723 - KILOMETRO 108',
            '8101 - KILOMETRO 11',
            '7240 - KILOMETRO 112',
            '6605 - KILOMETRO 116',
            '6603 - KILOMETRO 117',
            '6720 - KILOMETRO 125',
            '6600 - KILOMETRO 125',
            '7221 - KILOMETRO 128',
            '7226 - KILOMETRO 146',
            '2935 - KILOMETRO 172',
            '2946 - KILOMETRO 184',
            '2741 - KILOMETRO 187',
            '7100 - KILOMETRO 212',
            '6017 - KILOMETRO 282',
            '6070 - KILOMETRO 321',
            '6533 - KILOMETRO 322',
            '7400 - KILOMETRO 333',
            '6075 - KILOMETRO 352',
            '6075 - KILOMETRO 356',
            '2705 - KILOMETRO 36',
            '6457 - KILOMETRO 386',
            '6467 - KILOMETRO 393',
            '7005 - KILOMETRO 404',
            '7313 - KILOMETRO 433',
            '1980 - KILOMETRO 44',
            '7635 - KILOMETRO 440',
            '1727 - KILOMETRO 45',
            '1635 - KILOMETRO 45',
            '1727 - KILOMETRO 53',
            '8151 - KILOMETRO 563',
            '1981 - KILOMETRO 58',
            '1814 - KILOMETRO 59',
            '1629 - KILOMETRO 61',
            '8109 - KILOMETRO 652',
            '8105 - KILOMETRO 666',
            '8144 - KILOMETRO 697',
            '1981 - KILOMETRO 70',
            '1737 - KILOMETRO 77',
            '1741 - KILOMETRO 79',
            '1980 - KILOMETRO 82',
            '6605 - KILOMETRO 83',
            '2804 - KILOMETRO 88',
            '7221 - KILOMETRO 88',
            '1815 - KILOMETRO 88',
            '7220 - KILOMETRO 88',
            '8101 - KILOMETRO 9 SUD',
            '6605 - KILOMETRO 90',
            '1911 - KILOMETRO 92',
            '6031 - KILOMETRO 95',
            '7530 - KRABBE',
            '6533 - LA ADELA',
            '7243 - LA ADELAIDA',
            '7130 - LA ALAMEDA',
            '7116 - LA ALCIRA',
            '7130 - LA AMALIA',
            '7130 - LA AMISTAD',
            '7119 - LA AMORILLA',
            '6003 - LA ANGELITA',
            '1865 - LA ARGENTINA',
            '6555 - LA ARMONIA',
            '6513 - LA AURORA',
            '8151 - LA AURORA',
            '7009 - LA AURORA',
            '7007 - LA AZOTEA',
            '7130 - LA AZOTEA GRANDE',
            '7005 - LA AZUCENA',
            '1923 - LA BALANDRA',
            '7521 - LA BALLENA',
            '7605 - LA BALLENERA',
            '7260 - LA BARRANCOSA',
            '6003 - LA BEBA',
            '8136 - LA BLANCA',
            '7243 - LA BLANQUEADA',
            '2933 - LA BOLSA',
            '7620 - LA BRAVA',
            '2930 - LA BUANA MOZA',
            '7020 - LA CALERA',
            '7609 - LA CALETA',
            '6616 - LA CALIFORNIA ARGENTINA',
            '7260 - LA CAMPANA',
            '6628 - LA CARLOTA',
            '6471 - LA CARRETA',
            '6403 - LA CAUTIVA',
            '8144 - LA CELIA',
            '8136 - LA CELINA',
            '6725 - LA CENTRAL',
            '1737 - LA CHOZA',
            '7316 - LA CHUMBEADA',
            '7223 - LA CHUMBEADA',
            '7408 - LA COLINA',
            '7603 - LA COLMENA',
            '7300 - LA COLORADA',
            '7119 - LA COLORADA',
            '8126 - LA COLORADA CHICA',
            '7153 - LA CONSTANCIA',
            '7545 - LA COPETA',
            '2700 - LA CORA',
            '7114 - LA CORINA',
            '7114 - LA CORINCO',
            '1814 - LA COSTA',
            '7114 - LA COSTA',
            '6461 - LA COTORRA',
            '6017 - LA DELFINA',
            '2740 - LA DELIA',
            '7116 - LA DESPIERTA',
            '6538 - LA DORITA',
            '6628 - LA DORMILONA',
            '7637 - LA DULCE',
            '7603 - LA ELMA',
            '7007 - LA ESPERANZA',
            '7223 - LA ESPERANZA',
            '2915 - LA ESPERANZA',
            '7163 - LA ESPERANZA GRAL MADARIAGA',
            '7205 - LA ESPERANZA ROSAS LAS FLORES',
            '7100 - LA ESTRELLA',
            '7403 - LA ESTRELLA',
            '8136 - LA EVA',
            '7503 - LA FELICIANA',
            '8185 - LA FLORIDA',
            '7116 - LA FLORIDA',
            '6720 - LA FLORIDA',
            '1748 - LA FRATERNIDAD',
            '1814 - LA GARITA',
            '7521 - LA GAVIOTA',
            '8134 - LA GLEVA',
            '6665 - LA GLORIA',
            '1623 - LA GRACIELITA',
            '6437 - LA GREGORIA',
            '6437 - LA HERMINIA',
            '6475 - LA HIGUERA',
            '2805 - LA HORQUETA',
            '7130 - LA HORQUETA',
            '7500 - LA HORQUETA',
            '6005 - LA HUAYQUERIA',
            '2745 - LA INVENCIBLE',
            '7112 - LA ISABEL',
            '6555 - LA LARGA',
            '7116 - LA LARGA NUEVA',
            '2812 - LA LATA',
            '1814 - LA LEONOR',
            '6645 - LA LIMPIA',
            '6700 - LA LOMA',
            '7603 - LA LUCIA',
            '1636 - LA LUCILA',
            '7113 - LA LUCILA DEL MAR',
            '2752 - LA LUISA',
            '8150 - LA LUNA',
            '7225 - LA LUZ',
            '7603 - LA MADRECITA',
            '7300 - LA MANTEQUERIA',
            '6439 - LA MANUELA',
            '2715 - LA MARGARITA',
            '7260 - LA MARGARITA',
            '6471 - LA MARGARITA',
            '6640 - LA MARIA',
            '8109 - LA MARTINA',
            '7119 - LA MASCOTA',
            '7225 - LA MASCOTA',
            '8134 - LA MASCOTA',
            '2931 - LA MATILDE',
            '2707 - LA NACION',
            '7403 - LA NARCISA',
            '7400 - LA NAVARRA',
            '7005 - LA NEGRA',
            '2740 - LA NELIDA',
            '7545 - LA NEVADA',
            '6513 - LA NI',
            '1814 - LA NORIA',
            '7000 - LA NUMANCIA',
            '7313 - LA NUTRIA',
            '6022 - LA ORIENTAL',
            '6341 - LA PALA',
            '7007 - LA PALMA',
            '7403 - LA PALMIRA',
            '7263 - LA PAMPA',
            '7620 - LA PARA',
            '7001 - LA PASTORA',
            '7500 - LA PASTORA',
            '7245 - LA PAZ',
            '7247 - LA PAZ CHICA',
            '7601 - LA PEREGRINA',
            '6550 - LA PERLA',
            '2800 - LA PESQUERIA',
            '7116 - LA PIEDRA',
            '6007 - LA PINTA',
            '1900 - LA PLATA',
            '7631 - LA PLAYA',
            '8124 - LA POCHOLA',
            '7207 - LA PORTE',
            '7241 - LA PORTE',
            '6407 - LA PORTE',
            '1980 - LA POSADA',
            '7112 - LA POSTA',
            '7153 - LA POSTA',
            '6453 - LA PRADERA',
            '7543 - LA PRIMAVERA',
            '1921 - LA PRIMAVERA',
            '7630 - LA PRIMITIVA',
            '7112 - LA PROTECCION',
            '7311 - LA PROTEGIDA',
            '6561 - LA PROTEGIDA',
            '7311 - LA PROTEGIDA',
            '7403 - LA PROVIDENCIA',
            '2912 - LA QUERENCIA',
            '6667 - LA RABIA',
            '7260 - LA RAZON',
            '7603 - LA REFORMA',
            '7130 - LA REFORMA',
            '7110 - LA REFORMA',
            '7245 - LA REFORMA',
            '1744 - LA REJA',
            '7536 - LA RESERVA',
            '6623 - LA RICA',
            '7245 - LA RINCONADA',
            '2812 - LA ROSADA',
            '8187 - LA ROSALIA',
            '6667 - LA RUBIA',
            '1774 - LA SALADA',
            '7621 - LA SARA',
            '6612 - LA SARA',
            '2751 - LA SARITA',
            '8174 - LA SAUDADE',
            '8150 - LA SIRENA',
            '8154 - LA SOBERANA',
            '6535 - LA SOFIA',
            '8136 - LA SOMBRA',
            '7517 - LA SORTIJA',
            '6050 - LA SUIZA',
            '7163 - LA TABLADA',
            '1921 - LA TALINA',
            '7500 - LA TIGRA',
            '7174 - LA TOBIANA',
            '7403 - LA TOMASA',
            '6553 - LA TORRECITA',
            '6660 - LA TRIBU',
            '6015 - LA TRIBU',
            '6003 - LA TRINIDAD',
            '1804 - LA UNION',
            '7160 - LA UNION',
            '6601 - LA VALEROSA',
            '2715 - LA VANGUARDIA',
            '8181 - LA VASCONGADA',
            '7223 - LA VERDE',
            '6601 - LA VERDE',
            '7214 - LA VERDE',
            '7109 - LA VICTORIA',
            '7225 - LA VICTORIA',
            '6627 - LA VICTORIA DESVIO',
            '2751 - LA VIOLETA',
            '8115 - LA VIRGINIA',
            '8122 - LA VITICOLA',
            '6513 - LA YESCA',
            '6400 - LA ZANJA',
            '6077 - LA ZARATE',
            '7161 - LABARDEN',
            '6431 - LAGO EPECUEN',
            '6439 - LAGUNA ALSINA',
            '7620 - LAGUNA BRAVA',
            '8134 - LAGUNA CHASICO',
            '6001 - LAGUNA DE GOMEZ',
            '7240 - LAGUNA DE LOBOS',
            '7601 - LAGUNA DE LOS PADRES',
            '6501 - LAGUNA DEL CURA',
            '6435 - LAGUNA DEL MONTE',
            '7600 - LAGUNA DEL SOLDADO',
            '6660 - LAGUNA LAS MULITAS',
            '7214 - LAGUNA MEDINA',
            '6400 - LAGUNA REDONDA',
            '7151 - LANGUEYU',
            '1824 - LANUS',
            '6013 - LAPLACETTE',
            '7414 - LAPRIDA',
            '6451 - LARRAMENDY',
            '6634 - LARREA',
            '7531 - LARTIGAU',
            '7116 - LAS ACHIRAS',
            '7172 - LAS ARMAS',
            '2916 - LAS BAHAMAS',
            '7406 - LAS BANDURRIAS',
            '7130 - LAS BRUSCAS',
            '7241 - LAS CHACRAS',
            '7174 - LAS CHILCAS',
            '7116 - LAS CHILCAS',
            '7300 - LAS CORTADERAS',
            '8504 - LAS CORTADERAS',
            '6437 - LAS CUATRO HERMANAS',
            '2741 - LAS CUATRO PUERTAS',
            '8134 - LAS ESCOBAS',
            '7200 - LAS FLORES',
            '2930 - LAS FLORES',
            '6400 - LAS GUASQUITAS',
            '7412 - LAS HERMANAS',
            '8148 - LAS ISLETAS',
            '6476 - LAS JUANITAS',
            '7603 - LAS LOMAS',
            '1748 - LAS MALVINAS',
            '6607 - LAS MARIANAS',
            '7406 - LAS MARTINETAS',
            '6437 - LAS MERCEDES',
            '7530 - LAS MOSTAZAS',
            '7130 - LAS MULAS',
            '6507 - LAS NEGRAS',
            '7316 - LAS NIEVES',
            '7623 - LAS NUTRIAS',
            '8115 - LAS OSCURAS',
            '7150 - LAS PAJAS',
            '2806 - LAS PALMAS',
            '6022 - LAS PARVAS',
            '7605 - LAS PIEDRITAS',
            '7400 - LAS PIEDRITAS',
            '6533 - LAS ROSAS',
            '2707 - LAS SALADAS',
            '7007 - LAS SUIZAS',
            '7151 - LAS SULTANAS',
            '1921 - LAS TAHONAS',
            '7106 - LAS TONINAS',
            '7116 - LAS TORTUGAS',
            '6453 - LAS TOSCAS',
            '6437 - LAS TRES FLORES',
            '7500 - LAS VAQUERIAS',
            '7100 - LAS VIBORAS',
            '7406 - LASTRA',
            '7300 - LAZZARINO',
            '6032 - LEANDRO N ALEM',
            '7130 - LEGARISTI',
            '6400 - LERTORA',
            '6338 - LEUBUCO',
            '7116 - LEZAMA',
            '6700 - LEZICA Y TORREZURI',
            '7407 - LIBANO',
            '1716 - LIBERTAD',
            '7135 - LIBRES DEL SUD',
            '7007 - LICENCIADO MATIENZO',
            '2718 - LIERRA ADJEMIRO',
            '2806 - LIMA',
            '7505 - LIN CALEL',
            '6070 - LINCOLN',
            '1901 - LISANDRO OLMOS ETCHEVERRY',
            '1836 - LLAVALLOL',
            '7635 - LOBERIA',
            '7240 - LOBOS',
            '7100 - LOMA DE SALOMON',
            '7521 - LOMA DEL INDIO',
            '1657 - LOMA HERMOSA',
            '7403 - LOMA NEGRA',
            '7203 - LOMA NEGRA',
            '7203 - LOMA PARTIDA',
            '1625 - LOMA VERDE',
            '1981 - LOMA VERDE',
            '1832 - LOMAS DE ZAMORA',
            '1752 - LOMAS DEL MIRADOR',
            '2802 - LOMAS DEL RIO LUJAN',
            '1854 - LONGCHAMPS',
            '7021 - LOPEZ',
            '8117 - LOPEZ LECUBE',
            '2718 - LOPEZ MOLINARI',
            '6075 - LOS ALTOS',
            '2743 - LOS ANGELES',
            '1816 - LOS AROMOS',
            '6018 - LOS BOSQUES',
            '6242 - LOS CALDENES',
            '6062 - LOS CALLEJONES',
            '2814 - LOS CARDALES',
            '7620 - LOS CARDOS',
            '7226 - LOS CERRILLOS',
            '7635 - LOS CERROS',
            '6475 - LOS CHA',
            '7263 - LOS CHUCAROS',
            '6555 - LOS COLONIALES',
            '7263 - LOS CUATRO CAMINOS',
            '7220 - LOS EUCALIPTOS',
            '1895 - LOS EUCALIPTUS CASCO URBANO',
            '6343 - LOS GAUCHOS',
            '6015 - LOS HUESOS',
            '6451 - LOS INDIOS',
            '2709 - LOS INDIOS',
            '6230 - LOS LAURELES',
            '7000 - LOS LEONES',
            '1980 - LOS MERINOS',
            '7503 - LOS MOLLES',
            '7601 - LOS ORTIZ',
            '7603 - LOS PATOS',
            '7623 - LOS PINOS',
            '7412 - LOS PINOS',
            '1613 - LOS POLVORINES',
            '8512 - LOS POZOS',
            '1921 - LOS SANTOS VIEJOS',
            '1923 - LOS TALAS',
            '6015 - LOS TOLDOS',
            '7545 - LOUGE',
            '1741 - LOZANO',
            '6661 - LUCAS MONTEVERDE',
            '1917 - LUIS CHICO',
            '1838 - LUIS GUILLON',
            '6700 - LUJAN',
            '7639 - LUMB',
            '6439 - LURO',
            '7169 - MACEDO',
            '7151 - MAGALLANES',
            '6451 - MAGDALA',
            '1913 - MAGDALENA',
            '2718 - MAGUIRRE',
            '7160 - MAIPU',
            '6443 - MALABIA',
            '7631 - MALECON GARDELLA',
            '1846 - MALVINAS ARGENTINAS',
            '6661 - MAMAGUITA',
            '2717 - MANANTIALES',
            '2700 - MANANTIALES GRANDES',
            '1667 - MANUEL ALBERTI',
            '1897 - MANUEL B GONNET',
            '6608 - MANUEL JOSE GARCIA',
            '2713 - MANUEL OCAMPO',
            '1629 - MANZANARES',
            '2718 - MANZO Y NI',
            '1633 - MANZONE',
            '7633 - MAORI',
            '6557 - MAPIS',
            '1619 - MAQUINISTA F SAVIO',
            '7165 - MAR AZUL',
            '7174 - MAR CHIQUITA',
            '7109 - MAR DE AJO',
            '7609 - MAR DE COBO',
            '7165 - MAR DE LAS PAMPAS',
            '7600 - MAR DEL PLATA',
            '7607 - MAR DEL SUD',
            '7108 - MAR DEL TUYU',
            '2741 - MARCELINO UGARTE',
            '1727 - MARCOS PAZ',
            '1727 - MARCOS PAZ B BERNASCONI',
            '1727 - MARCOS PAZ B EL MARTILLO',
            '1727 - MARCOS PAZ B EL MORO',
            '1727 - MARCOS PAZ B EL ZORZAL',
            '1727 - MARCOS PAZ B LA LONJA',
            '1727 - MARCOS PAZ B LA MILAGROSA',
            '1727 - MARCOS PAZ B MARTIN FIERRO',
            '1727 - MARCOS PAZ B URIOSTE',
            '6400 - MARI LAUQUEN',
            '7003 - MARIA IGNACIA',
            '6467 - MARIA LUCILA',
            '6337 - MARIA P MORENO',
            '1723 - MARIANO ACOSTA',
            '2701 - MARIANO BENITEZ',
            '2718 - MARIANO H ALFONZO',
            '7517 - MARIANO ROLDAN',
            '6551 - MARIANO UNZUE',
            '6708 - MARISCAL SUCRE',
            '6667 - MARTIN BERRAONDO',
            '1682 - MARTIN CORONADO',
            '7311 - MARTIN FIERRO',
            '6400 - MARTIN FIERRO',
            '1640 - MARTINEZ',
            '6451 - MARUCHA',
            '6438 - MASUREL',
            '1627 - MATHEU',
            '6555 - MAURAS',
            '6531 - MAURICIO HIRSCH',
            '6645 - MAXIMO FERNANDEZ',
            '1812 - MAXIMO PAZ',
            '8146 - MAYOR BURATOVICH',
            '6053 - MAYOR JOSE ORELLANO',
            '6343 - MAZA',
            '6648 - MECHA',
            '6648 - MECHITA',
            '7605 - MECHONGUE',
            '7169 - MEDALAND',
            '7630 - MEDANO BLANCO',
            '8132 - MEDANOS',
            '1903 - MELCHOR ROMERO',
            '6748 - MEMBRILLAR',
            '1771 - MERCADO CENTRAL',
            '6600 - MERCEDES',
            '6239 - MERIDIANO VO',
            '1722 - MERLO',
            '7507 - MICAELA CASCALLARES',
            '1852 - MINISTRO RIVADAVIA',
            '6403 - MIRA PAMPA',
            '7607 - MIRAMAR',
            '7214 - MIRAMONTE',
            '7201 - MIRANDA',
            '6531 - MOCTEZUMA',
            '7020 - MOLINO GALILEO',
            '6627 - MOLL',
            '7136 - MONASTERIO',
            '6469 - MONES CAZON',
            '2743 - MONROE',
            '7119 - MONSALVO',
            '1825 - MONTE CHINGOLO',
            '7020 - MONTE CRESPO',
            '8185 - MONTE FIORE',
            '1842 - MONTE GRANDE',
            '8153 - MONTE HERMOSO',
            '1917 - MONTE VELOZ',
            '7167 - MONTECARLO',
            '8136 - MONTES DE OCA',
            '6230 - MOORES',
            '6507 - MOREA',
            '1744 - MORENO',
            '1708 - MORON',
            '6013 - MORSE',
            '6471 - MOURAS',
            '7404 - MU',
            '1663 - MU',
            '6501 - MULCAHY',
            '1605 - MUNRO',
            '2909 - MUTTI',
            '7613 - NAHUEL RUCA',
            '7007 - NAPALEOFU',
            '6605 - NAVARRO',
            '7630 - NECOCHEA',
            '6077 - NECOL ESTACION FCGM',
            '7223 - NEWTON',
            '7637 - NICANOR OLIVERA',
            '8151 - NICOLAS DESCALZI',
            '8134 - NICOLAS LEVALLE',
            '7316 - NIEVES',
            '6663 - NORBERTO DE LA RIESTRA',
            '1670 - NORDELTA',
            '6501 - NORUMBEGA',
            '7113 - NUEVA ATLANTIS',
            '6553 - NUEVA ESPA',
            '1907 - NUEVA HERMOSURA',
            '6451 - NUEVA PLATA',
            '8117 - NUEVA ROMA',
            '6077 - NUEVA SUIZA',
            '6748 - O HIGGINS',
            '7521 - OCHANDIO',
            '6537 - ODORQUI',
            '6652 - OLASCOAGA',
            '7400 - OLAVARRIA',
            '1981 - OLIDEN',
            '2931 - OLIVEIRA CESAR',
            '6608 - OLIVERA',
            '1636 - OLIVOS',
            '7545 - OMBU',
            '8142 - OMBUCTA',
            '6708 - OPEN DOOR',
            '7503 - ORENSE',
            '7509 - ORIENTE',
            '2812 - ORLANDO',
            '2703 - ORTIZ BASUALDO',
            '6660 - ORTIZ DE ROSAS',
            '7167 - OSTENDE',
            '2802 - OTAMENDI',
            '7545 - OTO',
            '7301 - PABLO ACOSTA',
            '1613 - PABLO NOGUES',
            '1657 - PABLO PODESTA',
            '7020 - PACHAN',
            '6434 - PALANTELEN',
            '6628 - PALEMON HUERGO',
            '7221 - PALMITAS',
            '1923 - PALO BLANCO',
            '2931 - PANAME',
            '1921 - PANCHO DIAZ',
            '6411 - PAPIN',
            '2935 - PARADA KILOMETRO 158',
            '1739 - PARADA KILOMETRO 76',
            '6703 - PARADA ROBLES',
            '6725 - PARADA TATAY',
            '7412 - PARAG',
            '7530 - PARAJE FRA PAL',
            '8158 - PARAJE LA AURORA',
            '7100 - PARAJE LA VASCA',
            '6550 - PARAJE MIRAMAR',
            '7540 - PARAJE SANTA ANA',
            '2711 - PARAJE SANTA ROSA',
            '1915 - PARAJE STARACHE',
            '7212 - PARDO',
            '7316 - PARISH',
            '7167 - PARQUE CARILO',
            '7020 - PARQUE MU',
            '1722 - PARQUE SAN MARTIN',
            '7114 - PARQUE TAILLADE',
            '7100 - PARRAVICHINI',
            '7547 - PASMAN',
            '8142 - PASO ALSINA',
            '8134 - PASO CRAMER',
            '7511 - PASO DEL MEDANO',
            '1742 - PASO DEL REY',
            '8115 - PASO MAYOR',
            '7163 - PASOS',
            '6077 - PASTEUR',
            '6503 - PATRICIOS',
            '6557 - PAULA',
            '2812 - PAVON',
            '1913 - PAYRO R',
            '6058 - PAZOS KANKI',
            '7225 - PE',
            '2711 - PEARSON',
            '6665 - PEDERNALES',
            '6451 - PEDRO GAMEN',
            '7515 - PEDRO LASALLE',
            '8148 - PEDRO LURO',
            '7135 - PEDRO NICOLAS ESCRIBANO',
            '6450 - PEHUAJO',
            '6409 - PEHUELCHES',
            '8109 - PEHUEN CO',
            '8117 - PELICURA',
            '6346 - PELLEGRINI',
            '1894 - PEREYRA',
            '1894 - PEREYRA IRAOLA PARQUE',
            '2933 - PEREZ MILLAN',
            '2700 - PERGAMINO',
            '7135 - PESSAGNO',
            '1870 - PI',
            '7540 - PI',
            '1921 - PI',
            '6051 - PICHINCHA',
            '8117 - PIEDRA ANCHA',
            '6241 - PIEDRITAS',
            '7633 - PIERES',
            '7517 - PIERINI',
            '8170 - PIGUE',
            '7116 - PILA',
            '1629 - PILAR',
            '7530 - PILLAHUINCO',
            '7167 - PINAMAR',
            '2703 - PINZON',
            '1921 - PIPINAS',
            '6551 - PIROVANO',
            '2705 - PIRUCO',
            '6634 - PLA',
            '7607 - PLA Y RAGNONI',
            '1885 - PLATANOS',
            '7609 - PLAYA CHAPADMALAL',
            '7109 - PLAYA LAS MARGARITAS',
            '7201 - PLAZA MONTERO',
            '1733 - PLOMER',
            '2703 - PLUMACHO',
            '1905 - POBLET',
            '6430 - POCITO',
            '7267 - POLVAREDAS',
            '7535 - PONTAUT',
            '1761 - PONTEVEDRA',
            '6063 - PORVENIR',
            '7404 - POURTALE',
            '6231 - PRADERE JUAN A',
            '1635 - PRESIDENTE DERQUI',
            '6621 - PRESIDENTE QUINTANA',
            '6422 - PRIMERA JUNTA',
            '8180 - PUAN',
            '6661 - PUEBLITOS',
            '6533 - PUEBLO MARTINEZ DE HOZ',
            '6000 - PUEBLO NUEVO',
            '6700 - PUEBLO NUEVO',
            '7400 - PUEBLO NUEVO',
            '2700 - PUEBLO OTERO',
            '6450 - PUEBLO SAN ESTEBAN',
            '7541 - PUEBLO SAN JOSE',
            '7541 - PUEBLO SANTA MARIA',
            '6620 - PUENTE BATALLA',
            '2740 - PUENTE CA',
            '2760 - PUENTE CASTEX',
            '7225 - PUENTE EL OCHENTA',
            '8111 - PUERTO BELGRANO',
            '8142 - PUERTO COLOMA',
            '1625 - PUERTO DE ESCOBAR',
            '8000 - PUERTO GALVAN',
            '1925 - PUERTO LA PLATA',
            '7641 - PUERTO NECOCHEA',
            '8111 - PUERTO ROSALES',
            '8508 - PUERTO TRES BONETES',
            '8506 - PUERTO WASSERMANN',
            '2763 - PUESTO DEL MEDIO',
            '2907 - PUJOL',
            '8109 - PUNTA ALTA',
            '1623 - PUNTA DE CANAL',
            '1917 - PUNTA INDIO',
            '1931 - PUNTA LARA',
            '7109 - PUNTA MEDANOS',
            '6335 - QUENUMA',
            '7631 - QUEQUEN',
            '7533 - QUI',
            '7406 - QUILCO',
            '1878 - QUILMES',
            '1879 - QUILMES OESTE',
            '6018 - QUIRNO COSTA',
            '6533 - QUIROGA',
            '1847 - RAFAEL CALZADA',
            '1755 - RAFAEL CASTILLO',
            '6001 - RAFAEL OBLIGADO',
            '2915 - RAMALLO',
            '6627 - RAMON BIAUS',
            '6533 - RAMON J NEILD',
            '7641 - RAMON SANTAMARINA',
            '1704 - RAMOS MEJIA',
            '7621 - RAMOS OTERO',
            '2701 - RANCAGUA',
            '1987 - RANCHOS',
            '1886 - RANELAGH',
            '7203 - RAUCH',
            '7530 - RAULET',
            '6734 - RAWSON',
            '7225 - REAL AUDIENCIA',
            '6559 - RECALDE',
            '6533 - REGINALDO J NEILD',
            '1826 - REMEDIOS DE ESCALADA',
            '1657 - REMEDIOS DE ESCALADA',
            '7307 - REQUENA',
            '7511 - RETA',
            '2752 - RETIRO SAN PABLO',
            '7313 - RICARDO GAVI',
            '1618 - RICARDO ROJAS',
            '7621 - RINCON DE BAUDRIX',
            '1648 - RINCON DE MILBERG',
            '1921 - RINCON DE NOARIO',
            '7225 - RINCON DE VIVOT',
            '6605 - RINCON NORTE',
            '2944 - RIO TALA',
            '6237 - RIVADAVIA',
            '8127 - RIVADEO',
            '6441 - RIVERA',
            '2703 - ROBERTO CANO',
            '1915 - ROBERTO PAYRO',
            '6075 - ROBERTS',
            '7404 - ROCHA',
            '2705 - ROJAS',
            '6430 - ROLITO ESTACION FCGB',
            '6612 - ROMAN BAEZ',
            '6403 - ROOSEVELT',
            '7245 - ROQUE PEREZ',
            '7205 - ROSAS',
            '6450 - ROVIRA',
            '6720 - RUIZ SOLIS',
            '1907 - RUTA 11 KILOMETRO 23',
            '1816 - RUTA 205 KILOMETRO 57',
            '1816 - RUTA 3 KILOMETRO 75 700',
            '6703 - RUTA 8 KILOMETRO 77',
            '2930 - RUTA 9 KILOMETRO 169 5',
            '2804 - RUTA 9 KILOMETRO 72',
            '8174 - SAAVEDRA',
            '7303 - SABBI',
            '6022 - SAFORCADA',
            '7163 - SALADA CHICA',
            '7163 - SALADA GRANDE',
            '7260 - SALADILLO',
            '7261 - SALADILLO NORTE',
            '6471 - SALAZAR',
            '8166 - SALDUNGARAY',
            '8506 - SALINA DE PIEDRA',
            '8134 - SALINAS CHICAS',
            '6339 - SALLIQUELO',
            '2741 - SALTO',
            '7241 - SALVADOR MARIA',
            '1980 - SAMBOROMBON',
            '8142 - SAN ADOLFO',
            '7623 - SAN AGUSTIN',
            '7620 - SAN ALBERTO',
            '8180 - SAN ANDRES',
            '1651 - SAN ANDRES',
            '6551 - SAN ANDRES',
            '6720 - SAN ANDRES DE GILES',
            '7305 - SAN ANDRES DE TAPALQUE',
            '8185 - SAN ANTONIO',
            '7116 - SAN ANTONIO',
            '2760 - SAN ANTONIO DE ARECO',
            '1718 - SAN ANTONIO DE PADUA',
            '7261 - SAN BENITO',
            '6476 - SAN BERNARDO',
            '6561 - SAN BERNARDO',
            '7111 - SAN BERNARDO DEL TUYU',
            '7011 - SAN CALA',
            '6451 - SAN CARLOS',
            '7521 - SAN CAYETANO',
            '7105 - SAN CLEMENTE DEL TUYU',
            '7603 - SAN CORNELIO',
            '7116 - SAN DANIEL',
            '7607 - SAN EDUARDO DEL MAR',
            '6601 - SAN ELADIO',
            '8136 - SAN EMILIO',
            '6017 - SAN EMILIO',
            '6661 - SAN ENRIQUE',
            '7116 - SAN ENRIQUE',
            '6725 - SAN ERNESTO',
            '2711 - SAN FEDERICO',
            '7603 - SAN FELIPE',
            '6417 - SAN FERMIN',
            '1646 - SAN FERNANDO',
            '7505 - SAN FRANCISCO DE BELLOCQ',
            '1881 - SAN FRANCISCO SOLANO',
            '1846 - SAN FRANCISCO SOLANO',
            '8124 - SAN GERMAN',
            '7305 - SAN GERVACIO',
            '7151 - SAN IGNACIO',
            '1642 - SAN ISIDRO',
            '6600 - SAN JACINTO',
            '7400 - SAN JACINTO',
            '7404 - SAN JORGE',
            '6665 - SAN JOSE',
            '6643 - SAN JOSE',
            '7635 - SAN JOSE',
            '8136 - SAN JOSE',
            '7203 - SAN JOSE',
            '1846 - SAN JOSE',
            '7114 - SAN JOSE DE GALI',
            '7109 - SAN JOSE DE LOS QUINTEROS',
            '7601 - SAN JOSE DE OTAMENDI',
            '6500 - SAN JUAN',
            '7401 - SAN JUAN',
            '2754 - SAN JUAN',
            '6530 - SAN JUAN DE NELSON',
            '7613 - SAN JULIAN',
            '7118 - SAN JUSTO',
            '1754 - SAN JUSTO',
            '7150 - SAN LAUREANO',
            '7007 - SAN MANUEL',
            '8164 - SAN MARTIN DE TOURS',
            '6239 - SAN MAURICIO',
            '7519 - SAN MAYOL',
            '1663 - SAN MIGUEL',
            '8185 - SAN MIGUEL ARCANGEL',
            '7220 - SAN MIGUEL DEL MONTE',
            '7631 - SAN MIGUEL DEL MORO',
            '2900 - SAN NICOLAS DE LOS ARROYOS',
            '7007 - SAN PASCUAL',
            '6734 - SAN PATRICIO',
            '2930 - SAN PEDRO',
            '7406 - SAN QUILCO',
            '7130 - SAN RAFAEL',
            '8150 - SAN RAMON',
            '2754 - SAN RAMON',
            '6424 - SAN RAMON',
            '7311 - SAN RAMON DE ANCHORENA',
            '8154 - SAN ROMAN',
            '6017 - SAN ROQUE',
            '6623 - SAN SEBASTIAN',
            '7521 - SAN SEVERO',
            '7621 - SAN SIMON',
            '7613 - SAN VALENTIN',
            '1865 - SAN VICENTE',
            '6233 - SANSINENA',
            '7243 - SANTA ALICIA',
            '7503 - SANTA CATALINA',
            '6463 - SANTA CECILIA CENTRO',
            '6472 - SANTA CECILIA NORTE',
            '6450 - SANTA CECILIA SUD',
            '7609 - SANTA CLARA DEL MAR',
            '7406 - SANTA CLEMENTINA',
            '2761 - SANTA COLOMA',
            '7414 - SANTA ELENA',
            '6700 - SANTA ELENA',
            '7609 - SANTA ELENA',
            '6241 - SANTA ELEODORA',
            '7243 - SANTA FELICIA',
            '6459 - SANTA INES',
            '6471 - SANTA INES',
            '7607 - SANTA IRENE',
            '6550 - SANTA ISABEL',
            '2935 - SANTA LUCIA',
            '7401 - SANTA LUISA',
            '6071 - SANTA MARIA',
            '6535 - SANTA MARIA BELLOQ',
            '6105 - SANTA REGINA',
            '2700 - SANTA RITA',
            '6437 - SANTA RITA PDO GUAMINI',
            '1739 - SANTA ROSA',
            '7303 - SANTA ROSA',
            '1888 - SANTA ROSA',
            '7212 - SANTA ROSA DE MINELLONO',
            '7163 - SANTA TERESA',
            '2912 - SANTA TERESA',
            '7107 - SANTA TERESITA',
            '2701 - SANTA TERESITA PERGAMINO',
            '7541 - SANTA TRINIDAD',
            '6660 - SANTIAGO GARBARINI',
            '7245 - SANTIAGO LARRE',
            '7119 - SANTO DOMINGO',
            '6530 - SANTO TOMAS',
            '6538 - SANTO TOMAS CHICO',
            '1676 - SANTOS LUGARES',
            '6507 - SANTOS UNZUE',
            '1872 - SARANDI',
            '2721 - SARASA',
            '6417 - SATURNO',
            '8105 - SAUCE CHICO',
            '7540 - SAUCE CORTO',
            '8150 - SAUCE GRANDE',
            '8150 - SAUCE GRANDE',
            '6030 - SAUZALES',
            '7119 - SEGUROLA',
            '6600 - SEMINARIO PIO XII',
            '7101 - SEVIGNE',
            '7316 - SHAW',
            '7609 - SIEMPRE VERDE',
            '7401 - SIERRA CHICA',
            '8168 - SIERRA DE LA VENTANA',
            '7601 - SIERRA DE LOS PADRES',
            '7403 - SIERRAS BAYAS',
            '6531 - SMITH',
            '7243 - SOL DE MAYO',
            '2709 - SOL DE MAYO',
            '6064 - SOLALE',
            '7151 - SOLANET',
            '2764 - SOLIS',
            '1885 - SOURIGUES',
            '1741 - SPERATTI',
            '7163 - SPERONI',
            '8103 - SPURR',
            '7536 - STEGMANN',
            '8508 - STROEDER',
            '6708 - SUCRE',
            '6612 - SUIPACHA',
            '6401 - SUNDBLAD',
            '1766 - TABLADA',
            '2743 - TACUARI',
            '7633 - TAMANGUEYU',
            '2700 - TAMBO NUEVO',
            '7000 - TANDIL',
            '7303 - TAPALQUE',
            '1770 - TAPIALES',
            '6721 - TATAY',
            '7021 - TEDIN URIBURU',
            '7530 - TEJO GALETA',
            '1834 - TEMPERLEY',
            '7401 - TENIENTE CORONEL MI',
            '8144 - TENIENTE ORIGONE',
            '8504 - TERMAS LOS GAUCHOS',
            '6343 - THAMES',
            '1648 - TIGRE',
            '6457 - TIMOTE',
            '7163 - TIO DOMINGO',
            '2754 - TODD',
            '7267 - TOLDOS VIEJOS',
            '6601 - TOMAS JOFRE',
            '7101 - TORDILLO',
            '8160 - TORNQUIST',
            '1635 - TORO',
            '6703 - TORRES',
            '1667 - TORTUGUITAS',
            '6400 - TRENQUE LAUQUEN',
            '6231 - TRES ALGARROBOS',
            '7500 - TRES ARROYOS',
            '8183 - TRES CUERVOS',
            '6443 - TRES LAGUNAS',
            '7100 - TRES LEGUAS',
            '6409 - TRES LOMAS',
            '8162 - TRES PICOS',
            '6727 - TRES SARGENTOS',
            '6053 - TRIGALES',
            '1806 - TRISTAN SUAREZ',
            '6071 - TRIUNVIRATO',
            '1618 - TRONCOS DEL TALAR',
            '6407 - TRONGE',
            '6501 - TROPEZON',
            '1664 - TRUJUI',
            '1834 - TURDERA',
            '6721 - TUYUTI',
            '7301 - UBALLES',
            '7151 - UDAQUIOLA',
            '7609 - UNIDAD TURISTICA CHAPADMALAL',
            '6553 - URDAMPILLETA',
            '1815 - URIBELARREA',
            '2718 - URQUIZA',
            '7301 - VA',
            '2764 - VAGUES',
            '6667 - VALDEZ',
            '1822 - VALENTIN ALSINA',
            '6401 - VALENTIN GOMEZ',
            '7630 - VALENZUELA ANTON',
            '7167 - VALERIA DEL MAR',
            '6557 - VALLIMANCA',
            '7519 - VASQUEZ',
            '7118 - VECINO',
            '6030 - VEDIA',
            '7003 - VELA',
            '7305 - VELLOSO',
            '8117 - VENANCIO',
            '7135 - VERGARA',
            '1917 - VERONICA',
            '2754 - VI',
            '8180 - VIBORAS',
            '1808 - VICENTE CASARES',
            '1638 - VICENTE LOPEZ',
            '7300 - VICENTE PEREDA',
            '1644 - VICTORIA',
            '6411 - VICTORINO DE LA PLAZA',
            '1915 - VIEYTES',
            '6070 - VIGELENCIA',
            '7208 - VILELA',
            '1607 - VILLA ADELINA',
            '1816 - VILLA ADRIANA',
            '1629 - VILLA AGUEDA',
            '6471 - VILLA ALDEANITA',
            '2800 - VILLA ANGUS',
            '7540 - VILLA ARCADIA',
            '1633 - VILLA ASTOLFI',
            '1653 - VILLA BALLESTER',
            '6000 - VILLA BELGRANO',
            '1682 - VILLA BOSCH',
            '6471 - VILLA BRANDA',
            '1888 - VILLA BROWN',
            '8000 - VILLA BUENOS AIRES',
            '1629 - VILLA BUIDE',
            '7203 - VILLA BURGOS',
            '7005 - VILLA CACIQUE',
            '2800 - VILLA CAPDEPONT',
            '6555 - VILLA CAROLA',
            '7505 - VILLA CARUCHA',
            '6430 - VILLA CASTELAR EST ERIZE',
            '2700 - VILLA CENTENARIO',
            '8000 - VILLA CERRITO',
            '7109 - VILLA CLELIA',
            '7607 - VILLA COPACABANA',
            '2718 - VILLA DA FONTE',
            '7000 - VILLA DAZA',
            '1614 - VILLA DE MAYO',
            '8109 - VILLA DEL MAR',
            '8000 - VILLA DELFINA',
            '2930 - VILLA DEPIETRI',
            '6500 - VILLA DIAMANTINA',
            '7630 - VILLA DIAZ VELEZ',
            '8000 - VILLA DOMINGO PRONSATO',
            '1874 - VILLA DOMINICO',
            '7000 - VILLA DUFAU',
            '8512 - VILLA ELENA',
            '1894 - VILLA ELISA',
            '1884 - VILLA ESPA',
            '6712 - VILLA ESPIL',
            '8000 - VILLA FLORESTA',
            '2800 - VILLA FLORIDA',
            '2800 - VILLA FOX',
            '6058 - VILLA FRANCIA',
            '6706 - VILLA FRANCIA',
            '7000 - VILLA GALICIA',
            '8101 - VILLA GENERAL ARIAS',
            '7165 - VILLA GESELL',
            '1713 - VILLA GOBERNADOR UDAONDO',
            '2700 - VILLA GODOY',
            '2912 - VILLA GRAL SAVIO EX SANCHEZ',
            '8101 - VILLA HARDING GREEN',
            '8101 - VILLA HERMINIA',
            '2930 - VILLA IGOLLO',
            '1752 - VILLA INSUPERABLE',
            '8126 - VILLA IRIS',
            '8000 - VILLA ITALIA',
            '7000 - VILLA ITALIA',
            '7020 - VILLA JUAREZ',
            '1625 - VILLA LA CHECHELA',
            '1881 - VILLA LA FLORIDA',
            '8109 - VILLA LAURA',
            '7000 - VILLA LAZA',
            '2946 - VILLA LEANDRA',
            '2761 - VILLA LIA',
            '8000 - VILLA LIBRE',
            '7203 - VILLA LOMA',
            '8000 - VILLA LORETO',
            '1754 - VILLA LUZURIAGA',
            '1672 - VILLA LYNCH',
            '6553 - VILLA LYNCH PUEYRREDON',
            '8109 - VILLA MAIO',
            '8185 - VILLA MARGARITA',
            '6628 - VILLA MARIA',
            '1603 - VILLA MARTELLI',
            '2800 - VILLA MASSONI',
            '6000 - VILLA MAYOR',
            '6343 - VILLA MAZA',
            '8000 - VILLA MITRE',
            '7318 - VILLA MONICA',
            '6625 - VILLA MOQUEHUA',
            '2800 - VILLA MOSCONI',
            '8000 - VILLA NOCITO',
            '1858 - VILLA NUMANCIA',
            '8000 - VILLA OBRERA',
            '8000 - VILLA OLGA GRUMBEIN',
            '6000 - VILLA ORTEGA',
            '6628 - VILLA ORTIZ',
            '6000 - VILLA PENOTTI',
            '2812 - VILLA PRECEPTOR M ROBLES',
            '6703 - VILLA PRECEPTOR MANUEL CRUZ',
            '2700 - VILLA PROGRESO',
            '2705 - VILLA PROGRESO',
            '7414 - VILLA PUEBLO NUEVO',
            '7631 - VILLA PUERTO QUEQUEN',
            '1674 - VILLA RAFFO',
            '2914 - VILLA RAMALLO',
            '2914 - VILLA RAMALLO EST FFCC',
            '8146 - VILLA RIO CHICO',
            '7101 - VILLA ROCH',
            '1631 - VILLA ROSA',
            '8103 - VILLA ROSAS',
            '6705 - VILLA RUIZ',
            '6101 - VILLA SABOYA',
            '1674 - VILLA SAENZ PE',
            '6720 - VILLA SAN ALBERTO',
            '2743 - VILLA SAN JOSE',
            '7203 - VILLA SAN PEDRO',
            '8000 - VILLA SANCHEZ ELIA',
            '2740 - VILLA SANGUINETTI',
            '1629 - VILLA SANTA MARIA',
            '1688 - VILLA SANTOS TESEI',
            '6511 - VILLA SANZ',
            '2930 - VILLA SARITA',
            '1706 - VILLA SARMIENTO',
            '6235 - VILLA SAUCE',
            '6430 - VILLA SAURI',
            '6403 - VILLA SENA',
            '8103 - VILLA SERRA',
            '6000 - VILLA TALLERES',
            '2944 - VILLA TERESA',
            '6000 - VILLA TRIANGULO',
            '1625 - VILLA VALLIER',
            '1888 - VILLA VATTEONE',
            '8160 - VILLA VENTANA',
            '1629 - VILLA VERDE',
            '7600 - VILLA VIGNOLO',
            '6000 - VILLA YORK',
            '6740 - VILLAFA',
            '2930 - VILLAIGRILLO',
            '8512 - VILLALONGA',
            '7225 - VILLANUEVA',
            '1731 - VILLARS',
            '1763 - VIRREY DEL PINO',
            '1646 - VIRREYES',
            '7130 - VISTA ALEGRE',
            '7130 - VITEL',
            '7612 - VIVORATA',
            '6064 - VOLTA',
            '7412 - VOLUNTAD',
            '2931 - VUELTA DE OBLIGADO',
            '6435 - VUELTA DE ZAPATA',
            '6646 - WARNES',
            '1875 - WILDE',
            '7303 - YERBAS',
            '7605 - YRAIZOS',
            '6443 - YUTUYACO',
            '1727 - ZAMUDIO',
            '7249 - ZAPIOLA',
            '2800 - ZARATE',
            '6018 - ZAVALIA',
            '1888 - ZEBALLOS',
            '1627 - ZELAYA',
            '7226 - ZENON VIDELA DORNA',
            '7545 - ZENTENA',
            '7530 - ZOILO PERALTA',
            '8103 - ZONA CANGREJALES',
            '2804 - ZONA DELTA CAMPANA',
            '1647 - ZONA DELTA SAN FERNANDO',
            '1649 - ZONA DELTA TIGRE',
            '2800 - ZONA DELTA ZARATE',
            '8151 - ZUBIAURRE'
        ];
    }

}
