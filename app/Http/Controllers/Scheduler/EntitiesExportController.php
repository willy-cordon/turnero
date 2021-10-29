<?php

namespace App\Http\Controllers\Scheduler;

use App\Http\Controllers\Controller;
use App\Models\ActivityInstance;
use App\Models\Appointment;
use App\Models\Location;
use App\Models\Supplier;
use App\Models\SupplierInterventionLog;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Rap2hpoutre\FastExcel\FastExcel;

class EntitiesExportController extends Controller
{
    //

    public function index()
    {
        return view('scheduler.entities-export.index');
    }

    public function activitiesExport(Request $request)
    {

        $now = now();

        return (new FastExcel($this->getActivitiesExportData($request)))->download('actividades-'.$now->format('YmdHis').'.xlsx',function ($activityInstance) {
            return [
                "Fecha"=>Carbon::parse($activityInstance->date)->format('d/m/Y'),
                "Voluntario"=>$activityInstance->supplier_name,
                "DNI" =>$activityInstance->supplier_dni,
                "Estado"=>$activityInstance->status,
                "Pregunta" => $activityInstance->question_name,
                "Respuesta" => $activityInstance->answer,
                "Acción" =>$activityInstance->action_name,
                "Grupo de Actividades"=>$activityInstance->group_name,
                "Actividad" => $activityInstance->activity_name,
                "EMAIL" =>$activityInstance->supplier_email,
                "Teléfono" =>$activityInstance->supplier_phone,
                "Turno" => $activityInstance->appointment_id,
                "Estado del turno" => $activityInstance->appointment_action_name ,
                "Reclutador" =>$activityInstance->user_name,
                "Creado" =>Carbon::parse($activityInstance->created_at)->format(config('app.datetime_format')),
                "Actualizado" => Carbon::parse($activityInstance->updated_at)->format(config('app.datetime_format')),
                "Grupo del voluntario" => $activityInstance->supplier_group_name

            ];
        });



    }


    private function getActivitiesExportData($request){
        // Fetch records
        $activityInstancesQuery = ActivityInstance::Query();
        $activityInstancesQuery->leftJoin('appointments', 'appointments.id', '=', 'activity_instances.appointment_id');
        $activityInstancesQuery->leftJoin('appointment_actions', 'appointment_actions.id', '=', 'appointments.action_id');
        $activityInstancesQuery->leftJoin('suppliers', 'suppliers.id', '=', 'appointments.supplier_id');
        $activityInstancesQuery->leftJoin('supplier_groups', 'suppliers.supplier_group_id', '=', 'supplier_groups.id');
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
        $activityInstancesQuery->whereNotNull('activity_group_types.id');


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
            'appointment_actions.name as appointment_action_name',
            'supplier_groups.name as supplier_group_name'

        );
/*
        195762

        207+ 108438 + 42535 = 151.180*/


