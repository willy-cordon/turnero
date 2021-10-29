<?php

namespace App\Http\Controllers\Scheduler;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduler\StoreSupplierRequest;
use App\Http\Requests\Scheduler\UpdateSupplierRequest;
use App\Models\ActivityInstance;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Scheme;
use App\Models\Supplier;
use App\Models\SupplierGroup;
use App\Models\SupplierInterventionLog;
use App\Services\SupplierService;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Throwable;
use function Sodium\compare;

class SupplierController extends Controller
{
    /**
     * @var SupplierService
     */
    private $supplierService;

    public function __construct(SupplierService $service)
    {
        $this->supplierService = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $reasons = SupplierInterventionLog::REASONS;
        $defaultReason = SupplierInterventionLog::REASON_ONE;
        return view('scheduler.suppliers.index', compact('reasons','defaultReason'));
    }

    /**
     * Display a listing of the resource workflow.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function workflow()
    {
        $reasons = SupplierInterventionLog::REASONS;
        return view('scheduler.suppliers.workflow', compact('reasons'));
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Supplier $supplier)
    {
        return view('scheduler.suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $disabled = '';
        $disabledCreate = 'disabled';
        $status = Supplier::STATUS;
        $statusCreate = Supplier::STATUS_ONE;
        $action  =  route("scheduler.suppliers.store");
        $method  = 'POST';
        $client  = Client::first();
        $genders = Supplier::GENDERS;
        $cps = $this->supplierService->getCPs();
        $schemes = Scheme::all();
        $supplierGroups = SupplierGroup::all();

        $disabledSupplierGroup = auth()->user()->hasRole(User::ROLE_ADMIN) || auth()->user()->hasRole(User::ROLE_ADMIN) ? '' : 'disabled';

        return view('scheduler.suppliers.create_edit', compact('action','method', 'client','cps', 'genders', 'disabledCreate','disabled','status','statusCreate','schemes','supplierGroups','disabledSupplierGroup'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  SupplierRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreSupplierRequest $request)
    {

        $this->supplierService->create($request);
        return redirect()->route('scheduler.suppliers.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Supplier $supplier)
    {
        $disabled = '';
        $disabledCreate ='';
        if(!auth()->user()->hasRole(User::ROLE_ADMIN))
        {
            $disabled = 'disabled';
        }

        $genders = Supplier::GENDERS;
        $status = Supplier::STATUS;
        $action =  route("scheduler.suppliers.update", [$supplier->id]);
        $method = 'PUT';
        $client = $supplier->client;
        $cps = $this->supplierService->getCPs();
        $schemes = Scheme::all();
        $supplierGroups = SupplierGroup::all();

        $disabledSupplierGroup = auth()->user()->hasRole(User::ROLE_ADMIN) || auth()->user()->hasRole(User::ROLE_ADMIN) ? '' : 'disabled';

        return view('scheduler.suppliers.create_edit', compact('supplier', 'action', 'method', 'client', 'cps','genders','status','disabled','disabledCreate','schemes','supplierGroups','disabledSupplierGroup'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  SupplierRequest $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        if (!empty($request->status) && $supplier->status != $request->status)
        {

            //Creamos la notificacion
            $subject = 'Cambio de estado del voluntario a "'.$request->status.'", realizado por: '. auth()->user()->name;;
            $notificationCreate = Notification::create([
                'email_subject' => $subject,
                'type' => Notification::STATUS,
                'created_by' => $supplier->recruiter_id
            ]);
            $notificationCreate->supplier()->associate(Supplier::find($supplier->id))->save();
        }
        $this->supplierService->update($supplier, $request);
        return redirect()->route('scheduler.suppliers.index');
    }

    public function destroy(Supplier $supplier)
    {
        $deleteStatus = $this->supplierService->destroy($supplier);
        return redirect()->route('scheduler.suppliers.index')->with('status', $deleteStatus);
    }

    public function toggleIntervention(Request $request)
    {
        return  Response::json($this->supplierService->toggleIntervention($request));

    }

    public function getByClient(Request $request)
    {
        
        $client_id = trim($request->client_id);

        if (empty($client_id)) {
            return Response::json([]);
        }

        $prev_location_id = trim($request->prev_location_id);
        $prev_action_id = trim($request->prev_action_id);
        $current_location_id = trim($request->current_location_id);
        $current_supplier_id = trim($request->current_supplier_id);
        $prev_location_id_workflow = trim($request->prev_location_id_workflow);

        $locationQuery = Location::query();
        $locationQuery ->where('id','=',$current_location_id);
        $locationQuery->get();
        $location = $locationQuery->first();


        $disabledSuppliers = Supplier::query();
        $disabledSuppliers ->where('status','=',Supplier::STATUS_TWO);
        $disabledSuppliers ->get();
        $disabledSuppliersIds = $disabledSuppliers->pluck('id');


        $locationHasSchemes = false;
        $locationHasDefaultScheme = false;
        $locationSchemes = $location->schemes->pluck('id');

        if (!empty($locationSchemes))
        {
            $locationHasSchemes = true;
            if ($locationSchemes->contains(config('app.default_scheme')))
            {
                $locationHasDefaultScheme = true;
            }
        }

        $filterSuppliers = false;
        $validSuppliers = [];
        $validSuppliersForPrevLocation = [];
        if($prev_location_id_workflow != null && $prev_action_id !=null && $prev_location_id != null){
            $filterSuppliers = true;

            $validSuppliersQuery = Appointment::Query();
            $validSuppliersQuery->select('appointments.supplier_id', 'appointments.start_date');
            $validSuppliersQuery->join('docks', 'appointments.dock_id', '=', 'docks.id');
            $validSuppliersQuery->where('appointments.action_id', $prev_action_id);
            $validSuppliersQuery->where('appointments.deleted_at', null);
            $validSuppliersQuery->where('appointments.next_step', 'Asignar próximo turno');

            $validSuppliersQueryForPrevLocation = clone $validSuppliersQuery;

            $validSuppliersQuery->where('docks.location_id', $prev_location_id_workflow);
            $validSuppliersQueryForPrevLocation->where('docks.location_id', $prev_location_id);

            if ($location->unique_appointment == true ){

                $invalidSupplierQuery = Appointment::query();
                $invalidSupplierQuery->join('docks', 'appointments.dock_id', '=', 'docks.id');
                $invalidSupplierQuery->where('docks.location_id', $current_location_id);
                $invalidSupplierQuery->where('appointments.deleted_at', null);
                $invalidSupplierQuery->whereNotIn('appointments.action_id',  [Appointment::STATUS_CANCELED, Appointment::NO_SHOW]);
                $invalidSupplierIds = $invalidSupplierQuery->get()->pluck('supplier_id','supplier_id');
                    if (!empty($current_supplier_id)){
                        $invalidSupplierIds->forget($current_supplier_id);
                    }
                $validSuppliersQuery->whereNotIn('appointments.supplier_id', $invalidSupplierIds);
                $validSuppliersQueryForPrevLocation->whereNotIn('appointments.supplier_id', $invalidSupplierIds);
            }

            $validSuppliers =  $validSuppliersQuery->get()->mapWithKeys(function ($data){
                return [$data->supplier_id => $data->start_date];
            });

            $validSuppliersForPrevLocation =  $validSuppliersQueryForPrevLocation->get()->mapWithKeys(function ($data){
                return [$data->supplier_id => $data->start_date];
            });

        }

        $supervision_user = false;
        $supervised_users = User::where('supervisor_id', '=', auth()->user()->id)->get();
        if ($supervised_users->count()>0){$supervision_user = true;}

        $suppliersQuery = Supplier::Query();
        $suppliersQuery->where('client_id', '=', $client_id);

            if ($locationHasSchemes){
                $suppliersQuery ->where(function($query1) use($location, $locationHasDefaultScheme){
                    $query1->whereIn('scheme_id',  $location->schemes->pluck('id'));
                    if($locationHasDefaultScheme){
                        $query1->orWhere('suppliers.scheme_id',null);
                    }
                });
            }else{
                return Response::json('');
            }

        if(auth()->user()->can('scheduler_admin') || auth()->user()->hasRole(User::ROLE_DOCTOR_ADMIN) ){
            if($filterSuppliers){

                $suppliersQuery->whereIn('id', $validSuppliers->keys());
                if(count($disabledSuppliersIds)>0){
                    $suppliersQuery->whereNotIn('id',$disabledSuppliersIds);
                }
                $suppliers = $suppliersQuery->get()->map(
                    function($supplier) use($validSuppliers, $validSuppliersForPrevLocation){
                        return ['id' => $supplier->id, 'text' => $supplier->wms_id . ' - ' . $supplier->wms_name, 'last_appointment_date'=>$validSuppliers[$supplier->id], 'last_prev_appointment_date'=>$validSuppliersForPrevLocation[$supplier->id]];
                });
            }else{

                if(count($disabledSuppliersIds)>0){
                    $suppliersQuery->whereNotIn('id',$disabledSuppliersIds);
                }
                $suppliers = $suppliersQuery->get()->map(function($supplier){
                        return ['id' => $supplier->id, 'text' => $supplier->wms_id . ' - ' . $supplier->wms_name, 'last_appointment_date'=>null, 'last_prev_appointment_date'=>null];
                });
            }
        }else if(auth()->user()->can('scheduler_coordinator')  ){

            if ($supervision_user){

                if($filterSuppliers){

                    $suppliersQuery->whereIn('id', $validSuppliers->keys());
                    if(count($disabledSuppliersIds)>0){
                        $suppliersQuery->whereNotIn('id',$disabledSuppliersIds);
                    }
                    $suppliers = $suppliersQuery->get()->map(function($supplier) use($validSuppliers,$validSuppliersForPrevLocation ){
                        return ['id' => $supplier->id, 'text' => $supplier->wms_id . ' - ' . $supplier->wms_name, 'last_appointment_date'=>$validSuppliers[$supplier->id], 'last_prev_appointment_date'=>$validSuppliersForPrevLocation[$supplier->id]];
                    });
                }else{
                    if(count($disabledSuppliersIds)>0){
                        $suppliersQuery->whereNotIn('id',$disabledSuppliersIds);
                    }
                    $suppliers = $suppliersQuery->where('client_id', '=', $client_id)->
                        leftJoin('users','users.id','=','suppliers.created_by')->
                        where('users.supervisor_id' , auth()->user()->id )->
                        select([
                            'suppliers.id','suppliers.wms_id','suppliers.wms_name'
                    ])->get()->map(function($supplier){
                        return ['id' => $supplier->id, 'text' => $supplier->wms_id . ' - ' . $supplier->wms_name, 'last_appointment_date'=>null,'last_prev_appointment_date'=>null];
                    });
                }
            }else{
                if(count($disabledSuppliersIds)>0){
                    $suppliersQuery->whereNotIn('id',$disabledSuppliersIds);
                }
                $suppliers = $suppliersQuery->get()->map(function($supplier){
                    return ['id' => $supplier->id, 'text' => $supplier->wms_id . ' - ' . $supplier->wms_name, 'last_appointment_date'=>null,'last_prev_appointment_date'=>null];
                });
            }

        }else{
            if($filterSuppliers){

                $suppliersQuery->where('created_by', auth()->user()->id);

                $suppliersQuery->whereIn('id', $validSuppliers->keys());
                if(count($disabledSuppliersIds)>0){
                    $suppliersQuery->whereNotIn('id',$disabledSuppliersIds);
                }

                $suppliers = $suppliersQuery->get()->map(function ($supplier) use($validSuppliers,$validSuppliersForPrevLocation) {
                    return ['id' => $supplier->id, 'text' => $supplier->wms_id . ' - ' . $supplier->wms_name, 'last_appointment_date'=>$validSuppliers[$supplier->id],'last_prev_appointment_date'=>$validSuppliersForPrevLocation[$supplier->id]];
                });
            }else {

                $suppliersQuery->where('created_by', auth()->user()->id);

                if(count($disabledSuppliersIds)>0){
                    $suppliersQuery->whereNotIn('id',$disabledSuppliersIds);
                }

                $suppliers = $suppliersQuery->get()->map(function ($supplier) {
                    return ['id' => $supplier->id, 'text' => $supplier->wms_id . ' - ' . $supplier->wms_name, 'last_appointment_date'=>null,'last_prev_appointment_date'=>null ];
                });
            }
        }
        return Response::json($suppliers);
    }


    public function search(Request $request)
    {


        $disabledSuppliers = Supplier::query();
        $disabledSuppliers ->where('status','=',Supplier::STATUS_TWO);
        $disabledSuppliers ->get();
        $disabledSuppliersIds = $disabledSuppliers->pluck('id');


        //Parametros de busqueda
        $term = trim($request->q);
        /*  if (empty($term)) {
              return Response::json([]);
          }
  */
        $client_id = $request->client_id;

