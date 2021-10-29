<?php

namespace App\Http\Controllers\Scheduler;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduler\SchemeRequest;
use App\Models\Scheme;
use App\Services\SchemeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SchemeController extends Controller
{

    /**
     * @var schemeService
     */
    private $schemeService;

    public function __construct(SchemeService $service)
    {
        $this->schemeService = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $schemes = $this->schemeService->all();
        return view('scheduler.schemes.index', compact('schemes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $action =  route("scheduler.schemes.store");
        $method = 'POST';
        return view('scheduler.schemes.create_edit', compact('action','method'));
    }

    /**
     * Store a newly created resource in storage.
     * @param SchemeRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SchemeRequest $request)
    {
        $this->schemeService->create($request);
        return redirect()->route('scheduler.schemes.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Scheme $scheme
     * @return \Illuminate\Http\Response
     */
    public function edit(Scheme $scheme)
    {
        $action = route("scheduler.schemes.update", [$scheme->id]);
        $method = 'PUT';
        return view('scheduler.schemes.create_edit', compact('scheme', 'action', 'method'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SchemeRequest $request
     * @param Scheme $scheme
     * @return void
     */
    public function update(SchemeRequest $request, Scheme $scheme)
    {
        $this->schemeService->update($scheme, $request);
        return redirect()->route('scheduler.schemes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Scheme $scheme
     * @return \Illuminate\Http\Response
     */
    public function destroy(Scheme $scheme)
    {
        $deleteStatus = $this->schemeService->destroy($scheme);
        return redirect()->route('scheduler.schemes.index')->with('status', $deleteStatus);
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        $this->schemeService->restore($id);
        return redirect()->route('scheduler.schemes.index');
    }


}
