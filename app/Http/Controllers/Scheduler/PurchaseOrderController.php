<?php

namespace App\Http\Controllers\Scheduler;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\PurchaseOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class PurchaseOrderController extends Controller
{

    private $purchaseOrderService;

    public function __construct(PurchaseOrderService $service)
    {
        $this->purchaseOrderService = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $purchaseOrders = $this->purchaseOrderService->all();
        return view('scheduler.purchase_order.index', compact('purchaseOrders'));
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        return view('scheduler.purchase_order.show', compact('purchaseOrder'));

    }

    public function search(Request $request)
    {
        $term = trim($request->q);

      /*  if (empty($term)) {
            return Response::json([]);
        }
*/

        $client_id = $request->client_id;
        $supplier_id = $request->supplier_id;


        if(!empty($request->supplier_id)){
            //Si hay supplier id, ignoro el client_id
            $client_id = false;
        }

        $purchaseOrders = PurchaseOrder::
                                        when($term, function ($query, $term){
                                            return $query->where('number', 'LIKE', "{$term}%");
                                        })
                                        ->when($supplier_id, function ($query, $supplier_id) {
                                            return $query->where('supplier_id', $supplier_id);
                                        })
                                        ->when($client_id, function ($query, $client_id) {
                                            return $query->whereIn('supplier_id', Supplier::where('client_id', $client_id)->pluck('id')->toArray());
                                        })
                                        ->get()
                                        ->map(function($purchaseOrder){
                                                return [ 'id' => $purchaseOrder->id,
                                                         'text' => $purchaseOrder->number,
                                                         'due_date'=> $purchaseOrder->due_date,
                                                         'supplier' => $purchaseOrder->supplier()->first(['id','wms_name'])
                                                        ];
                        });

        //TODO:where due_date

        return Response::json($purchaseOrders);
    }

    public function getById(Request $request)
    {
        $ids = trim($request->ids);

        if (empty($ids)) {
            return Response::json([]);
        }

        $purchaseOrders = PurchaseOrder::whereIn('id',explode(',', $ids))
                                        ->get()
                                        ->map(function($purchaseOrder){
                                            return [
                                                'id' => $purchaseOrder->id,
                                                'text' => $purchaseOrder->number,
                                                'supplier' => $purchaseOrder->supplier()->first(['id','wms_name'])
                                            ];
                                        });

        //TODO:where due_date

        return Response::json($purchaseOrders);
    }
}