        foreach ($activityInstancesQuery->cursor() as $activityInstance){
            yield $activityInstance;
        }
    }


    public function suppliersExport(Request $request)
    {


        //Log::debug('export suppliers');
        $usersNames = User::all()->mapWithKeys(function ($user){return [$user->id=>$user->name];});
        $suppliersQuery = Supplier::query();
        $suppliersQuery->leftJoin('users', 'users.id', '=', 'suppliers.created_by');
        $suppliersQuery->leftJoin('users as recruiter', 'recruiter.id', '=', 'suppliers.recruiter_id');
        $suppliersQuery->leftJoin('schemes', 'schemes.id', '=', 'suppliers.scheme_id');
        $suppliersQuery->leftJoin('supplier_groups', 'supplier_groups.id', '=', 'suppliers.supplier_group_id');



        $supervised_users = User::where('supervisor_id', '=', auth()->user()->id)->get();
        if ($supervised_users->count()>0){$supervision_user = true;}

        if(auth()->user()->hasRole(User::ROLE_SCHEDULER)) {
            $suppliersQuery->where(function($query) {
                $query->where('suppliers.created_by', auth()->user()->id)
                    ->orWhere('suppliers.recruiter_id', auth()->user()->id);
            });
        }else if(auth()->user()->hasRole(User::ROLE_COORDINATOR) && $supervision_user){
            $suppliersQuery->whereIn('suppliers.recruiter_id', $supervised_users->pluck('id'));
        }

//        $suppliersQuery->where('deleted_at','!=', null);

        $suppliersQuery->select([
            'suppliers.is_intervened',
            'suppliers.status',
            'schemes.name as scheme',
            'supplier_groups.name as supplierGroup',
            'suppliers.wms_id',
            'suppliers.wms_name',
            'suppliers.wms_date',
            'suppliers.wms_gender',
            'suppliers.email',
            'suppliers.address',
            'suppliers.aux5',
            'suppliers.aux4',
            'suppliers.phone',
            'suppliers.contact',
            'suppliers.aux1',
            'suppliers.aux2',
            'suppliers.aux3',
            'suppliers.created_at',
            'suppliers.validate_address',
            'users.name as responsable',
            'recruiter.name as recruiterName',
            'suppliers.comorbidity',

        ]);

        $suppliers = $suppliersQuery->get();
        $now = now();
//        Log::debug($suppliers);
        return (new FastExcel($suppliers))->download('voluntarios-'.$now->format('YmdHis').'.xlsx',function ($supplier) {
            $status_text = $supplier->is_intervened == 1 ? 'Intervenido' : 'NO Intervenido';
            $addresValidate = $supplier->validate_address != null ? 'Si' : 'No';

            return [
                "Estado de intervención" => $status_text,
                "Estado del voluntario" => $supplier->status,
                "Esquema" => $supplier->scheme,
                "Grupo de voluntario" => $supplier->supplierGroup,
                "Dni" => $supplier->wms_id,
                "Apellido y nombre" => $supplier->wms_name,
                "Fecha de nacimiento" => $supplier->wms_date != '' ? Carbon::parse($supplier->getOriginal('wms_date'))->format('d/m/Y') : '',
                "Edad" => $supplier->wms_date != '' ? Carbon::parse($supplier->getOriginal('wms_date'))->age : '',
                "Género" => $supplier->wms_gender,
                "Email" => $supplier->email,
                "Dirección" => $supplier->address,
                "Piso/Depto" => $supplier->aux5,
                "Localidad" => $supplier->aux4,
                "Celular" => $supplier->phone,
                "Tel. Fijo" => $supplier->contact,
                "Nombre de contacto" => $supplier->aux1,
                "Tel. Contacto" => $supplier->aux2,
                "Comentarios" => $supplier->aux3,
                "Creado" => $supplier->created_at,
                "Direccón Validada" => $addresValidate,
                "Responsable" => $supplier->responsable,
                "Reclutador" => $supplier->recruiterName,
                "Comorbilidades" => $supplier->comorbidity == 1 ? 'SI' : 'NO',

            ];
        });





    }
    public function appointmentsExport(Request $request)
    {
        //Log::debug($request);

        $appointmentsQuery = Appointment::query();
        $appointmentsQuery->leftJoin('docks', 'docks.id', '=', 'appointments.dock_id');
        $appointmentsQuery->leftJoin('locations', 'docks.location_id', '=', 'locations.id');
        $appointmentsQuery->leftJoin('suppliers', 'suppliers.id', '=', 'appointments.supplier_id');
        $appointmentsQuery->leftJoin('schemes', 'schemes.id', '=', 'suppliers.scheme_id');
        $appointmentsQuery->leftJoin('appointment_actions', 'appointment_actions.id', '=', 'appointments.action_id');
        $appointmentsQuery->leftJoin('users as created_by', 'created_by.id', '=', 'appointments.created_by');
        $appointmentsQuery->leftJoin('users as updated_by', 'updated_by.id', '=', 'appointments.updated_by');
        $appointmentsQuery->leftJoin('users as original_created_by', 'original_created_by.id', '=', 'appointments.original_created_by');
        $appointmentsQuery->leftJoin('users as recruiter', 'recruiter.id', '=', 'suppliers.recruiter_id');


        $supervised_users = User::where('supervisor_id', '=', auth()->user()->id)->get();
        if ($supervised_users->count()>0){$supervision_user = true;}
        if(auth()->user()->hasRole(User::ROLE_SCHEDULER) ) {
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

        $appointmentsQuery->select([
            'appointments.id',
            'appointments.start_date',
            'docks.name as circuito',
            'suppliers.wms_name as voluntario',
            'suppliers.wms_id as dni',
            'locations.name as tipoVisita',
            'schemes.name as scheme',
            'appointment_actions.name as estadoTurno',
            'suppliers.phone as celular',
            'suppliers.aux2',
            'suppliers.email',
            'suppliers.address',
            'suppliers.validate_address',
            'appointments.transportation',
            'appointments.need_assistance',
            'appointments.next_step',
            'appointments.comments',
            'created_by.name as responsable',
            'appointments.created_at',
            'original_created_by.name as creadoPor',
            'appointments.updated_at',
            'updated_by.name as actualizadoPor',
            'recruiter.name as recruiterName',
            'suppliers.wms_date as nacimiemto',
            'suppliers.wms_date as edad',
            'suppliers.wms_gender as genero',
            'suppliers.comorbidity as comorbilidad',


        ]);

        $appointments = $appointmentsQuery->get();
        $now = now();
        return (new FastExcel($appointments))->download('Turnos-'.$now->format('YmdHis').'.xlsx',function ($appointment) {

            return [
                "Nro" => $appointment->id,
                "Fecha y hora" => $appointment->start_date,
                "Circuito" => $appointment->circuito,
                "Nombre del voluntario" => $appointment->voluntario,
                "DNI del voluntario" =>$appointment->dni,
                "Comorbilidades" => $appointment->comorbilidad == 1 ? 'SI' : 'NO',
                "Esquema" =>$appointment->scheme,
                "Tipo de visita" => $appointment->tipoVisita ?? '',
                "Estado" => $appointment->estadoTurno,
                "Telefono-celular" => $appointment->celular,
                "Telefono Emergencia" => $appointment->aux2,
                "Email voluntario" => $appointment->email,
                "Dirección voluntario" => $appointment->address,
                "Dirección validada" => $appointment->validate_address,
                "Transporte" => $appointment->transportation,
                "Asistencia" => $appointment->need_assistance == 1 ? 'SI' : 'NO',
                "Proxima Acción" => $appointment->next_step,
                "Comentarios" => $appointment->comments,
                "Responsable" => $appointment->responsable,
                "Creado"=> $appointment->created_at,
                "Creado por"=> $appointment->creadoPor,
                "Modificado"=> $appointment->updated_at,
                "Actualizado por"=> $appointment->actualizadoPor,
                "Dueño del voluntario"=> $appointment->recruiterName,
                "Fecha de nacimiento"=> $appointment->nacimiemto != '' ? Carbon::parse($appointment->getOriginal('nacimiemto'))->format('d/m/Y') : '',
                "Edad"=> $appointment->edad != '' ? Carbon::parse($appointment->getOriginal('edad'))->age : '',
                "Genero"=> $appointment->genero

            ];
        });




    }
    public function intervenedExport(Request $request)
    {
        //Log::debug($request);

        $supplierInterventionQuery = SupplierInterventionLog::Query();
        $supplierInterventionQuery->leftJoin('suppliers', 'suppliers.id', '=', 'supplier_intervention_logs.supplier_id');
        $supplierInterventionQuery->leftJoin('users', 'users.id', '=', 'supplier_intervention_logs.created_by');

        $supplierInterventionQuery->select([
            'supplier_intervention_logs.id',
            'suppliers.wms_name as voluntario',
            'suppliers.wms_id as dni',
            'supplier_intervention_logs.intervention_reason as razonIntervencion',
            'users.name as creador',
            'supplier_intervention_logs.created_at'
        ]);

        $supplierInterventions = $supplierInterventionQuery->get();
        $now = now();
        return (new FastExcel($supplierInterventions))->download('Registro de intervencion-'.$now->format('YmdHis').'.xlsx',function ($supplierIntervention) {

            return [
                  "Id" => $supplierIntervention->id,
                  "Voluntario" => $supplierIntervention->voluntario ,
                  "Dni" =>  $supplierIntervention->dni,
                  "Razón internvención" => $supplierIntervention->razonIntervencion ,
                  "Creado por" => $supplierIntervention->creador ,
                  "Fecha de creación" =>$supplierIntervention->created_at != '' ? Carbon::parse($supplierIntervention->getOriginal('created_at'))->format('d/m/Y') : '' ,


            ];
        });


    }

}
