<?php

namespace App\Services;

use App\Models\Notification;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\DocBlock\Tags\Author;
use Zendaemon\Services\Service;
use Zendaemon\Services\Traits\{CreateModel, DestroyModel, ReadModel, UpdateModel};

final class NotificationService extends Service
{
    use CreateModel, ReadModel, UpdateModel, DestroyModel;

    /**
     * Set model class name.
     *
     * @return void
     */
    protected function setModel(): void
    {
        $this->model = Notification::class;
    }



    public function getDataTables(Request $request)
    {
        $usersNames = User::all()->mapWithKeys(function ($user) {
            return [$user->id => $user->name];
        });
        ## Read value
        $draw            = $request->get('draw');
        $start           = $request->get("start");
        $rowperpage      = $request->get("length");
        $columnIndex_arr = $request->get('order');
        $columnName_arr  = $request->get('columns');
        $order_arr       = $request->get('order');
        $search_arr      = $request->get('search');
        $columnIndex     = $columnIndex_arr[0]['column'];

        $datetime_columns = ['notifications.created_at'];

        $totalColum = [];
        if ( isset( $columnName_arr ) ) {
            for ( $i=0, $ien=count($columnName_arr) ; $i<$ien ; $i++ ) {
                $requestColumn = $request['columns'][$i];

                if(!$requestColumn['search']['value'] == NULL){
                    $totalColum [$requestColumn['data']] =$requestColumn['search']['value'];
                }
            }
        }

        $columnName = str_replace('-', '.',$columnName_arr[$columnIndex]['data']);
        $columnSortOrder = $order_arr[0]['dir'];

        // Total records
//        $totalRecords = Notification::select('count(*) as allcount')->count();

        $notificationQuery = Notification::Query();
        $notificationQuery->leftJoin('suppliers', 'suppliers.id', '=', 'notifications.supplier_id');
        $notificationQuery->leftJoin('users as created_by', 'created_by.id', '=', 'notifications.created_by');

        $supervision_user = false;
        $supervised_users = User::where('supervisor_id', '=', auth()->user()->id)->get();
        if ($supervised_users->count()>0){$supervision_user = true;}
//        Log::debug($supervision_user);

        if(auth()->user()->hasRole(User::ROLE_SCHEDULER) || auth()->user()->hasRole(User::ROLE_DOCTOR)) {

            $notificationQuery->where('notifications.created_by', auth()->user()->id);
            $totalRecords = Notification::select('count(*) as allcount')->where('notifications.created_by', auth()->user()->id)->count();
        }else if (auth()->user()->hasRole(User::ROLE_COORDINATOR) && $supervision_user){

            $notificationQuery->whereIn('notifications.created_by', $supervised_users->pluck('id'));
            $totalRecords = Notification::select('count(*) as allcount')->whereIn('notifications.created_by', $supervised_users->pluck('id'))->count();
        }else{
            //En el caso de que no sea scheduler o no sea un coordinador con supervisados, mostramos todos
            $totalRecords = Notification::select('count(*) as allcount')->count();
        }


        foreach ($totalColum as $column => $value ){
            $db_column = str_replace('-', '.',$column);

            if (in_array($db_column, $datetime_columns)) {
                $db_column = DB::raw("DATE_FORMAT(" . $db_column . ",'%d/%m/%Y %H:%i')");
            }
            $notificationQuery->where($db_column, 'like', '%' .$value . '%');
        }


        $notificationQuery->select('notifications.*');
        $notificationQuery->orderBy($columnName,$columnSortOrder);

        $totalRecordswithFilter = $notificationQuery->get()->count();
        if($rowperpage != -1){
            $notificationQuery->skip($start);
            $notificationQuery->take($rowperpage);
        }
        $notifications = $notificationQuery->get();

        $data_arr = array();
        $sno = $start+1;


        foreach($notifications as $notification){
            $disable = '';
            $supervision_user = false;
            $supervised_users = User::where('supervisor_id', '=', auth()->user()->id)->get();
            if ($supervised_users->count()>0){$supervision_user = true;}


            $statusText = '';
            $buttonText='';
            if($notification->status == 0){
                $buttonEye ='<button class="btn btn-xs btn-danger buttonEye"  data-status = "'.$notification->status.'" title="'.trans('global.show').'" onclick="getStatus('.$notification->id.','.$notification->status.')" '.$disable.'><i class="fas fa-eye-slash"></i></button>';

                $subjectText = '<p class="font-weight-bold subjectText" data-status = "'.$notification->id.'"> '.$notification->email_subject.' </p>';
                $statusText = '<p class="statusDanger ai-status"> No leido </p>';
            }else{
                $buttonEye ='<button class="btn btn-xs btn-success buttonEye" data-status = "'.$notification->status.'" title="'.trans('global.show').'" onclick="getStatus('.$notification->id.','.$notification->status.')" '.$disable.'><i class="fas fa-eye"></i></button>';

                    $subjectText = '<p class= "font-weight-normal subjectText" data-status = "'.$notification->id.'"> '.$notification->email_subject.' </p>';
                $statusText = '<p class="statusSuccess ai-status"> Leido </p>';

            }

            $actionButton = '';

            if (auth()->user()->hasRole(User::ROLE_ADMIN) || auth()->user()->hasRole(User::ROLE_SCHEDULER_ADMIN) || auth()->user()->hasRole(User::ROLE_COORDINATOR) ){$buttonText = '<p> '.$statusText.' </p>'; $actionButton = $buttonText;}
            elseif (auth()->user()->hasRole(User::ROLE_COORDINATOR) && $supervision_user){$buttonText = '<p> '.$statusText.' </p>';$actionButton = $buttonText;}
            else{
                $actionButton = $buttonEye;
            }
//            $actionButton = $buttonEye.$buttonText;
            $data_arr[] = array(
                "any"                     => '',
                "notifications-status"     => $actionButton,
                "notifications-created_at"   => $notification->created_at->format(config('app.datetime_format')) ?? '',
                "notifications-email_subject"   => $subjectText ?? '',
                "suppliers-wms_name"    => $notification->supplier->wms_name ?? '',
                "suppliers-wms_id" => $notification->supplier->wms_id ?? '',
                "notifications-type"      => $notification->type ?? '',
                "notification-status" => $notification->status,
                "notification-id" => $notification->id,
                "created_by-name"=> Arr::exists($usersNames, $notification->created_by) ? $usersNames[$notification->created_by] : ''


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
}
