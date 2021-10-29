<?php

namespace App\Http\Controllers\Scheduler;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduler\AppointmentAdminToolsMigrationRequest;
use App\Models\Activity;
use App\Models\ActivityInstance;
use App\Models\Appointment;
use App\Models\Supplier;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AppointmentAdminToolsController extends Controller
{

    public function index(){
        $suppliers = Supplier::all();
        $users = User::all();

        return view('scheduler.appointment-admin-tools.migrations', compact('suppliers', 'users'));

    }

    public function updateMigrationSupplier(AppointmentAdminToolsMigrationRequest $request)
    {

        $supplier = Supplier::Query()->where('id', '=', $request->get('supplier_id'))->first();

        if($supplier->is_intervened == true){
            $supplierQuery = Supplier::Query();
            $supplierQuery->where('id', '=', $request->get('supplier_id'));
            $supplierQuery->update(['recruiter_id' => $request->get('user_id')]);
        }else {
            $supplierQuery = Supplier::Query();
            $supplierQuery->where('id', '=', $request->get('supplier_id'));
            $supplierQuery->update(['created_by' => $request->get('user_id')]);
            $supplierQuery->update(['recruiter_id' => $request->get('user_id')]);

            $appointmentQuery = Appointment::Query();
            $appointmentQuery->where('supplier_id', '=', $request->get('supplier_id'));
            $appointmentQuery->update(['created_by' => $request->get('user_id')]);

            $activitiesQuery = ActivityInstance::Query();
            $activitiesQuery->leftJoin('appointments', 'appointments.id', '=', 'activity_instances.appointment_id');
            $activitiesQuery->leftJoin('suppliers', 'suppliers.id', '=', 'appointments.supplier_id');
            $activitiesQuery->where('suppliers.id', '=', $request->get('supplier_id'));
            $activitiesQuery->update(['activity_instances.created_by' => $request->get('user_id')]);
        }
        return redirect()->route('scheduler.appointment-admin-tools.migrations');
    }










}
