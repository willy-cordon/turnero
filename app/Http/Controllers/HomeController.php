<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Location;
use App\Models\Sequence;
use App\Services\AppointmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function index()
    {
        $locations = Location::all();
        $sequences = Sequence::all();

        return view('scheduler.home', compact('locations','sequences'));
    }
}
