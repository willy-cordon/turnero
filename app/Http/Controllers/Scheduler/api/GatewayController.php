<?php

namespace App\Http\Controllers\Scheduler\api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentType;
use App\Models\Client;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Throwable;

class GatewayController extends Controller
{
    public function getDailyAppointments(){


        $suppliers = [  [103055, "LABORATORIOS ANDROMACO SO"],
                        [56839, "KIMBERLY_CLARK DIV_PANALE"],
                        [57681, "LOS CINCO HISPANOS S_A_"],
                        [19380,"PROCTER _ GAMBLE LIMPIEZA"],
                        [999998,"ALIMENTOS REFRIGERADOS S_"],
                        [44725, "FERRERO ARGENTINA S_A_"]
                     ];

        $purchase_orders_n = [5,8,13,21,34,55,89];
        $quantities_n = [100,20,56,39,71,34,12,67];
        $lines_n = [5,1,8,5,16,7];

        $response = array();
        foreach ($suppliers as $supplier){
            $supplier_data = array();
            $supplier_data["supplier_id"]=$supplier[0];
            $supplier_data["supplier_name"]=$supplier[1];
            $purchase_orders_k = array_rand($purchase_orders_n, 1);

            $purchase_orders = array();
            for($i=0; $i<$purchase_orders_n[$purchase_orders_k];$i++){
                $purchase_order = array();
                $purchase_order["number"] = rand(3000,10000) + $supplier[0];
                $purchase_order["due_date"] = now();
                $lines_k = array_rand($lines_n, 1);
                $purchase_order_lines = array();
                $total_quantity = 0;
                for($k=0; $k<$lines_n[$lines_k];$k++){
                    $purchase_order_line = array();

                    $purchase_order_line["line_number"] = $k + 1;
                    $purchase_order_line["part_code"] = 1;
                    $purchase_order_line["part_description"] = $supplier[1].' item - po line -'.($k + 1);
                    $quantities_k = array_rand($quantities_n, 1);
                    $purchase_order_line["quantity"] = $quantities_n[$quantities_k];
                    $purchase_order_lines[] = $purchase_order_line;
                    $total_quantity += $purchase_order_line["quantity"] ;
                }
                $purchase_order["purchase_order_lines"] = $purchase_order_lines;
                $purchase_order["total_quantity"] = $total_quantity;
                $purchase_orders[]=$purchase_order;
            }
            $supplier_data["purchase_orders"]= $purchase_orders;

            $response[] = $supplier_data;
        }


        return $response;
    }

    public function storePurchaseOrders(Request $request){


        try {
            $po_data = json_decode($request->get('po_data'));
            $client_id = $request->route('client_id');

            foreach ($po_data as $supplier_data){

                $supplier = Supplier::updateOrCreate(
                    ["wms_id" => $supplier_data->supplier_id, "client_id" => $client_id],
                    ["wms_name" => $supplier_data->supplier_name]);

                foreach ($supplier_data->purchase_orders as $purchase_order_data){
                    $purchase_order = PurchaseOrder::updateOrCreate(
                        ["number" => $purchase_order_data->number],
                        ["due_date" => Carbon::parse($purchase_order_data->due_date)->toDateTime(),
                            "items" => $purchase_order_data->purchase_order_lines,
                            "total_quantity"=>$purchase_order_data->total_quantity
                        ]
                    );
                    $purchase_order->supplier()->associate($supplier)->save();

                }
            }
        } catch (Throwable $e) {
            report($e);

            return "error:".$e->getMessage();
        }

        return "ok";

    }

    public function changeAppointmentToSynchronized(Request $request){
        $appointment = Appointment::find($request->route('appointment_id'));
        if($appointment && $appointment->is_reservation == false){
            $appointment->synchronized_at = now();
            $appointment->save();
            return ['status'=>'ok'];
        }
        return ['status'=>'error'];
    }
}
