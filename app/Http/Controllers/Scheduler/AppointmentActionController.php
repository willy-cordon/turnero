<?php

namespace App\Http\Controllers\Scheduler;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduler\StoreAppointmentActionRequest;
use App\Http\Requests\Scheduler\UpdateAppointmentActionRequest;
use App\Models\AppointmentAction;
use App\Services\AppointmentActionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AppointmentActionController extends Controller
{
    /**
     * @var appointmentActionService
     */
    private $appointmentActionService;

    public function __construct(AppointmentActionService $service)
    {
        $this->appointmentActionService = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $appointmentActions = $this->appointmentActionService->all();
        return view('scheduler.appointment-actions.index', compact('appointmentActions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $action =  route("scheduler.appointment-actions.store");
        $method = 'POST';
        return view('scheduler.appointment-actions.create_edit', compact('action','method'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreAppointmentActionRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreAppointmentActionRequest $request)
    {
        $this->appointmentActionService->create($request);
        return redirect()->route('scheduler.appointment-actions.index');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(AppointmentAction $appointmentAction)
    {
        $action = route("scheduler.appointment-actions.update", [$appointmentAction->id]);
        $method = 'PUT';
        return view('scheduler.appointment-actions.create_edit', compact('appointmentAction', 'action', 'method'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAppointmentActionRequest $request
     * @param AppointmentAction $AppointmentAction
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateAppointmentActionRequest $request, AppointmentAction $appointmentAction)
    {

        $this->appointmentActionService->update($appointmentAction, $request);
        return redirect()->route('scheduler.appointment-actions.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(AppointmentAction $appointmentAction)
    {
        $deleteStatus = $this->appointmentActionService->destroy($appointmentAction);
        return redirect()->route('scheduler.appointment-actions.index')->with('status', $deleteStatus);
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        $this->appointmentActionService->restore($id);
        return redirect()->route('scheduler.appointment-actions.index');
    }
}
