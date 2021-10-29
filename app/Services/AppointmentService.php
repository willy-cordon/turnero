<?php

namespace App\Services;

use App\Mail\AppointmentCanceled;
use App\Mail\AppointmentCreated;
use App\Models\Activity;
use App\Models\ActivityInstance;
use App\Models\Appointment;
use App\Models\AppointmentAction;
use App\Models\AppointmentChangeLog;
use App\Models\AppointmentOrigin;
use App\Models\AppointmentType;
use App\Models\AppointmentUnloadType;
use App\Models\Client;
use App\Models\Dock;
use App\Models\Location;
use App\Models\Notification;
use App\Models\SchedulerCellLock;
use App\Models\SchedulerLock;
use App\Models\Scheme;
use App\Models\Settings;
use App\Models\Supplier;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Notifications\Action;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;
use Zendaemon\Services\Service;
use Zendaemon\Services\Traits\DestroyModel;



final class AppointmentService extends Service
{
    use DestroyModel;

    const ASSIGN_NEXT = 'Asignar próximo turno';
    const NOT_ASSIGN_NEXT = 'Fuera del Estudio';
    private $activityInstanceService;

    /**
     * Set model class name.
     *
     * @return void
     */
    protected function setModel(): void
    {
        $this->model = Appointment::class;
        $this->activityInstanceService = new ActivityInstanceService();
    }


    /**
     * @return Collection
     */
    public function all(): Collection
    {
        if (auth()->user()->can('scheduler_admin') || auth()->user()->can('scheduler_coordinator')) {
            //return $this->model::with(['dock','supplier', 'action'])->orderBy('id', 'desc')->take(50)->get();
            return $this->model::with(['dock', 'supplier', 'action'])->get();
        } else {
            return $this->model::where('created_by', auth()->user()->id)->with(['dock', 'supplier', 'action'])->get();
        }

    }

    public function getRelatedModels($location = null, $creation = false)
    {
        $appointmentActions = [];

        //TODO: cambiar toda esta logica a Roles
        if (auth()->user()->can('scheduler_admin')) {
            $appointmentActions = AppointmentAction::all()->sortBy('name');
        } else {
            if (auth()->user()->can('scheduler_coordinator')) {

                if($creation == true){
                    $appointmentActions = AppointmentAction::whereIn('id', [Appointment::STATUS_CONFIRM])->get()->sortBy('name');
                }else {
                    $appointmentActions = AppointmentAction::all()->sortBy('name');
                }
            } else {
                if (auth()->user()->can('scheduler_user')) {
                    if($creation == true){
                        $appointmentActions = AppointmentAction::whereIn('id', [Appointment::STATUS_CONFIRM])->get()->sortBy('name');
                    }else {
                        if(auth()->user()->hasRole(User::ROLE_DOCTOR) || auth()->user()->hasRole(User::ROLE_DOCTOR_ADMIN) ){
                            $appointmentActions = AppointmentAction::all()->sortBy('name');
                        }else {
                            $appointmentActions = AppointmentAction::whereIn('id', [Appointment::STATUS_CONFIRM, Appointment::STATUS_CANCELED])->get()->sortBy('name');
                        }
                    }
                }
            }
        }

        $appointmentTransportation = ['Via Cabify', 'Propio medio'];
        $appointmentNextSteps = [self::ASSIGN_NEXT, self::NOT_ASSIGN_NEXT];
        $clients = Client::all(['id', 'name']);
        $suppliers = Supplier::all('id', 'wms_name');
        $allLocations = Location::all();
        if ($location) {
            $init_hour = $location->init_hour;
            $end_hour = $location->end_hour;
            $prev_days_from = $location->prev_days_from;
            $prev_days_to = $location->prev_days_to;
            $appointment_init_minutes_size = $location->appointment_init_minutes_size;
            $schedulerLocks = SchedulerLock::where('location_id', $location->id)->get()->mapWithKeys(function ($lock) {
                return [Carbon::parse($lock->getOriginal('lock_date'))->format('Ymd') => $lock->available_appointments];
            });
            $schedulerCellLocks = [];

            $getCellLocks = SchedulerCellLock::query();
            $getCellLocks->where('location_id', '=', $location->id);
            $getCellLocksArray = $getCellLocks->get();
            $datas = $getCellLocksArray->pluck('lock_key');

            foreach ($datas as $data) {
                $schedulerCellLocks [$data] = 1;
            }

            $locations = collect([$location]);
            $appointments = Appointment::with(['dock', 'supplier', 'action'])
                ->whereIn('dock_id', $location->docks->pluck('id'))
                ->whereNotIn('action_id', [Appointment::STATUS_CANCELED, Appointment::NO_SHOW])
                ->get();
        } else {
            $init_hour = Settings::get('init_hour', 8);
            $end_hour = Settings::get('end_hour', 18);
            $prev_days_from = 0;
            $prev_days_to = 0;
            $appointment_init_minutes_size = Settings::get('appointment_init_minutes_size', 30);
            $schedulerLocks = [];
            $schedulerCellLocks = [];
            $locations = $allLocations;
            $appointments = Appointment::with(['dock', 'supplier', 'action'])
                ->whereNotIn('action_id',  [Appointment::STATUS_CANCELED, Appointment::NO_SHOW])
                ->get();
        }

        $schedulerSections = [];
        foreach ($locations as $location) {
            $docks = Dock::where("location_id", $location->id)->get();
            $locationData = ["key" => "location" . $location->id, "label" => $location->name, "open" => 'true'];
            $children = [];
            foreach ($docks as $dock) {
                $children[] = ["key" => $dock->id, "label" => $dock->name];
            }
            $locationData["children"] = $children;
            $schedulerSections[] = $locationData;
        }


        $schedulerAppointments = [];
        foreach ($appointments as $appointment) {

            $schedulerAppointments[] = ["start_date" => $appointment->start_date,
                "end_date" => $appointment->end_date,
                "text" => "appointment",
                "section_id" => $appointment->dock_id,
                "purchase_orders" => '',
                "supplier" => $appointment->supplier->wms_name,
                "client" => '',
                "trucks_qty" => '',
                "sku_qty" => '',
                "packages_qty" => '',
                "pallets_qty" => '',
                "type" => $allLocations->where('id', $appointment->dock->location_id)->first()->name,
                "action" => $appointment->action->name,
                "action_id" => $appointment->action->id,
                "unload_type" => [],
                "origin" => [],
                "comments" => $appointment->comments,
                "is_reservation" => $appointment->is_reservation,
                "synchronized_at" => $appointment->synchronized_at,
                "appointment_id" => $appointment->id];
        }
        return compact('appointmentActions', 'appointmentTransportation', 'clients', 'suppliers', 'schedulerSections', 'schedulerAppointments', 'appointment_init_minutes_size', 'init_hour', 'end_hour', 'schedulerLocks', 'appointmentNextSteps', 'prev_days_from', 'prev_days_to', 'schedulerCellLocks');
    }

