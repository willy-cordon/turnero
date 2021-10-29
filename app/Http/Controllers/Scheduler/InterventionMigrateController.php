<?php

namespace App\Http\Controllers\Scheduler;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduler\InterventionMigrateRequest;
use App\Models\ActivityInstance;
use App\Models\Appointment;
use App\Models\Notification;
use App\Models\Supplier;
use App\Models\SupplierInterventionLog;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InterventionMigrateController extends Controller
{

    public function index()
    {
        $supplierInterventions = Supplier::where('is_intervened','=',true)->get();
        $userDoctors = User::whereHas("roles", function($q){ $q->whereIn("id", [User::ROLE_DOCTOR, User::ROLE_DOCTOR_ADMIN]); })->get();

        return view('scheduler.intervention-migrate.migration', compact('supplierInterventions','userDoctors'));
    }

    public function migrationIntervention(InterventionMigrateRequest $request)
    {

        $user_id = $request->get('user_id');
        $supplier_id = $request->get('supplier_id');

        $supplierMigrate = Supplier::query();
        $supplierMigrate ->where('id','=', $supplier_id);
        $supplierMigrate ->update(['created_by'=>$user_id]);

        $userDoctor = User::where('id', '=' , $user_id)->first();
        $appointments = Appointment::where('supplier_id',$supplier_id)->get();
        foreach ($appointments as $appointment) {

            $appointment->update(['created_by'=>$user_id]);
        }
        $activityInstancesQuery = ActivityInstance::Query();

        $activityInstancesQuery->leftJoin('appointments', 'appointments.id', '=', 'activity_instances.appointment_id');
        $activityInstancesQuery->where('appointments.supplier_id', $supplier_id);
        $activityInstances = $activityInstancesQuery->select('activity_instances.*')->get();


        foreach ($activityInstances as $activityInstance) {
            $activityInstance->update(['created_by'=>$user_id]);
        }

        $description = '<b>'.auth()->user()->name.'</b>'.' migró al voluntario intervenido a: '.'<b> '.$userDoctor->name.'</b> ';

        $interventionLog = SupplierInterventionLog::create([
            'description' => $description,
            'intervention_reason' => SupplierInterventionLog::MIGRATION
        ]);
        $interventionLog->supplier()->associate(Supplier::find($supplier_id))->save();



        $subject = 'Voluntario migrado por: ' . auth()->user()->name. ' a '.$userDoctor->name ;
        //Creamos la notificacion
        $notificationCreate = Notification::create([
            'email_subject' => $subject,
            'type' => Notification::MIGRATION,
            'created_by' => $userDoctor->id
        ]);
        $notificationCreate->supplier()->associate(Supplier::find($supplier_id))->save();


        $supplierInterventions = Supplier::where('is_intervened','=',true)->get();
        $userDoctors = User::whereHas("roles", function($q){ $q->where("name",'=', "doctor"); })->get();

        return redirect()->route('scheduler.intervention-migrate.migrations', compact('supplierInterventions','userDoctors'));

    }


}