        if(empty($client_id)){
            return Response::json([]);
        }

        if(auth()->user()->can('scheduler_admin') || auth()->user()->can('scheduler_coordinator') ){
            $suppliersQuery = Supplier::Query();
            $suppliersQuery->when($term, function ($query, $term){
                return $query->where(function($query) use ($term){
                    $query->where('wms_name', 'LIKE', "{$term}%")->orWhere('wms_id', 'LIKE', "{$term}%");
                });
            });
            $suppliersQuery->where('client_id', $client_id);
            if(count($disabledSuppliersIds)>0){
                $suppliersQuery->whereNotIn('id',$disabledSuppliersIds);
            }

            $suppliers =$suppliersQuery->get()->map(function ($supplier){
                    return ['id' => $supplier->id, 'text' => $supplier->wms_id . ' - ' . $supplier->wms_name];
                });
        }else{
            $suppliersQuery = Supplier::Query();
            $suppliersQuery-> when($term, function ($query, $term){
                return $query->where(function($query) use ($term){
                    $query->where('wms_name', 'LIKE', "{$term}%")->orWhere('wms_id', 'LIKE', "{$term}%");
                });
            });
            $suppliersQuery->where('client_id', $client_id);
            $suppliersQuery->where('created_by', auth()->user()->id);
            if(count($disabledSuppliersIds)>0){
                $suppliersQuery->whereNotIn('id',$disabledSuppliersIds);
            }


            $suppliers = $suppliersQuery->get()->map(function ($supplier){
                    return ['id' => $supplier->id, 'text' => $supplier->wms_id . ' - ' . $supplier->wms_name];
                });

        }

