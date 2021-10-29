<?php

namespace App\Services;

use App\Models\AppointmentChangeLog;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Zendaemon\Services\Service;
use Zendaemon\Services\Traits\{CreateModel, DestroyModel, ReadModel, UpdateModel};

final class AppointmentChangeLogService extends Service
{
    use CreateModel, ReadModel, UpdateModel, DestroyModel;

    /**
     * Set model class name.
     *
     * @return void
     */
    protected function setModel(): void
    {
        $this->model = AppointmentChangeLog::class;
    }

    public function getDataTables(Request $request)
    {
        ## Read value
        $draw            = $request->get('draw');
        $start           = $request->get("start");
        $rowperpage      = $request->get("length");
        $columnIndex_arr = $request->get('order');
        $columnName_arr  = $request->get('columns');
        $order_arr       = $request->get('order');
        $search_arr      = $request->get('search');
        $columnIndex     = $columnIndex_arr[0]['column'];
        $dateInit        = $request->get('dataInit');
        $dateEnd         = $request->get('dataEnd');
        $datetime_columns = ['appointments.start_date', 'appointment_change_logs.updated_at'];
        $data_arr = array();
        $responseControl = array(
            "draw" => intval($draw),
            "iTotalRecords" => 0,
            "iTotalDisplayRecords" => 0,
            "aaData" => $data_arr
        );
        if ($dateInit == null && $dateEnd == null){return $responseControl;}

        $usersNames = User::all()->mapWithKeys(function ($user) {
            return [$user->id => $user->name];
        });

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
        $totalRecords = '';
        // Total records
        $totalRecordsQuery = AppointmentChangeLog::query();
        $totalRecordsQuery  ->select('count(*) as allcount')->count();


        $supervision_user = false;
        $supervised_users = User::where('supervisor_id', '=', auth()->user()->id)->get();
        if ($supervised_users->count()>0){$supervision_user = true;}
        // Fetch records
        $appointmentChangeLogQuery = AppointmentChangeLog::Query();
        $appointmentChangeLogQuery->leftJoin('appointments', 'appointments.id', '=', 'appointment_change_logs.appointment_id');
        $appointmentChangeLogQuery->leftJoin('suppliers', 'suppliers.id', '=', 'appointments.supplier_id');
        $appointmentChangeLogQuery->leftJoin('users as updated_by', 'updated_by.id', '=', 'appointments.updated_by');
        $appointmentChangeLogQuery->leftJoin('appointment_actions', 'appointment_actions.id', '=', 'appointments.action_id');


        $dateI = Carbon::createFromFormat(config('app.datetime_format'), $dateInit);
        $dateE = Carbon::createFromFormat(config('app.datetime_format'), $dateEnd);

        $appointmentChangeLogQuery ->whereBetween('appointment_change_logs.created_at', [$dateI,$dateE])->get();

        $totalRecords = $totalRecordsQuery->count();


        foreach ($totalColum as $column => $value ){

            $db_column = str_replace('-', '.',$column);
            if (in_array($db_column, $datetime_columns)) {
                $db_column = DB::raw("DATE_FORMAT(" . $db_column . ",'%d/%m/%Y %H:%i')");
            }
            $appointmentChangeLogQuery->where($db_column, 'like', '%' .$value . '%');

        }

        $appointmentChangeLogQuery->select('appointment_change_logs.*');
        $appointmentChangeLogQuery->orderBy($columnName,$columnSortOrder);

        $totalRecordswithFilter = $appointmentChangeLogQuery->get()->count();

        if($rowperpage != -1){
            $appointmentChangeLogQuery->skip($start);
            $appointmentChangeLogQuery->take($rowperpage);
        }
        $appointmentChangeLogs = $appointmentChangeLogQuery->get();


        foreach($appointmentChangeLogs as $appointmentChangeLog){

            if(!empty($appointmentChangeLog->appointment)) {
                $data_arr[] = array(
                    "any" => '',
                    "appointments-id" => $appointmentChangeLog->appointment_id ?? '',
                    "appointments-start_date" => $appointmentChangeLog->appointment->start_date ?? '',
                    "appointment_actions-name" => $appointmentChangeLog->field_value_text ?? '',
                    "appointment_change_logs-field_name" => $appointmentChangeLog->field_name ?? '',
                    "suppliers-wms_name" => $appointmentChangeLog->appointment->supplier->wms_name ?? '',
                    "suppliers-wms_id" => $appointmentChangeLog->appointment->supplier->wms_id ?? '',
                    "suppliers-phone" => $appointmentChangeLog->appointment->supplier->phone ?? '',
                    "suppliers-address" => $appointmentChangeLog->appointment->supplier->address . ' ' . $appointmentChangeLog->appointment->supplier->aux5 . ', ' . $appointmentChangeLog->appointment->supplier->aux4,
                    "suppliers-validate_address" => $appointmentChangeLog->appointment->supplier->validate_address,
                    "updated_by-name" => Arr::exists($usersNames,$appointmentChangeLog->created_by) ? $usersNames[$appointmentChangeLog->created_by] : '',
                    "appointment_change_logs-updated_at" => Carbon::parse($appointmentChangeLog->updated_at)->format(config('app.datetime_format')),


                );
            }
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
