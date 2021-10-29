<?php

namespace App\Http\Controllers\Scheduler;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduler\SequenceRequest;
use App\Models\Sequence;
use App\Services\SequenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SequenceController extends Controller
{

    /**
     * @var SequenceService
     */
    private $sequenceService;

    public function __construct(SequenceService $service)
    {
        $this->sequenceService = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sequences = $this->sequenceService->all();
        return view('scheduler.sequence.index', compact('sequences'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $action =  route("scheduler.sequence.store");
        $method = 'POST';
        return view('scheduler.sequence.create_edit', compact('action','method'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SequenceRequest $request)
    {
        $this->sequenceService->create($request);
        return redirect()->route('scheduler.sequence.index');
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Sequence $sequence)
    {
        $action = route("scheduler.sequence.update", [$sequence->id]);
        $method = 'PUT';
        return view('scheduler.sequence.create_edit', compact('sequence', 'action', 'method'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SequenceRequest $request, Sequence $sequence)
    {

        $this->sequenceService->update($sequence, $request);
        return redirect()->route('scheduler.sequence.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sequence $sequence)
    {
        $deleteStatus = $this->sequenceService->destroy($sequence);
        return redirect()->route('scheduler.sequence.index')->with('status', $deleteStatus);
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        $this->sequenceService->restore($id);
        return redirect()->route('scheduler.sequence.index');
    }
}
