<?php

namespace App\Services;

use App\Models\SupplierInterventionLog;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Zendaemon\Services\Service;
use Zendaemon\Services\Traits\{CreateModel, DestroyModel, ReadModel, UpdateModel};

final class SupplierInterventionLogService extends Service
{
    use CreateModel, ReadModel, UpdateModel, DestroyModel;

    /**
     * Set model class name.
     *
     * @return void
     */
    protected function setModel(): void
    {
        $this->model = SupplierInterventionLog::class;
    }


    public function getDataTables(Request $request)
    {

        $usersNames = User::all()->mapWithKeys(function ($user){return [$user->id=>$user->name];});
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
        $totalRecordsQuery = SupplierInterventionLog::Query();
        $totalRecordsQuery ->select('count(*) as allcount');

        // Total records
//        $totalRecords = SupplierInterventionLog::select('count(*) as allcount')->count();

        $supplierInterventionQuery = SupplierInterventionLog::Query();
        $supplierInterventionQuery->leftJoin('suppliers', 'suppliers.id', '=', 'supplier_intervention_logs.supplier_id');
        $supplierInterventionQuery->leftJoin('users', 'users.id', '=', 'supplier_intervention_logs.created_by');


        $supplierInterventionQuery->orWhere('suppliers.wms_name', 'like', '%' .$searchValue . '%');
        $supplierInterventionQuery->orWhere('suppliers.wms_id', 'like', '%' .$searchValue . '%');

        $supplierInterventionQuery->orWhere('supplier_intervention_logs.id', 'like', '%' .$searchValue . '%');
        $supplierInterventionQuery->orWhere('supplier_intervention_logs.intervention_reason', 'like', '%' .$searchValue . '%');
        $supplierInterventionQuery->orWhere('supplier_intervention_logs.created_at', 'like', '%' .$searchValue . '%');

        $supplierInterventionQuery->orWhere('users.name', 'like', '%' .$searchValue . '%');


        $totalRecords = $totalRecordsQuery->count();

        $supplierInterventionQuery->select('supplier_intervention_logs.*');
        $supplierInterventionQuery->orderBy($columnName,$columnSortOrder);

        $totalRecordswithFilter = $supplierInterventionQuery->get()->count();

        if($rowperpage != -1){
            $supplierInterventionQuery->skip($start);
            $supplierInterventionQuery->take($rowperpage);
        }
        $supplierInterventions = $supplierInterventionQuery->get();

        $data_arr = array();

        foreach ($supplierInterventions as $supplierIntervention)
        {
            $description = '';
            if($supplierIntervention->intervention_reason != '1. Vigilancia.'){
                $description = $supplierIntervention->description;
            }

            $data_arr[] = array(
                "any" => '',
                "supplier_intervention_logs-id"                 => $supplierIntervention->id ?? '',
                "suppliers-wms_name"                             => $supplierIntervention->supplier->wms_name ?? '',
                "suppliers-wms_id"                              => $supplierIntervention->supplier->wms_id ?? '',
                "supplier_intervention_logs-intervention_reason"=> $supplierIntervention->intervention_reason ?? '',
                "supplier_intervention_logs-description"        => $description,
                "supplier_intervention_logs-created_by"         => Arr::exists($usersNames,$supplierIntervention->created_by ) ? $usersNames[$supplierIntervention->created_by] : '',
//                'updated_by-name'                               => $usersNames[$supplierIntervention->updated_by] ?? '',
                'supplier_intervention_logs-created_at'         => $supplierIntervention->created_at->format('d/m/Y') ?? '',
//                'supplier_intervention_logs-updated_at'         => $supplierIntervention->updated_at->format('d-m-Y') ?? ''
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