    public function create(Request $request): Model
    {

        $appointment = $this->model::create($request->all());
        $rangeDayFrom = Carbon::parse($request->get('rangeDayFrom'))->format('Y-m-d');
        $rangeDayTo = Carbon::parse($request->get('rangeDayTo'))->format('Y-m-d');
        $start_date = Carbon::createFromFormat(config('app.datetime_format'),$request->get('start_date'))->format('Y-m-d');
        //TODO Probar
        if ($start_date >= $rangeDayFrom && $start_date <= $rangeDayTo)
        {
            $appointment->date_range_status = Appointment::IN_RANGE ;
        }else{
            $appointment->date_range_status = Appointment::OUT_RANGE ;
        }
        $appointment->save();
        AppointmentChangeLog::create(
            ['field_name' => 'Estado',
                'field_value_text' => AppointmentAction::find($request->action)->name,
                'field_value_text_old' => '',
                'appointment_id' => $appointment->id,
                'created_by' => auth()->user()->id]);
        return $this->save($appointment, $request);
    }

    public function update(Model $appointment, Request $request): Model
    {

        $old_startDate = $appointment->start_date;
        $start_date = $request->start_date;
        $actionQuery = AppointmentAction::where('id',$request->action)->first();

        $updateActivityInstances = false;
        if ($old_startDate != $start_date) {
            $updateActivityInstances = true;
            AppointmentChangeLog::create
            ([
                'field_name' => 'Fecha',
                'field_value_text' => $start_date,
                'field_value_text_old' => $old_startDate,
                'appointment_id' => $appointment->id,
                'created_by' => auth()->user()->id
            ]);
        }

        $oldState = $appointment->action->name;
        if ($oldState != $actionQuery->name){
            AppointmentChangeLog::create
            ([
                'field_name' => 'Estado',
                'field_value_text' => $actionQuery->name,
                'field_value_text_old' => $oldState,
                'appointment_id' => $appointment->id,
                'created_by' => auth()->user()->id
            ]);
        }


        $appointment->update($request->all());
        return $this->save($appointment, $request, $updateActivityInstances);
    }


