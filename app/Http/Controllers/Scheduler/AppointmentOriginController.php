<?php

namespace App\Http\Controllers\Scheduler;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduler\StoreAppointmentOriginRequest;
use App\Http\Requests\Scheduler\UpdateAppointmentOriginRequest;
use App\Models\AppointmentOrigin;
use App\Services\AppointmentOriginService;
use Illuminate\Http\Request;

class AppointmentOriginController extends Controller
{
    /**
     * @var appointmentOriginService
     */
    private $appointmentOriginService;

    public function __construct(AppointmentOriginService $service)
    {
        $this->appointmentOriginService = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $appointmentOrigins = $this->appointmentOriginService->all();
        return view('scheduler.appointment-origins.index', compact('appointmentOrigins'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $action =  route("scheduler.appointment-origins.store");
        $method = 'POST';
        return view('scheduler.appointment-origins.create_edit', compact('action','method'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreAppointmentOriginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreAppointmentOriginRequest $request)
    {
        $this->appointmentOriginService->create($request);
        return redirect()->route('scheduler.appointment-origins.index');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(AppointmentOrigin $appointmentOrigin)
    {
        $action = route("scheduler.appointment-origins.update", [$appointmentOrigin->id]);
        $method = 'PUT';
        return view('scheduler.appointment-origins.create_edit', compact('appointmentOrigin', 'action', 'method'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAppointmentOriginRequest $request
     * @param AppointmentOrigin $appointmentOrigin
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateAppointmentOriginRequest $request, AppointmentOrigin $appointmentOrigin)
    {
        $this->appointmentOriginService->update($appointmentOrigin, $request);
        return redirect()->route('scheduler.appointment-origins.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(AppointmentOrigin $appointmentOrigin)
    {
        $deleteStatus = $this->appointmentOriginService->destroy($appointmentOrigin);
        return redirect()->route('scheduler.appointment-origins.index')->with('status', $deleteStatus);
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        $this->appointmentOriginService->restore($id);
        return redirect()->route('scheduler.appointment-origins.index');
    }
}
