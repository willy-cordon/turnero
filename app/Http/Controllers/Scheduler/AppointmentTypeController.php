<?php

namespace App\Http\Controllers\Scheduler;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduler\StoreAppointmentTypeRequest;
use App\Http\Requests\Scheduler\UpdateAppointmentTypeRequest;
use App\Models\AppointmentType;

use App\Services\AppointmentTypeService;


class AppointmentTypeController extends Controller
{

    /**
     * @var AppointmentTypeService
     */
    private $appointmentTypeService;

    public function __construct(AppointmentTypeService $service)
    {
        $this->appointmentTypeService = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $appointmentTypes = $this->appointmentTypeService->all();
        return view('scheduler.appointment-types.index', compact('appointmentTypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $action =  route("scheduler.appointment-types.store");
        $method = 'POST';
        return view('scheduler.appointment-types.create_edit', compact('action','method'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreAppointmentTypeRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreAppointmentTypeRequest $request)
    {
        $this->appointmentTypeService->create($request);
        return redirect()->route('scheduler.appointment-types.index');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(AppointmentType $appointmentType)
    {
        $action = route("scheduler.appointment-types.update", [$appointmentType->id]);
        $method = 'PUT';
        return view('scheduler.appointment-types.create_edit', compact('appointmentType', 'action', 'method'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAppointmentTypeRequest $request
     * @param AppointmentType $appointmentType
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateAppointmentTypeRequest $request, AppointmentType $appointmentType)
    {
        $this->appointmentTypeService->update($appointmentType, $request);
        return redirect()->route('scheduler.appointment-types.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(AppointmentType $appointmentType)
    {
        $deleteStatus = $this->appointmentTypeService->destroy($appointmentType);
        return redirect()->route('scheduler.appointment-types.index')->with('status', $deleteStatus);
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        $this->appointmentTypeService->restore($id);
        return redirect()->route('scheduler.appointment-types.index');
    }
}
