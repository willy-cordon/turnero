<?php

namespace App\Http\Controllers\Scheduler;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduler\StoreDockRequest;
use App\Http\Requests\Scheduler\UpdateDockRequest;
use App\Models\Dock;
use App\Models\Location;
use App\Services\DockService;
use Illuminate\Http\Request;

class DockController extends Controller
{
    /**
     * @var $dockService
     */
    private $dockService;

    public function __construct(DockService $service)
    {
        $this->dockService = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $docks = $this->dockService->all();
        return view('scheduler.docks.index', compact('docks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $action =  route("scheduler.docks.store");
        $method = 'POST';
        $locations = Location::all();
        return view('scheduler.docks.create_edit', compact('action','method', 'locations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreDockRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreDockRequest $request)
    {
        $this->dockService->create($request);
        return redirect()->route('scheduler.docks.index');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Dock $dock
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Dock $dock)
    {
        $action = route("scheduler.docks.update", [$dock->id]);
        $method = 'PUT';
        $locations = Location::all();
        return view('scheduler.docks.create_edit', compact('dock', 'action', 'method', 'locations'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param UpdateDockRequest $request
     * @param Dock $dock
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateDockRequest $request, Dock $dock)
    {
        $this->dockService->update($dock, $request);
        return redirect()->route('scheduler.docks.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Dock $dock
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Dock $dock)
    {
        $deleteStatus = $this->dockService->destroy($dock);
        return redirect()->route('scheduler.docks.index')->with('status', $deleteStatus);
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        $this->dockService->restore($id);
        return redirect()->route('scheduler.docks.index');
    }
}
