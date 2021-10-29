@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.suppliers.title_singular') }}</title>
@endsection
@section('content')
<a class="back-link" href="{{route('scheduler.suppliers.index')}}"><i class="fas fa-long-arrow-alt-left"></i> {{trans('scheduler.suppliers.title')}}</a>
<div class="card">
    <div class="card-header">
        {{ trans('scheduler.suppliers.title_singular') }}
    </div>

    <div class="card-body">
        <div class="form-row">
            <div class="col-md-4 show-group">
                <span>{{ trans('scheduler.suppliers.fields.wms_name') }}</span>
                <span>{{$supplier->wms_name}}</span>
            </div>
            <div class="col-md-4 show-group">
                <span>{{ trans('scheduler.suppliers.fields.wms_id') }}</span>
                <span>{{$supplier->wms_id}}</span>
            </div>
            <div class="col-md-4 show-group">
                <span>{{ trans('scheduler.clients.title_singular') }}</span>
                <span>{{$supplier->client->name}}</span>
            </div>
        </div>

        <div class="form-row">
            <div class="col-md-4 show-group">
                <span>{{ trans('scheduler.suppliers.fields.address') }}</span>
                <span>{{$supplier->address}}</span>
            </div>
            <div class="col-md-4 show-group">
                <span>{{ trans('scheduler.suppliers.fields.phone') }}</span>
                <span>{{$supplier->phone}}</span>
            </div>
            <div class="col-md-4 show-group">
                <span>{{ trans('scheduler.suppliers.fields.contact') }}</span>
                <span>{{$supplier->contact}}</span>
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-4 show-group">
                <span>{{ trans('scheduler.suppliers.fields.email') }}</span>
                <span>{{$supplier->email}}</span>
            </div>
            <div class="col-md-4 show-group">
                <span>{{ trans('scheduler.suppliers.fields.aux1') }}</span>
                <span>{{$supplier->aux1}}</span>
            </div>
            <div class="col-md-4 show-group">
                <span>{{ trans('scheduler.suppliers.fields.aux2') }}</span>
                <span>{{$supplier->aux2}}</span>
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-4 show-group">
                <span>{{ trans('scheduler.suppliers.fields.aux3') }}</span>
                <span>{{$supplier->aux3}}</span>
            </div>
            <div class="col-md-4 show-group">
                <span>{{ trans('scheduler.suppliers.fields.aux4') }}</span>
                <span>{{$supplier->aux4}}</span>
            </div>
            <div class="col-md-4 show-group">
                <span>{{ trans('scheduler.suppliers.fields.aux5') }}</span>
                <span>{{$supplier->aux5}}</span>
            </div>
        </div>

    </div>
</div>
@endsection