    private function save(Model $appointment, Request $request, $updateActivityInstances = false): Model
    {

        if ($request->is_reservation == 0) {
            $appointment->purchaseOrders()->sync($request->purchase_orders);
        } else {
            $appointment->purchaseOrders()->detach();
        }

        $supplier = Supplier::find($request->supplier);

        $appointment->supplier()->associate($supplier)->save();
        $appointment->action()->associate(AppointmentAction::find($request->action))->save();
        $appointment->dock()->associate(Dock::find($request->dock))->save();

        $bcc = [auth()->user()->email, 'mjuzt@celsur.com.ar'];

        //$bcc = ['mjuzt@celsur.com.ar'];
        /*if (auth()->user()->supervisor_id) {
            $bcc[] = User::find(auth()->user()->supervisor_id)->email;
        }*/
        if ($request->action == Appointment::STATUS_ACCOMPLISH && $request->next_step == self::ASSIGN_NEXT) {
            $this->activityInstanceService->bulkCreate($appointment, Activity::ANSWER_FINISH);
        }

        if ($request->action == Appointment::STATUS_CONFIRM) {
            $this->activityInstanceService->bulkCreate($appointment, Activity::ANSWER_INIT);
            if ($updateActivityInstances) {
                $this->activityInstanceService->bulkUpdate($appointment);
            }
        }

        try {
            if ($request->action == Appointment::STATUS_CONFIRM) {
                if ($appointment->getOriginal('start_date') > now()) {
                    $subject = 'Reserva Turno para el Estudio de Vacuna contra el COVID-19 ' . $appointment->start_date; //Subject Email
                    $bcc = $this->addBccMail($appointment->dock->location->appointment_created_bcc_emails, $bcc);
                    Mail::to($supplier->email)->bcc($bcc)->send(new AppointmentCreated(auth()->user(), $supplier, $request->start_date, $request->transportation, $subject));
                }
            }

            //Obtenemos los emails de location, los recorremos y agregamos a el array $bcc
            if ($request->action == Appointment::STATUS_CANCELED) {
                if ($appointment->getOriginal('start_date') > now()) {
                    $subject = 'Cancelación de Turno para el Estudio de Vacuna contra el COVID-19 - Fecha del turno:' . $appointment->start_date;
                    $bcc = $this->addBccMail($appointment->dock->location->appointment_canceled_bcc_emails, $bcc);
                    Mail::to($supplier->email)->bcc($bcc)->send(new AppointmentCanceled(auth()->user(), $request->start_date, $subject, $appointment->supplier));
                }
            }
        }catch (\Exception $e){
            report($e);
        }

        return $appointment;
    }


    public function getDataTables(Request $request)
    {
        $locations = Location::all();
        $usersNames = User::all()->mapWithKeys(function ($user) {
            return [$user->id => $user->name];
        });
        $schemeNames = Scheme::all()->mapWithKeys(function ($scheme){return [$scheme->id=>$scheme->name];});
        $datetime_columns = ['appointments.start_date', 'appointments.created_at', 'appointments.updated_at'];
        ## Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');

        $totalColum = [];

        if (isset($request['columns'])) {
            for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++) {
                $requestColumn = $request['columns'][$i];
                if (!$requestColumn['search']['value'] == NULL) {
                    $totalColum [$requestColumn['data']] = $requestColumn['search']['value'];
                }
            }
        }

        $columnIndex = $columnIndex_arr[0]['column'];
        $columnName = str_replace('-', '.', $columnName_arr[$columnIndex]['data']);

        $columnSortOrder = $order_arr[0]['dir'];


        $supervision_user = false;
        $supervised_users = User::where('supervisor_id', '=', auth()->user()->id)->get();
        if ($supervised_users->count()>0){$supervision_user = true;}

        // Fetch records
        $appointmentsQuery = Appointment::Query();

        if(auth()->user()->hasRole(User::ROLE_SCHEDULER)) {
            $appointmentsQuery->where(function($query) {
                $query->where('appointments.created_by', auth()->user()->id)
                    ->orWhere('suppliers.recruiter_id', auth()->user()->id);
            });
            $totalRecords = Appointment::select('count(*) as allcount')->where('appointments.created_by', auth()->user()->id)->count();
        }else if (auth()->user()->hasRole(User::ROLE_COORDINATOR) && $supervision_user){
            $appointmentsQuery->whereIn('suppliers.recruiter_id', $supervised_users->pluck('id'));
            $totalRecords = Appointment::select('count(*) as allcount')
                ->leftJoin('suppliers', 'suppliers.id', '=', 'appointments.supplier_id')
                ->whereIn('suppliers.recruiter_id', $supervised_users->pluck('id'))->count();
        }else{
            //En el caso de que no sea scheduler o no sea un coordinador con supervisados, mostramos todos
            $totalRecords = Appointment::select('count(*) as allcount')->count();
        }


