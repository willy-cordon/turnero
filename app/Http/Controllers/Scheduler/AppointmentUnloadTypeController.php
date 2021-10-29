<?php

namespace App\Http\Controllers\Scheduler;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduler\StoreAppointmentUnloadTypeRequest;
use App\Http\Requests\Scheduler\UpdateAppointmentUnloadTypeRequest;
use App\Models\AppointmentUnloadType;
use App\Services\AppointmentUnloadTypeService;
use Illuminate\Http\Request;

class AppointmentUnloadTypeController extends Controller
{
    /**
     * @var appointmentUnloadTypeService
     */
    private $appointmentUnloadTypeService;

    public function __construct(AppointmentUnloadTypeService $service)
    {
        $this->appointmentUnloadTypeService = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $appointmentUnloadTypes = $this->appointmentUnloadTypeService->all();
        return view('scheduler.appointment-unload-types.index', compact('appointmentUnloadTypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $action =  route("scheduler.appointment-unload-types.store");
        $method = 'POST';
        return view('scheduler.appointment-unload-types.create_edit', compact('action','method'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreAppointmentUnloadTypeRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreAppointmentUnloadTypeRequest $request)
    {
        $this->appointmentUnloadTypeService->create($request);
        return redirect()->route('scheduler.appointment-unload-types.index');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(AppointmentUnloadType $appointmentUnloadType)
    {
        $action = route("scheduler.appointment-unload-types.update", [$appointmentUnloadType->id]);
        $method = 'PUT';
        return view('scheduler.appointment-unload-types.create_edit', compact('appointmentUnloadType', 'action', 'method'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAppointmentUnloadTypeRequest $request
     * @param AppointmentUnloadType $appointmentUnloadType
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateAppointmentUnloadTypeRequest $request, AppointmentUnloadType $appointmentUnloadType)
    {
        $this->appointmentUnloadTypeService->update($appointmentUnloadType, $request);
        return redirect()->route('scheduler.appointment-unload-types.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(AppointmentUnloadType $appointmentUnloadType)
    {
        $deleteStatus = $this->appointmentUnloadTypeService->destroy($appointmentUnloadType);
        return redirect()->route('scheduler.appointment-unload-types.index')->with('status', $deleteStatus);
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        $this->appointmentUnloadTypeService->restore($id);
        return redirect()->route('scheduler.appointment-unload-types.index');
    }
}
