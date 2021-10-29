<?php

namespace App\Http\Controllers\Scheduler;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduler\TransportationVoucherRequest;
use App\Models\Appointment;
use App\Models\Supplier;
use App\Services\TransportationVoucherService;
use App\Models\TransportationVoucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransportationVoucherController extends Controller
{

    private $transportationVoucherService;

    public function __construct(TransportationVoucherService $service)
    {
        $this->transportationVoucherService = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('scheduler.transportation-vouchers.index');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Appointment $appointment)
    {

       $supplier = $appointment->supplier()->first();

        $voucherQuery = TransportationVoucher::query();
        $voucherQuery->where('appointment_id','=',$appointment->id);
        $voucher = $voucherQuery->get();
        $voucherId = $voucher->first();
        if (count($voucher)){
            return redirect()->route('scheduler.transportation-vouchers.show',$voucherId->id);
        }else{
           $action = route('scheduler.transportation-vouchers.store');
           $method = 'POST';

           return view('scheduler.transportation-vouchers.create-voucher', compact('action','method','appointment','supplier'));
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransportationVoucherRequest $request)
    {
        $this->transportationVoucherService->create($request);
        $voucher = TransportationVoucher::query();
        $voucher->where('appointment_id','=',$request->get('appointment_id'));
        $voucherSupplier = $voucher->get()->first();

        return redirect()->route('scheduler.transportation-vouchers.show',$voucherSupplier->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(TransportationVoucher $transportationVoucher)
    {

        return view('scheduler.transportation-vouchers.show',compact('transportationVoucher'));
    }



    public function getVouchers(Request $request)
    {
        return $this->transportationVoucherService->dataTablesVouchers($request);
    }
}
