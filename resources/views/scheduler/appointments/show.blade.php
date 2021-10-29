@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.appointments.title_singular') }} | Nro: {{$appointment->id}}</title>
@endsection
@section('content')
    <a class="back-link" href="{{route('scheduler.appointments.index')}}"><i class="fas fa-long-arrow-alt-left"></i> {{trans('scheduler.appointments.title')}}</a>

    <button type="button" style="position: absolute; right: 0; margin-right: 40px; margin-top: 40px; z-index: 2;" class="btn btn-primary" onclick="$('.to-print').printThis();"><i class="fas fa-print"></i> {{trans('global.datatables.print')}}</button>
    <div class="card to-print">
        <div class="card-header">
            {{ trans('scheduler.appointments.title_singular') }} - Nro: <strong>{{$appointment->id}}</strong>
        </div>

        <div class="card-body">
            <div class="form-row">
                <div class="col-md-2 show-group">
                    <span>{{ trans('scheduler.appointments.fields.client') }}</span>
                    <span>{{$appointment->supplier->client->name}}</span>
                </div>
                <div class="col-md-3 show-group">
                    <span>{{ trans('scheduler.appointments.fields.date_hour') }}</span>
                    <span> {{ substr($appointment->start_date, 0,10) ?? '' }} <strong>{{ substr($appointment->start_date, 11,16) ?? '' }}-{{ substr($appointment->end_date, 11,16) ?? '' }}</strong></span>
                </div>
                <div class="col-md-1 show-group">
                    <span>{{ trans('scheduler.appointments.fields.dock') }}</span>
                    <span>{{$appointment->dock->name}}</span>
                </div>
                <div class="col-md-4 show-group">
                    <span>{{ trans('scheduler.appointments.fields.supplier') }}</span>
                    <span>{{$appointment->supplier->wms_name}}</span>
                </div>
                <div class="col-md-2 show-group">
                    <span>{{ trans('scheduler.appointments.fields.supplier_dni') }}</span>
                    <span>{{$appointment->supplier->wms_id}}</span>
                </div>
            </div>

            <div class="form-row">
                <div class="col-md-4 show-group">
                    <span>{{ trans('scheduler.appointments.fields.supplier_address') }}</span>
                    <span> {{ $appointment->supplier->address ?? '' }} {{ $appointment->supplier->aux5 ?? '' }} , {{ $appointment->supplier->aux4 ?? '' }}</span>
                </div>

                <div class="col-md-2 show-group">
                    <span>{{ trans('scheduler.appointments.fields.created_at') }}</span>
                    <span>{{$appointment->created_at}}</span>
                </div>
                <div class="col-md-2 show-group">
                    <span>{{ trans('scheduler.appointments.fields.type') }}</span>
                    <span>{{$appointment->dock->location->name}}</span>
                </div>
                <div class="col-md-2 show-group">
                    <span>{{ trans('scheduler.appointments.fields.action') }}</span>
                    <span>{{$appointment->action->name}}</span>
                </div>
                <div class="col-md-2 show-group">
                    <span>{{ trans('scheduler.appointments.fields.transportation') }}</span>
                    <span>{{$appointment->transportation}}</span>
                </div>
            </div>
            <div class="form-row">

                <div class="col-md-2 show-group">
                    <span>{{ trans('scheduler.appointments.fields.next_step') }}</span>
                    <span>{{$appointment->next_step}}</span>
                </div>
                <div class="col-md-4 show-group">
                    <span>{{ trans('scheduler.appointments.fields.need_assistance') }}</span>
                    <span> @if($appointment->need_assistance == 1)
                            SI
                        @else
                            NO
                        @endif
                    </span>
                </div>

                <div class="col-md-6 show-group">
                    <span>{{ trans('scheduler.appointments.fields.comments') }}</span>
                    <span>{{$appointment->comments}}</span>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent

@endsection