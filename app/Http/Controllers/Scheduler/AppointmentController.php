<?php

namespace App\Http\Controllers\Scheduler;

use App\Enums\ActivityStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduler\StoreAppointmentRequest;
use App\Http\Requests\Scheduler\UpdateAppointmentRequest;
use App\Models\Activity;
use App\Models\ActivityInstance;
use App\Models\Appointment;
use App\Models\Location;
use App\Models\Supplier;
use App\Services\AppointmentService;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class AppointmentController extends Controller
{


    private $appointmentService;


    public function __construct(AppointmentService $service){
        $this->appointmentService = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $appointments = [];
        $locations = Location::all();
        $usersNames = User::all()->mapWithKeys(function ($user){return [$user->id=>$user->name];});
        return view('scheduler.appointments.index', compact('appointments', 'locations', 'usersNames'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Location $location)
    {
        $relatedModels = $this->appointmentService->getRelatedModels($location, true);
        $action =  route("scheduler.appointments.store");
        $method = 'POST';
        return view('scheduler.appointments.create_edit', compact('relatedModels', 'action', 'method', 'location'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreAppointmentRequest $request)
    {
            Log::debug($request);
            $this->appointmentService->create($request);
            return redirect()->route('scheduler.appointments.index');

    }


    /**
     * Display the specified resource.
     *
     * @param  Appointment $appointment
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Appointment $appointment)
    {
        return view('scheduler.appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Appointment $appointment
     *
     */
    public function edit(Appointment $appointment)
    {
        if ($appointment->synchronized_at)
            return abort(401);

        $relatedModels = $this->appointmentService->getRelatedModels($appointment->dock->location);
        $action = route("scheduler.appointments.update", [$appointment->id]);
        $method = 'PUT';
        $location = $appointment->dock->location;
        return view('scheduler.appointments.create_edit', compact('appointment', 'relatedModels','action', 'method', 'location'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {

        $this->appointmentService->update($appointment, $request);
        return redirect()->route('scheduler.appointments.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Appointment $appointment

     */
    public function destroy(Appointment $appointment)
    {

        if ($appointment->synchronized_at)
            return abort(401);

        $deleteStatus = $this->appointmentService->destroy($appointment);
        return redirect()->route('scheduler.appointments.index')->with('status', $deleteStatus);
    }

    public function checkAppointment(Request $request)
    {
        $dock_id = trim($request->dock_id);
        $date = trim($request->date);
        $appointment_id = trim($request->appointment_id);
        if (empty($dock_id) || empty($date)) {
            return Response::json([]);
        }

        $date = Carbon::createFromFormat(config('app.datetime_format'), $date);

        $appointments = Appointment::where('dock_id', $dock_id)
                                    ->whereNotIn('action_id', [Appointment::STATUS_CANCELED, Appointment::NO_SHOW])
                                    ->whereDate('start_date', Carbon::parse($date)
                                    ->format('Y-m-d'))->get();

        $result = [];
        foreach ($appointments as $appointment){
            if($appointment_id != $appointment->id) {
                $appointment_start_date = Carbon::createFromFormat(config('app.datetime_format'), $appointment->start_date);
                $appointment_end_date = Carbon::createFromFormat(config('app.datetime_format'), $appointment->end_date);

                while (strtotime($appointment_start_date) < strtotime($appointment_end_date)) {
                    $result[] = Carbon::parse($appointment_start_date)->format(config('app.datetime_format'));
                    $appointment_start_date->addMinutes(15);
                }
            }
        }
        return Response::json($result);
    }

    public function getBySupplier(Request $request)
    {
        $supplier_id = trim($request->supplier_id);

        if (empty($supplier_id)) {
            return Response::json([]);
        }
        $appointments = Appointment::where('supplier_id', $supplier_id)
                                    ->leftJoin('docks', 'docks.id', '=', 'appointments.dock_id')
                                    ->leftJoin('locations', 'locations.id', '=', 'docks.location_id')
                                    ->leftJoin('suppliers', 'suppliers.id', '=', 'appointments.supplier_id')
                                    ->where('appointments.deleted_at', null)
                                    ->whereIn('appointments.action_id',[Appointment::STATUS_ACCOMPLISH])
                                    ->where(function($appointmentSubQuery){
                                        $appointmentSubQuery->whereRaw('`suppliers`.`scheme_id` IN (select scheme_id from location_scheme where location_id = locations.id)')
                                            ->orWhere(function($appointmentSubQuery2){
                                                $appointmentSubQuery2->whereNull('suppliers.scheme_id')
                                                    ->whereRaw(config('app.default_scheme').' IN (select scheme_id from location_scheme where location_id = locations.id)');
                                            });
                                    })
                                    ->select('appointments.id', 'locations.name', 'appointments.start_date', 'locations.id as location_id')->get()->map(function ($data){
                                        return ['id'=>$data->id, 'text' => ' Fecha: '.$data->start_date.' | Nro: '.$data->id.' | '.$data->name, 'location_id'=>$data->location_id];
                                    });
        return Response::json($appointments);
    }

    public function getAppointments(Request $request)
    {
        return $this->appointmentService->getDataTables($request);
    }


    public function addIsInRange(Request $request)
    {
        $appointmentQuery = Appointment::query();
        $appointmentQuery->whereNotIn('action_id', [Appointment::STATUS_CANCELED, Appointment::NO_SHOW]);
        $appointmentQuery->whereNull('deleted_at');
        $appointmentQuery->orderBy('created_at', 'asc');

        $appointmentQuery2 = clone $appointmentQuery;
        $appointmentTotal = $appointmentQuery2->select('count(*) as allcount')->count();
        $appointmentData = $appointmentQuery->skip($request->from)->take(1000)->get();

        $result = [];
        foreach ($appointmentData as $appointment){
            if(!empty($appointment->dock->location->prev_location_id)){
                $lastPrevLocationAppointmentQuery = Appointment::Query();
                $lastPrevLocationAppointmentQuery->leftJoin('docks', 'docks.id', '=', 'appointments.dock_id' );
                $lastPrevLocationAppointmentQuery->leftJoin('locations', 'locations.id', '=', 'docks.location_id' );
                $lastPrevLocationAppointmentQuery->where('appointments.supplier_id', $appointment->supplier_id);
                $lastPrevLocationAppointmentQuery->where('locations.id', $appointment->dock->location->prev_location_id);
                $lastPrevLocationAppointmentQuery->whereNotIn('appointments.action_id',  [Appointment::STATUS_CANCELED, Appointment::NO_SHOW]);
                $lastPrevLocationAppointmentQuery->whereNull('appointments.deleted_at');
                $lastPrevLocationAppointmentQuery->orderBy('appointments.created_at', 'desc');
                $lastPrevLocationAppointmentQuery->select([DB::raw('DATE_FORMAT(appointments.start_date,"%Y-%m-%d %H:%m:%s") as dateAppointment') ,'appointments.id']);
                $lastPrevLocationAppointmentQuery->limit(1);
                $prevAppointment = $lastPrevLocationAppointmentQuery->first();

                $currentAppointmentStartDate = $appointment->start_date;
                $prevAppointmentStartDate = $prevAppointment->dateAppointment;


                $startDateFormat = Carbon::createFromFormat('d/m/Y H:i',$currentAppointmentStartDate)->format('Y-m-d H:i');
                $parseFormatInit = Carbon::parse($prevAppointmentStartDate);
                $prev_from = $parseFormatInit->addDays($appointment->dock->location->prev_days_from)->format('Y-m-d H:i');
                $prev_to = $parseFormatInit->addDays($appointment->dock->location->prev_days_to)->format('Y-m-d H:i');

                $appointmentUpdateQuery = Appointment::query();
                $appointmentUpdateQuery ->where('id',$appointment->id);
                if ($startDateFormat >= $prev_from && $startDateFormat <= $prev_to)
                {
                    $appointmentUpdateQuery -> update(['date_range_status' => Appointment::IN_RANGE]);

                }else{

                    $appointmentUpdateQuery->update(['date_range_status' => Appointment::OUT_RANGE]);
                }


                $result[] =['current_id'=>$appointment->id, 'current_location'=>$appointment->dock->location->id, 'prev_appointment_id'=>$prevAppointment->id,'prev_appointment_date'=>$prevAppointment->dateAppointment ];
            }else{
                $result[] =['current_id'=>$appointment->id, 'current_location'=>$appointment->dock->location->id, 'prev_appointment_id'=>''];
            }
        }


        return Response::json(["last_from"=>$request->from, "total"=>$appointmentTotal, "data"=>$result]);
    }
}
