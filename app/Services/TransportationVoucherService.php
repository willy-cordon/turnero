<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Supplier;
use App\Models\TransportationVoucher;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Zendaemon\Services\Service;
use Zendaemon\Services\Traits\{CreateModel, DestroyModel, ReadModel, UpdateModel};

final class TransportationVoucherService extends Service
{
    use CreateModel, ReadModel, UpdateModel, DestroyModel;

    /**
     * Set model class name.
     *
     * @return void
     */
    protected function setModel(): void
    {
        $this->model = TransportationVoucher::class;
    }

    public function create(Request $request): Model
    {
        $addressQuery = Supplier::Query();
            $addressQuery->where('address', '!=', $request->get('address'));
            $addressQuery->where('id','=',$request->get('supplier_id'));
            $addressQuery->update(['address'=>$request->get('address')]);

            $transportationVoucher = $this->model::create();
            $transportationVoucher->appointment()->associate(Appointment::find($request->get('appointment_id')))->save();
            $transportationVoucher->supplier()->associate(Supplier::find($request->get('supplier_id')))->save();

            return $transportationVoucher;
    }


    public function dataTablesVouchers(Request $request)
    {
        ## Read value
        $draw            = $request->get('draw');
        $start           = $request->get("start");
        $rowperpage      = $request->get("length");
        $columnIndex_arr = $request->get('order');
        $columnName_arr  = $request->get('columns');
        $order_arr       = $request->get('order');
        $search_arr      = $request->get('search');
        $columnIndex     = $columnIndex_arr[0]['column'];



        $columnName = str_replace('-', '.',$columnName_arr[$columnIndex]['data']);
        $columnSortOrder = $order_arr[0]['dir'];
        $searchValue = $search_arr['value'];
        $totalRecordsQuery = TransportationVoucher::query();
        $totalRecordsQuery ->select('count(*) as allcount');

        // Total records
//        $totalRecords = TransportationVoucher::select('count(*) as allcount')->count();

        $transportationQuery = TransportationVoucher::Query();
        $transportationQuery->leftJoin('suppliers', 'suppliers.id', '=', 'transportation_vouchers.supplier_id');
        $transportationQuery->leftJoin('users', 'users.id', '=', 'transportation_vouchers.created_by');

        $transportationQuery->orWhere('suppliers.wms_name', 'like', '%' .$searchValue . '%');
        $transportationQuery->orWhere('suppliers.wms_id', 'like', '%' .$searchValue . '%');
        $transportationQuery->orWhere('suppliers.address', 'like', '%' .$searchValue . '%');
        $transportationQuery->orWhere('suppliers.wms_id', 'like', '%' .$searchValue . '%');
        $transportationQuery->orWhere('suppliers.aux5', 'like', '%' .$searchValue . '%');

/*
        $supervision_user = false;
        $supervised_users = User::where('supervisor_id', '=', auth()->user()->id)->get();
        if ($supervised_users->count()>0){$supervision_user = true;}

        if(!auth()->user()->hasRole(User::ROLE_SCHEDULER) && $supervision_user){
            $transportationQuery->whereIn('transportation_vouchers.created_by', $supervised_users->pluck('id'));
            $totalRecordsQuery->whereIn('transportation_vouchers.created_by', $supervised_users->pluck('id'));
        }*/

        $totalRecords = $totalRecordsQuery->count();

        $transportationQuery->select('transportation_vouchers.*');
        $transportationQuery->orderBy($columnName,$columnSortOrder);

        $totalRecordswithFilter = $transportationQuery->get()->count();

        if($rowperpage != -1){
            $transportationQuery->skip($start);
            $transportationQuery->take($rowperpage);
        }
        $vouchers = $transportationQuery->get();

        $data_arr = array();

        foreach ($vouchers as $voucher)
        {
            $buttonView = '<a class="btn btn-xs btn-primary" title=" '. trans('global.view') .'" href=" '.route('scheduler.transportation-vouchers.show', $voucher->id) .' ">
                <i class="fas fa-eye"></i>
            </a> ';

            $data_arr[] = array(
                "any" => '',
                "transportation_vouchers-id"           => $voucher->id ?? '',
                "transportation_vouchers-created_at"   => $voucher->created_at,
                "suppliers-wms_name"                   => $voucher->supplier->wms_name ?? '',
                "suppliers-wms_id"                     => $voucher->supplier->wms_id ?? '',
                "suppliers-address"                    => $voucher->supplier->address ?? '',
                'button-action'                        => $buttonView
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        return $response;



    }
}
