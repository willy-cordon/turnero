@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.purchase_orders.title') }}</title>
    <link href="{{ asset('css/datatables.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('scheduler.purchase_orders.title') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover datatable datatable-loading datatable-purchase_order">
                    <thead>
                        <tr>
                            <th class="not-export-col" width="10">

                            </th>
                            <th>
                                {{ trans('scheduler.purchase_orders.fields.id') }}
                            </th>
                            <th>
                                {{ trans('scheduler.purchase_orders.fields.number') }}
                            </th>
                            <th>
                                {{ trans('scheduler.purchase_orders.fields.due_date') }}
                            </th>
                            <th>
                                {{ trans('scheduler.suppliers.title_singular') }}
                            </th>
                            <th class="not-export-col">
                                {{ trans('global.action') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($purchaseOrders as $key => $purchaseOrder)
                        <tr data-entry-id="{{ $purchaseOrder->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $purchaseOrder->id ?? '' }}
                            </td>
                            <td>
                                {{ $purchaseOrder->number ?? '' }}
                            </td>
                            <td>
                                {{ $purchaseOrder->due_date ?? '' }}
                            </td>
                            <td>
                                {{ $purchaseOrder->supplier->wms_name ?? '' }}
                            </td>
                            <td>
                                <a class="btn btn-xs btn-primary" title="{{ trans('global.show') }}" href="{{ route('scheduler.purchase-orders.show', $purchaseOrder->id) }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>


        </div>
    </div>
@endsection
@section('scripts')
@parent
<!--Lo necesario para que ande datatables-->
<script src="{{ asset('js/datatables.js') }}"></script>
@include('partials.datatables_globals')
<!-- -->
<script>
    $(function () {
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);


        $.extend(true, $.fn.dataTable.defaults, {
            order: [[ 1, 'desc' ]]
        });
        $('.datatable-purchase_order:not(.ajaxTable)').DataTable({ buttons: dtButtons });
    })

</script>
@endsection