<?php

namespace App\Services;

use App\Models\Dock;
use App\Models\Location;
use App\Models\SchedulerCellLock;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Zendaemon\Services\Service;
use Zendaemon\Services\Traits\{CreateModel, DestroyModel, ReadModel, UpdateModel};

final class SchedulerCellLockService extends Service
{
    use CreateModel, ReadModel, UpdateModel, DestroyModel;

    /**
     * Set model class name.
     *
     * @return void
     */
    protected function setModel(): void
    {
        $this->model = SchedulerCellLock::class;
    }

    public function create(Request $request): Model
    {

        $formatDate = '';

        $cellLockDate =  Carbon::createFromFormat(config('app.date_format'), $request->get('lock_date'));
        $formatDate = $cellLockDate->format('Ymd');
        $requestType ='';
        if ($request->get('lock_type') == SchedulerCellLock::TYPE_LOCKS_CELL){
            $requestType = $formatDate.'_'.$request->get('dock_name').'-'.$request->get('hour');
        }
        if ($request->get('lock_type') == SchedulerCellLock::TYPE_LOCKS_DOCK){
            $requestType = $formatDate.'_'.'R'.'-'.$request->get('dock_name');
        }
        if ($request->get('lock_type') == SchedulerCellLock::TYPE_LOCKS_HOUR){
            $requestType = $formatDate.'_'.'C'.'-'.$request->get('hour');

        }

            $schedulerCellLock = $this->model::create([
               'lock_date'=> $cellLockDate->format('Y-m-d H:i:s'),
                'lock_type' => $request->get('lock_type'),
                'lock_key'  => $requestType,
                'hour'      =>$request->get('hour_key'),
                'dock_name' =>$request->get('dock_key')
            ]);

        $schedulerCellLock->location()->associate(Location::find($request->get('location_id')))->save();

        return $schedulerCellLock;

    }

    public function dataTablesCellLocks(Request $request)
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

        $columnName = str_replace('-', '.',$columnName_arr[$columnIndex]['data']);
        $columnSortOrder = $order_arr[0]['dir'];
        $searchValue = $search_arr['value'];
        // Total records
        $totalRecords = SchedulerCellLock::select('count(*) as allcount')->count();

        $cellLockQuery = SchedulerCellLock::Query();
//        $cellLockQuery->leftJoin('locations', 'location_id', '=', 'scheduler_cell_locks.location_id');
        $cellLockQuery->orWhere('scheduler_cell_locks.lock_type', 'like', '%' .$searchValue . '%');
        $cellLockQuery->orWhere('scheduler_cell_locks.dock_name', 'like', '%' .$searchValue . '%');
        $cellLockQuery->orWhere('scheduler_cell_locks.lock_date', 'like', '%' .$searchValue . '%');
        $cellLockQuery->orWhere('scheduler_cell_locks.hour', 'like', '%' .$searchValue . '%');


        $cellLockQuery->select('scheduler_cell_locks.*');
        $cellLockQuery->orderBy($columnName,$columnSortOrder);

        $totalRecordswithFilter = $cellLockQuery->get()->count();

        if($rowperpage != -1){
            $cellLockQuery->skip($start);
            $cellLockQuery->take($rowperpage);
        }
        $cellLocks = $cellLockQuery->get();

        $data_arr = array();

        foreach ($cellLocks as $cellLock)
        {

            $buttonView = '<button type="button"  class="btn btn-xs btn-danger" onclick="$(\'#delete_form\').attr(\'action\', \' '. route('scheduler.cell-locks.destroy', $cellLock->id) .'\' );$(\'.delete-confirm-submit\').modal(\'show\')" title=" '. trans('global.delete').' "><i class=\'fas fa-trash\'></i> </button>';

            $data_arr[] = array(
                "any" => '',
                "scheduler_cell_locks-lock_type"    => $cellLock->lock_type ?? '',
                "scheduler_cell_locks-dock_name"    => $cellLock->dock_name ?? '',
                "scheduler_cell_locks-lock_date"    => $cellLock->lock_date != '' ? Carbon::parse($cellLock->getOriginal('lock_date'))->format('d/m/Y') : '',
                "scheduler_cell_locks-hour"         => $cellLock->hour != '' ? $cellLock->hour : ''  ,
                "location"                          => $cellLock->location()->pluck('name'),
                'button-action'                     => $buttonView
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

    public function getHours(Location $location)
    {
        $initHour = $location->init_hour;
        $endHour  = $location->end_hour;
        $minutes_size = $location->appointment_init_minutes_size;
        $totalHour = $endHour - $initHour  ;
        $totalLoop = $totalHour * 60 / $minutes_size;

        $hoursArray= [];
        $hourSize = $minutes_size / 60;
        for ($i=0; $i <= $totalLoop; $i++ ){

            $valHour = $hourSize * $i;
            $valHour1 =$initHour + $valHour ;
            $convertHour = $valHour1 * 60;

            $formatHour = gmdate("i:s", $convertHour);

            $hoursArray [$i] = $formatHour;
        }

        return $hoursArray;
    }

    public function getDocks($location)
    {
        $docksQuery = Dock::where('location_id', '=', $location->id)->get();
        $dock_id = $docksQuery->pluck('id');
        $dock_name = $docksQuery->pluck('name');
        $docksArray = [];

            $k = 0;
            foreach ($dock_name as $dock)
            {
                $k++;

                $docksArray [$k] = $dock;

            }

          return $docksArray;

    }
}
