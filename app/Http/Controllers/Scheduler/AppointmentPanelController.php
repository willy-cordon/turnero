<?php

namespace App\Http\Controllers\Scheduler;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Services\AppointmentService;


class AppointmentPanelController extends Controller
{
    private $appointmentService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AppointmentService $service)
    {
        $this->appointmentService = $service;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Location $location)
    {
        $relatedModels = $this->appointmentService->getRelatedModels($location);

        return view('scheduler.panel.index', compact('relatedModels', 'location'));
    }
}