        $appointmentsQuery->leftJoin('docks', 'docks.id', '=', 'appointments.dock_id');
        $appointmentsQuery->leftJoin('locations', 'docks.location_id', '=', 'locations.id');
        $appointmentsQuery->leftJoin('suppliers', 'suppliers.id', '=', 'appointments.supplier_id');
        $appointmentsQuery->leftJoin('schemes', 'schemes.id', '=', 'suppliers.scheme_id');
        $appointmentsQuery->leftJoin('appointment_actions', 'appointment_actions.id', '=', 'appointments.action_id');
        $appointmentsQuery->leftJoin('users as created_by', 'created_by.id', '=', 'appointments.created_by');
        $appointmentsQuery->leftJoin('users as updated_by', 'updated_by.id', '=', 'appointments.updated_by');
        $appointmentsQuery->leftJoin('users as original_created_by', 'original_created_by.id', '=', 'appointments.original_created_by');
        $appointmentsQuery->leftJoin('users as recruiter', 'recruiter.id', '=', 'suppliers.recruiter_id');

        foreach ($totalColum as $column => $value) {
            $db_column = str_replace('-', '.', $column);
            if ($db_column == 'appointments.need_assistance') {
                if (strtolower($value) == 'si') {
                    $value = 1;
                }
                if (strtolower($value) == 'no') {
                    $value = 0;
                }
            }

            if ($db_column == 'suppliers.comorbidity') {
                if (strtolower($value) == 'si') {
                    $value = 1;
                }
                if (strtolower($value) == 'no') {
                    $value = 0;
                }
            }
            if (in_array($db_column, $datetime_columns)) {
                $db_column = DB::raw("DATE_FORMAT(" . $db_column . ",'%d/%m/%Y %H:%i')");
            }
            if($db_column != 'suppliers.wms_age') { // TODO: Ver como buscar por una columna generada
                $appointmentsQuery->where($db_column, 'like', '%' . $value . '%');
            }

        }

        $appointmentsQuery->select('appointments.*');
        $appointmentsQuery->orderBy($columnName, $columnSortOrder);
        $totalRecordsWithFilter = $appointmentsQuery->get()->count();
        if ($rowperpage != -1) {
            $appointmentsQuery->skip($start);
            $appointmentsQuery->take($rowperpage);
        }
        $appointments = $appointmentsQuery->get();

        $data_arr = array();

