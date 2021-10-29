<?php

namespace App\Http\Controllers\Scheduler;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * @var NotificationService
     */
    private $notificationService;

    public function __construct(NotificationService $service)
    {
        $this->notificationService = $service;
    }

    public function index(){
        $notificationsAll ='';
        $notificationsRead='';
        $supervision_user = false;
        $supervised_users = User::where('supervisor_id', '=', auth()->user()->id)->get();
        if ($supervised_users->count()>0){$supervision_user = true;}

        if(auth()->user()->hasRole(User::ROLE_SCHEDULER) || auth()->user()->hasRole(User::ROLE_DOCTOR)  ) {

            $notificationsAll = Notification::select('count(*) as allcount')->where('notifications.created_by', auth()->user()->id)->count();
            $notificationsRead = Notification::where('status','=','1')->where('notifications.created_by', auth()->user()->id)->count();

        }else if (auth()->user()->hasRole(User::ROLE_COORDINATOR) && $supervision_user){

            $notificationsAll = Notification::select('count(*) as allcount')->whereIn('notifications.created_by', $supervised_users->pluck('id'))->count();
            $notificationsRead = Notification::where('status','=','1')->count();

        }else{

            $notificationsAll = Notification::select('count(*) as allcount')->count();
            $notificationsRead = Notification::where('status','=','1')->count();

        }



        return view('scheduler.notifications-email.index', compact('notificationsAll','notificationsRead'));
    }

    public function getNotifications(Request $request)
    {
        return $this->notificationService->getDataTables($request);
    }

    public function setStatus(Request $request)
    {

        $status = $request->get('status');
        $status == '1' ? $status = '0' : $status = '1';

        $notificationQuery = Notification::query();
        $notificationQuery->where('id','=',$request->get('id'));
        $notificationQuery->update(['status'=> $status]);

        $supervision_user = false;
        $supervised_users = User::where('supervisor_id', '=', auth()->user()->id)->get();
        if ($supervised_users->count()>0){$supervision_user = true;}

        if(auth()->user()->hasRole(User::ROLE_SCHEDULER) || auth()->user()->hasRole(User::ROLE_DOCTOR) ) {

            $notificationsAll = Notification::select('count(*) as allcount')->where('notifications.created_by', auth()->user()->id)->count();
            $notificationsRead = Notification::where('status','=','1')->where('notifications.created_by', auth()->user()->id)->count();

        }else if (auth()->user()->hasRole(User::ROLE_COORDINATOR) && $supervision_user){

            $notificationsAll = Notification::select('count(*) as allcount')->whereIn('notifications.created_by', $supervised_users->pluck('id'))->count();
            $notificationsRead = Notification::where('status','=','1')->count();

        }else{

            $notificationsAll = Notification::select('count(*) as allcount')->count();
            $notificationsRead = Notification::where('status','=','1')->count();

        }
        //Log::debug($notificationsAll);
        //Log::debug($notificationsRead);

        if ($notificationQuery){
            return ["status"=>"ok","notificationAll" => $notificationsAll, "notificationRead" => $notificationsRead];
        }


    }


}
