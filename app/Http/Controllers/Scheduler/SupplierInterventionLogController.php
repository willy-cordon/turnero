<?php

namespace App\Http\Controllers\Scheduler;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduler\SupplierInterventionLogRequest;
use App\Models\Supplier;
use App\Models\SupplierInterventionLog;
use App\Services\SupplierInterventionLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupplierInterventionLogController extends Controller
{

    /**
     * @var SupplierInterventionLogService
     */
    private $supplierInterventionLogService;

    public function __construct(SupplierInterventionLogService $service)
    {
        $this->supplierInterventionLogService = $service;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('scheduler.suppliers-intervention-log.index');
    }

    public function save(SupplierInterventionLogRequest $request){
        //Log::debug($request);
        $saveSupplierIntervention = SupplierInterventionLog::create([
            'description' => $request->description,
            'intervention_reason' => $request->reasons,
        ]);
        $saveSupplierIntervention->supplier()->associate(Supplier::find($request->get('supplier_id')))->save();
        //Log::debug($saveSupplierIntervention->created_at);
        if ($saveSupplierIntervention){
            return ['status'=>'ok'];
        }else{
            return ['status'=>'error'];
        }
    }


    public function getSupplierInterventionLog(Request $request)
    {
        return $this->supplierInterventionLogService->getDataTables($request);
    }

}
