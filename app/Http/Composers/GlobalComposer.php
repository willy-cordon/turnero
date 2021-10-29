<?php

namespace App\Http\Composers;

use App\Models\ActivityGroupType;
use App\Models\ActivityInstance;
use App\Models\Appointment;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Supplier;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Class GlobalComposer.
 */
class GlobalComposer
{
    /**
     * Bind data to the view.
     *
     * @param View $view
     */
    public function compose(View $view)
    {
        $view->with('logged_in_user', auth()->user());
        $view->with('all_locations', Location::get(['id', 'name']));
        $unread_notifications = -1;
        if(auth()->user() != null && (auth()->user()->hasRole(User::ROLE_SCHEDULER) || auth()->user()->hasRole(User::ROLE_DOCTOR) )) {
            $unread_notifications = Notification::where('status','=','0')->where('notifications.created_by', auth()->user()->id)->count();
        }
        $view->with('unread_notifications',$unread_notifications);

        // Notificaciones Activity group type \\
        //Ediary
        $countEDiaryAll = $countEDiaryPending = $countEDiaryInProgress = $countEDiaryExpired = -1;
        $countTurnosAll = $countTurnosPending = $countTurnosInProgress = $countTurnosExpired = -1;
        $countVigilanciaAll = $countVigilanciaPending = $countVigilanciaInProgress = $countVigilanciaExpired = -1;

       /* $temp_all_ed = $temp_all_t = $temp_all_v = 0;

        $countersQuery = ActivityInstance::query();
        $countersQuery->leftJoin('activities','activities.id','=','activity_instances.activity_id');
        $countersQuery->leftJoin('activity_groups','activity_groups.id','=','activities.activity_group_id');
        $countersQuery->leftJoin('activity_group_types','activity_groups.activity_group_type_id','=','activity_group_types.id');
        $countersQuery->leftJoin('appointments', 'activity_instances.appointment_id', '=', 'appointments.id');
        $countersQuery->where('appointments.action_id', '!=', Appointment::STATUS_CANCELED);
        $countersQuery->where('activity_instances.status', '!=', ActivityInstance::STATUS_CANCEL);
        $countersQuery->select('activity_group_types.name','activity_instances.status',DB::raw('count(*) as total'));
        $countersQuery->groupBy('activity_group_types.name', 'activity_instances.status');

        foreach ($countersQuery->get() as $counter){
            if($counter->name == ActivityInstance::GROUP_TYPE_EDIARY){
                $temp_all_ed += $counter->total;
                if($counter->status == ActivityInstance::STATUS_IN_PROGRESS) $countEDiaryInProgress = $counter->total;
                if($counter->status == ActivityInstance::STATUS_TODO) $countEDiaryPending = $counter->total;

            }
            if($counter->name == ActivityInstance::GROUP_TYPE_TURNOS){
                $temp_all_t += $counter->total;
            }
            if($counter->name == ActivityInstance::GROUP_TYPE_VIGILANCIA){
                $temp_all_v += $counter->total;
            }

        }
        $countEDiaryAll=$temp_all_ed;
        $countTurnosAll = $temp_all_t;
        $countVigilanciaAll = $temp_all_v;
*/
        if (auth()->user() != null )
        {

//            $eDiaryId = ActivityGroupType::where('name',ActivityInstance::GROUP_TYPE_EDIARY)->first();
//            $eDiaryAll = ActivityInstance::query();
//            $eDiaryAll ->leftJoin('activities','activities.id','=','activity_instances.activity_id');
//            $eDiaryAll ->leftJoin('activity_groups','activity_groups.id','=','activities.activity_group_id');
//            $eDiaryAll ->leftJoin('activity_group_types','activity_group_types.id','=','activity_groups.activity_group_type_id');
//            $eDiaryAll ->leftJoin('appointments','appointments.id','=','activity_instances.appointment_id');
//            $eDiaryAll ->leftJoin('suppliers','suppliers.id','=','appointments.supplier_id');
//            $eDiaryAll ->where('activity_group_types.id',$eDiaryId->id);
//            $eDiaryAll ->where('suppliers.status','!=',Supplier::STATUS_TWO);
//            $eDiaryAll->where('appointments.action_id', '!=', Appointment::STATUS_CANCELED);
//            $eDiaryAll->selectRaw('count(activity_instances.id) as totalEdiary');
//            $countEdiaryAll = $eDiaryAll ->count();
//
//            $eDiaryPending = ActivityInstance::query();
//            $eDiaryPending ->leftJoin('activities','activities.id','=','activity_instances.activity_id');
//            $eDiaryPending ->leftJoin('activity_groups','activity_groups.id','=','activities.activity_group_id');
//            $eDiaryPending ->leftJoin('activity_group_types','activity_group_types.id','=','activity_groups.activity_group_type_id');
//            $eDiaryPending ->leftJoin('appointments','appointments.id','=','activity_instances.appointment_id');
//            $eDiaryPending ->leftJoin('suppliers','suppliers.id','=','appointments.supplier_id');
//            $eDiaryPending ->where('activity_group_types.id',$eDiaryId->id);
//            $eDiaryPending ->where('suppliers.status','!=',Supplier::STATUS_TWO);
//            $eDiaryPending->where('appointments.action_id', '!=', Appointment::STATUS_CANCELED);
//            $eDiaryPending->where('activity_instances.date', '<=', now() );
//            $eDiaryPending->whereIn('activity_instances.status', [ActivityInstance::STATUS_TODO]);
//            $eDiaryPending->selectRaw('count(activity_instances.date) as totalPending');
//            $countEdiaryPending = $eDiaryPending ->count();
//
//            $eDiaryInProgress = ActivityInstance::query();
//            $eDiaryInProgress ->leftJoin('activities','activities.id','=','activity_instances.activity_id');
//            $eDiaryInProgress ->leftJoin('activity_groups','activity_groups.id','=','activities.activity_group_id');
//            $eDiaryInProgress ->leftJoin('activity_group_types','activity_group_types.id','=','activity_groups.activity_group_type_id');
//            $eDiaryInProgress ->leftJoin('appointments','appointments.id','=','activity_instances.appointment_id');
//            $eDiaryInProgress ->leftJoin('suppliers','suppliers.id','=','appointments.supplier_id');
//            $eDiaryInProgress ->where('activity_group_types.id',$eDiaryId->id);
//            $eDiaryInProgress ->where('suppliers.status','!=',Supplier::STATUS_TWO);
//            $eDiaryInProgress->where('appointments.action_id', '!=', Appointment::STATUS_CANCELED);
//            $eDiaryInProgress->whereIn('activity_instances.status', [ActivityInstance::STATUS_IN_PROGRESS]);
//            $eDiaryInProgress->selectRaw('count(activity_instances.status) as totalInProgress');
//            $countEdiaryInProgress = $eDiaryInProgress ->count();
//
//            $eDiaryExpired = ActivityInstance::query();
//            $eDiaryExpired ->leftJoin('activities','activities.id','=','activity_instances.activity_id');
//            $eDiaryExpired ->leftJoin('activity_groups','activity_groups.id','=','activities.activity_group_id');
//            $eDiaryExpired ->leftJoin('activity_group_types','activity_group_types.id','=','activity_groups.activity_group_type_id');
//            $eDiaryExpired ->leftJoin('appointments','appointments.id','=','activity_instances.appointment_id');
//            $eDiaryExpired ->leftJoin('suppliers','suppliers.id','=','appointments.supplier_id');
//            $eDiaryExpired ->where('activity_group_types.id',$eDiaryId->id);
//            $eDiaryExpired ->where('suppliers.status','!=',Supplier::STATUS_TWO);
//            $eDiaryExpired ->where('appointments.action_id', '!=', Appointment::STATUS_CANCELED);
//            $eDiaryExpired->where('activity_instances.date', '<', now() );
//            $eDiaryExpired->whereIn('activity_instances.status', [ActivityInstance::STATUS_TODO, ActivityInstance::STATUS_IN_PROGRESS]);
//            $eDiaryExpired->selectRaw('count(activity_instances.created_by) as totalExpired');
//            $countEdiaryExpired = $eDiaryExpired ->count();

        }
        $view->with('countEdiaryAll',$countEDiaryAll);
        $view->with('countEdiaryPending',$countEDiaryPending);
        $view->with('countEdiaryInProgress',$countEDiaryInProgress);
        $view->with('countEdiaryExpired',$countEDiaryExpired);



    }
}