        return Response::json($suppliers);
    }

    public function getSuppliers(Request $request)
    {
       return $this->supplierService->getDataTables($request);
    }

    public function getWorkflowSuppliers(Request $request)
    {
        return $this->supplierService->getWorkflowDataTables($request);
    }

    public function timeLine(Supplier $supplier)
    {
        $intervenedLogQuery = SupplierInterventionLog::query();
        $intervenedLogQuery ->where('supplier_id','=',$supplier->id);
        $intervenedLogQuery ->select([DB::raw('DATE_FORMAT(supplier_intervention_logs.created_at,"%d/%m/%Y") as dateAll'),'supplier_intervention_logs.intervention_reason as textResult',DB::raw("'appointment' as typeValue"),DB::raw("' ' as action"), 'supplier_intervention_logs.created_at as dateOrder']);

        $appointmentsSupplierQuery = Appointment::query();
        $appointmentsSupplierQuery->leftJoin('docks', 'docks.id', '=', 'appointments.dock_id');
        $appointmentsSupplierQuery->leftJoin('locations', 'docks.location_id', '=', 'locations.id');
        $appointmentsSupplierQuery->leftJoin('appointment_actions', 'appointment_actions.id', '=', 'appointments.action_id');
        $appointmentsSupplierQuery ->where('appointments.supplier_id','=',$supplier->id);
        $appointmentsSupplierQuery ->where('appointment_actions.name','!=', Appointment::STATUS_CANCELED);
        $appointmentsSupplierQuery ->select([DB::raw('DATE_FORMAT(appointments.start_date,"%d/%m/%Y") as dateAll'),
            'locations.name as textResult',
            DB::raw("'intervention' as typeValue"),
            'appointment_actions.name as action',
            'appointments.start_date as dateOrder'
        ]);
        $appointmentsSupplierQuery ->union($intervenedLogQuery);
        $appointmentsSupplierQuery ->orderBy('dateOrder','asc');
        $appointmentsSuppliers = $appointmentsSupplierQuery->get();


        return view('scheduler.suppliers.show-timeline',compact('supplier','appointmentsSuppliers'));
    }


}

