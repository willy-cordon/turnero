<?php

namespace App\Http\Controllers\Scheduler;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduler\SupplierGroupRequest;
use App\Models\SupplierGroup;
use App\Services\SupplierGroupService;
use Illuminate\Http\Request;


class SupplierGroupController extends Controller
{
    /**
     * @var supplierGroupService
     */
    private $supplierGroupService;

    public function __construct(SupplierGroupService $service)
    {
        $this->supplierGroupService = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $supplierGroups = $this->supplierGroupService->all();
        return view('scheduler.supplier-groups.index', compact('supplierGroups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $action =  route("scheduler.supplier-groups.store");
        $method = 'POST';
        return view('scheduler.supplier-groups.create_edit', compact('action','method'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SupplierGroupRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(SupplierGroupRequest $request)
    {
        $this->supplierGroupService->create($request);
        return redirect()->route('scheduler.supplier-groups.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SupplierGroup  $supplierGroup
     * @return \Illuminate\Http\Response
     */
    public function show(SupplierGroup $supplierGroup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SupplierGroup  $supplierGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(SupplierGroup $supplierGroup)
    {
        $action = route("scheduler.supplier-groups.update", [$supplierGroup->id]);
        $method = 'PUT';
        return view('scheduler.supplier-groups.create_edit', compact('supplierGroup', 'action', 'method'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SupplierGroupRequest $request
     * @param \App\Models\SupplierGroup $supplierGroup
     * @return \Illuminate\Http\Response
     */
    public function update(SupplierGroupRequest $request, SupplierGroup $supplierGroup)
    {
        $this->supplierGroupService->update($supplierGroup, $request);
        return redirect()->route('scheduler.supplier-groups.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SupplierGroup  $supplierGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(SupplierGroup $supplierGroup)
    {
        $deleteStatus = $this->supplierGroupService->destroy($supplierGroup);
        return redirect()->route('scheduler.supplier-groups.index')->with('status', $deleteStatus);
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        $this->supplierGroupService->restore($id);
        return redirect()->route('scheduler.supplier-groups.index');
    }
}
