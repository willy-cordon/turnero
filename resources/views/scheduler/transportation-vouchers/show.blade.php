@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.transportation_vouchers.title_singular') }} | Nro: {{$transportationVoucher->id}}</title>
@endsection
@section('content')
    <a class="back-link" href="{{route('scheduler.transportation-vouchers.index')}}"><i class="fas fa-long-arrow-alt-left"></i> {{trans('scheduler.transportation_vouchers.title')}}</a>

    <button type="button" style="position: absolute; right: 0; margin-right: 40px; margin-top: 40px; z-index: 2;" class="btn btn-primary" onclick="$('.to-print').printThis();"><i class="fas fa-print"></i> {{trans('global.datatables.print')}}</button>
    <div class="card to-print">
        <div class="card-header">
            {{ trans('scheduler.transportation_vouchers.title_print_original') }} - Nro: <strong>{{$transportationVoucher->id}}</strong>
        </div>

        <div class="card-body">
            <div class="form-row">
                <div class="col-md-6 show-group">
                    <span>{{ trans('scheduler.transportation_vouchers.fields.supplier') }}</span>
                    <span>{{$transportationVoucher->supplier->wms_name}}</span>
                </div>
                <div class="col-md-3 show-group">
                    <span>{{ trans('scheduler.transportation_vouchers.fields.dni') }}</span>
                    <span>{{$transportationVoucher->supplier->wms_id}}</span>
                </div>
                <div class="col-md-3 show-group">
                    <span>{{ trans('scheduler.transportation_vouchers.fields.dateTime') }}</span>
                    <span>{{$transportationVoucher->created_at}}</span>
                </div>
                <div class="col-md-8 show-group">
                    <span>{{ trans('scheduler.transportation_vouchers.fields.address') }}</span>
                    <span> {{ $transportationVoucher->supplier->address ?? '' }} {{ $transportationVoucher->supplier->aux5 ?? '' }} , {{ $transportationVoucher->supplier->aux4 ?? '' }}</span>
                </div>
                <div class="col-md-4 show-group">
                    <span>{{ trans('scheduler.transportation_vouchers.fields.license_plate') }}</span>
                    <span></span>
                </div>

            </div>


        </div>


    </div>


    <div class="card to-print">
        <div class="card-header">
            {{ trans('scheduler.transportation_vouchers.title_print_duplicate') }} - Nro: <strong>{{$transportationVoucher->id}}</strong>
        </div>

        <div class="card-body">
            <div class="form-row">
                <div class="col-md-6 show-group">
                    <span>{{ trans('scheduler.transportation_vouchers.fields.supplier') }}</span>
                    <span>{{$transportationVoucher->supplier->wms_name}}</span>
                </div>
                <div class="col-md-3 show-group">
                    <span>{{ trans('scheduler.transportation_vouchers.fields.dni') }}</span>
                    <span>{{$transportationVoucher->supplier->wms_id}}</span>
                </div>
                <div class="col-md-3 show-group">
                    <span>{{ trans('scheduler.transportation_vouchers.fields.dateTime') }}</span>
                    <span>{{$transportationVoucher->supplier->created_at}}</span>
                </div>
                <div class="col-md-8 show-group">
                    <span>{{ trans('scheduler.transportation_vouchers.fields.address') }}</span>
                    <span> {{ $transportationVoucher->supplier->address ?? '' }} {{ $transportationVoucher->supplier->aux5 ?? '' }} , {{ $transportationVoucher->supplier->aux4 ?? '' }}</span>
                </div>

                <div class="col-md-4 show-group">
                    <span>{{ trans('scheduler.transportation_vouchers.fields.license_plate') }}</span>
                    <span></span>
                </div>
            </div>


        </div>


    </div>
@endsection
@section('scripts')
    @parent

@endsection