        foreach ($appointments as $appointment) {
            $buttons = '';
            if($appointment->action->id == Appointment::STATUS_CONFIRM || $appointment->action->id == Appointment::STATUS_IN_PLACE ){
                $buttons = '<a class="btn btn-xs btn-success" title=" ' . trans('global.edit') . ' " href=" ' . route('scheduler.appointments.edit', $appointment->id) . '"><i class="fas fa-pen"></i> </a> ';
                $buttons .= '<button type="button"  class="btn btn-xs btn-danger" onclick="$(\'#delete_form\').attr(\'action\', \' ' . route('scheduler.appointments.destroy', $appointment->id) . '\' );$(\'.delete-confirm-submit\').modal(\'show\')" title=" ' . trans('global.delete') . ' "><i class=\'fas fa-trash\'></i> </button> ';
            }

            $buttonview = '<a class="btn btn-xs btn-primary" title=" ' . trans('global.view') . '" href=" ' . route('scheduler.appointments.show', $appointment->id) . ' ">
                
                <i class="fas fa-eye"></i>
            </a> ';

            $buttonTransportation = '';
            if ($appointment->action->id != Appointment::STATUS_CANCELED  && $appointment->action->id != Appointment::STATUS_CONFIRM && $appointment->action->id != Appointment::NO_SHOW) {
                $buttonTransportation = '<a class="btn btn-xs btn-secondary" title=" ' . trans('global.view') . '" href=" ' . route('scheduler.transportation-vouchers.create', $appointment->id) . ' ">
                                              <i class="fas fa-car"></i>
                                          </a> ';
            }
            $actionControl = $buttons . $buttonview . $buttonTransportation;

            if (!auth()->user()->hasRole(User::ROLE_ADMIN)) {
                if ($appointment->supplier->is_intervened && !auth()->user()->hasRole(User::ROLE_DOCTOR) && !auth()->user()->hasRole(User::ROLE_DOCTOR_ADMIN)) {
                    $actionControl = '';
                } /*elseif (auth()->user()->hasRole(User::ROLE_DOCTOR) && !$appointment->supplier->is_intervened) {
                    $actionControl = '';
                }*/
            }


            $supplier_dni = $appointment->supplier->wms_id;
            $supplier_name = $appointment->supplier->wms_name;

            $supplier_name_link = '<a class="table-link" href=" ' . route('scheduler.suppliers.show', $appointment->supplier->id) . '" target="_blank">
                                ' . $supplier_name . '  <i class="fas fa-external-link-square-alt"></i>
                                </a>';
            $supplier_dni_link = '<a class="table-link" href="' . route('scheduler.suppliers.show', $appointment->supplier->id) . ' " target="_blank">
                                    ' . $supplier_dni . '  <i class="fas fa-external-link-square-alt"></i>
                                </a>';


            $intervened_class = '';
            if ((!auth()->user()->hasRole(User::ROLE_DOCTOR) || !auth()->user()->hasRole(User::ROLE_DOCTOR_ADMIN))  && $appointment->supplier->is_intervened) {
                $intervened_class = ' is_intervened';
            } elseif ((auth()->user()->hasRole(User::ROLE_DOCTOR) || auth()->user()->hasRole(User::ROLE_DOCTOR_ADMIN)) && $appointment->supplier->is_intervened) {
                $intervened_class = ' is_intervened';
            }


            $statusSupplier = 'status_outside';
            $supplier_status = $appointment->supplier->status == Supplier::STATUS_TWO ? $statusSupplier : '';

            $data_arr[] = array(
                "any" => '',
                "action" => $actionControl,
                "appointments-id" => $appointment->id,
                "appointments-start_date" => $appointment->start_date,
                "docks-name" => $appointment->dock->name,
                "suppliers-wms_name" => $supplier_name_link,
                "suppliers-wms_id" => $supplier_dni_link,
                "suppliers-comorbidity"=> $appointment->supplier->comorbidity == 1 ? 'SI' : 'NO',
                "schemes-name" => $schemeNames[$appointment->supplier->scheme_id] ?? '',
                "locations-name" => $locations->where('id', $appointment->dock->location_id)->first()->name ?? '',
                "appointment_actions-name" => $appointment->action->name ?? '',
                "suppliers-phone" => $appointment->supplier->phone ?? '',
                "suppliers-aux2" => $appointment->supplier->aux2 ?? '',
                "suppliers-email" => $appointment->supplier->email,
                "suppliers-address" => $appointment->supplier->address . ' ' . $appointment->supplier->aux5 . ', ' . $appointment->supplier->aux4,
                "suppliers-validate_address" => $appointment->supplier->validate_address,
                "appointments-transportation" => $appointment->transportation ?? '',
                "appointments-need_assistance" => $appointment->need_assistance == 1 ? 'SI' : 'NO',
                "appointments-next_step" => $appointment->next_step ?? '',
                "appointments-comments" => $appointment->comments ?? '',
                "appointments-created_at" => $appointment->created_at ?? '',
                "created_by-name" =>  Arr::exists($usersNames,$appointment->created_by) ? $usersNames[$appointment->created_by] : '',
                "original_created_by-name" => Arr::exists($usersNames,$appointment->original_created_by) ? $usersNames[$appointment->original_created_by] : '',
                "appointments-updated_at" => $appointment->updated_at ?? '',
                "updated_by-name" => Arr::exists($usersNames,$appointment->updated_by) ? $usersNames[$appointment->updated_by] : '',
                "recruiter-name" => Arr::exists($usersNames,$appointment->supplier->recruiter_id) ? $usersNames[$appointment->supplier->recruiter_id] : '',
                "supplier_name" => $supplier_name,
                "supplier_dni" => $supplier_dni,
                "action_id" => $appointment->action->id,
                "is_intervened" => $intervened_class,
                "suppliers-wms_date" => $appointment->supplier->wms_date ? Carbon::parse($appointment->supplier->getOriginal('wms_date'))->format('d/m/Y') : '',
                "suppliers-wms_age" => Carbon::parse($appointment->supplier->getOriginal('wms_date'))->age ?? '',
                "suppliers-wms_gender" => $appointment->supplier->wms_gender ?? '',
                "is_supplier_status" => $supplier_status
            );

        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordsWithFilter,
            "aaData" => $data_arr
        );
        return $response;
    }

    /**
     * @param $bcc_emails
     * @param array $bcc
     * @return array
     */
    private function addBccMail($bcc_emails, array $bcc): array
    {
        if ($bcc_emails != null && !empty($bcc_emails)) {
            $bcc_emails = explode(',', $bcc_emails);
            if(is_array($bcc_emails)) {
                foreach ($bcc_emails as $bcc_email) {
                    $bcc[] = $bcc_email;
                }
            }
        }
        return $bcc;
    }

}
