<?php

namespace App\Http\Controllers\Scheduler;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduler\ActivityMigrationAppointmentRequest;
use App\Http\Requests\Scheduler\ActivityMigrationUserRequest;
use App\Models\ActivityInstance;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ActivityAdminTools extends Controller
{
    public function index()
    {

        $userIdsWithActivityInstancesQuery = ActivityInstance::Query();
        $userIdsWithActivityInstancesQuery->groupBy('created_by');
        $userIdsWithActivityInstancesQuery->select('created_by');
        $userIdsWithActivityInstances = $userIdsWithActivityInstancesQuery->get()->pluck('created_by');

        $usersWithActivityInstances = User::whereIn('id',$userIdsWithActivityInstances )->get()->unique();

        $users = User::all();
        return view('scheduler.activities-admin-tools.migrations', compact('users','usersWithActivityInstances'));
    }

    public function updateMigrationAppointment(ActivityMigrationAppointmentRequest $request)
    {
        $userIdsWithActivityInstancesQuery = ActivityInstance::Query();
        $userIdsWithActivityInstancesQuery->groupBy('created_by');
        $userIdsWithActivityInstancesQuery->select('created_by');
        $userIdsWithActivityInstances = $userIdsWithActivityInstancesQuery->get()->pluck('created_by');

        $usersWithActivityInstances = User::whereIn('id',$userIdsWithActivityInstances )->get()->unique();

        $users = User::all();

        $appointmentQuery = ActivityInstance::Query();
        $appointmentQuery->where('appointment_id','=',$request->get('appointment_id'));
        $appointmentQuery->update(['created_by'=>$request->get('users_migration')]);

        return redirect()->route('scheduler.activities-admin-tools.migrations', compact('users','usersWithActivityInstances'));

    }

    public function updateMigrationUser(ActivityMigrationUserRequest $request)
    {
        $userIdsWithActivityInstancesQuery = ActivityInstance::Query();
        $userIdsWithActivityInstancesQuery->groupBy('created_by');
        $userIdsWithActivityInstancesQuery->select('created_by');
        $userIdsWithActivityInstances = $userIdsWithActivityInstancesQuery->get()->pluck('created_by');

        $usersWithActivityInstances = User::whereIn('id',$userIdsWithActivityInstances )->get()->unique();

        $users = User::all();
        $createdByQuery = ActivityInstance::Query();
        $createdByQuery->where('created_by', '=', $request->get('userFrom') );
        $createdByQuery->update(['created_by'=>$request->get('userTo')]);

        return redirect()->route('scheduler.activities-admin-tools.migrations',compact('usersWithActivityInstances','users'));

    }

}
