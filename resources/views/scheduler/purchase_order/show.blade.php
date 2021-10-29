@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.purchase_orders.title_singular') }}</title>
@endsection
@section('content')
    <a class="back-link" href="{{route('scheduler.purchase-orders.index')}}"><i class="fas fa-long-arrow-alt-left"></i> {{trans('scheduler.purchase_orders.title')}}</a>
    <div class="card">
        <div class="card-header">
            {{ trans('scheduler.purchase_orders.title_singular') }}
        </div>

        <div class="card-body">
            <div class="form-row">
                <div class="col-md-3 show-group">
                    <span>{{ trans('scheduler.purchase_orders.fields.number') }}</span>
                    <span>{{$purchaseOrder->number}}</span>
                </div>
                <div class="col-md-3 show-group">
                    <span>{{ trans('scheduler.purchase_orders.fields.due_date') }}</span>
                    <span>{{$purchaseOrder->due_date}}</span>
                </div>
                <div class="col-md-6 show-group">
                    <span>{{ trans('scheduler.suppliers.title_singular') }}</span>
                    <span>{{$purchaseOrder->supplier->wms_name}} <button type="button" class="btn btn-xs btn-info" data-toggle="modal" data-target="#supplier-show"> <i class="fas fa-eye"></i> {{trans('global.view')}}</button></span>
                </div>
            </div>


            <div class="modal fade" id="supplier-show" tabindex="-1" role="dialog" aria-labelledby="supplierModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="supplierModalLabel">{{ trans('scheduler.suppliers.title_singular') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                        </div>

                    </div>
                </div>
            </div>



            <div class="form-row">
                <table class="table">
                    <thead>
                    <tr>
                        <th>
                            {{ trans('scheduler.purchase_orders.fields.line_number') }}
                        </th>
                        <th>
                            {{ trans('scheduler.purchase_orders.fields.part_code') }}
                        </th>
                        <th>
                            {{ trans('scheduler.purchase_orders.fields.part_description') }}
                        </th>
                        <th>
                            {{ trans('scheduler.purchase_orders.fields.quantity') }}
                        </th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($purchaseOrder->items as $key => $item)
                        <tr>

                            <td>
                                {{ $item->line_number ?? '' }}
                            </td>
                            <td>
                                {{ $item->part_code ?? '' }}
                            </td>
                            <td>
                                {{ $item->part_description ?? '' }}
                            </td>
                            <td>
                                {{ $item->quantity ?? '' }}
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
    <script>
        $(function(){
            $( "#supplier-show .modal-body" ).load( "{{ route('scheduler.suppliers.show', $purchaseOrder->supplier->id) }} .card-body" );
        })
    </script>
@endsection