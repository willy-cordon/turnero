<?php

namespace App\Http\Controllers\Scheduler;

use App\Http\Controllers\Controller;
use App\Models\AppointmentChangeLog;
use App\Services\AppointmentChangeLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AppointmentChangeLogController extends Controller
{
    private $appointmentChangeLogService;


    public function __construct(AppointmentChangeLogService $service){
        $this->appointmentChangeLogService = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('scheduler.appointment-change-log.index');
    }

  public function getAppointmentChangeLog(Request $request)
  {

      return $this->appointmentChangeLogService->getDataTables($request);
  }










}
