@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.transportation_vouchers.title_singular') }}</title>
@endsection
@section('content')
{{--    <a class="back-link" href="{{route('scheduler.activity-groups.index')}}"><i class="fas fa-long-arrow-alt-left"></i> {{trans('scheduler.activity_groups.title')}}</a>--}}
    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('scheduler.transportation_vouchers.title_singular') }} - {{date('Y/m/d H:i')}}
        </div>
        <div class="card-body">
            <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method($method)

                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }} mr-2">


                   <h3>{{$supplier->wms_name}}</h3>
                </div>
                <div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
                    <label for="description">{{ trans('scheduler.transportation_vouchers.fields.address') }}</label>
                    <input type="text" id="address" name="address" class="form-control" value="{{ old('address', isset($supplier) ? $supplier->address : '') }}">
                    @if($errors->has('address'))
                        <em class="invalid-feedback">
                            {{ $errors->first('address') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.actions.fields.description_helper') }}
                    </p>
                    <input type="hidden" name="appointment_id" value="{{$appointment->id}}">
                    <input type="hidden" name="supplier_id" value="{{$supplier->id}}">
                </div>

                <div>
                    <input class="btn btn-success" type="submit" value="{{ trans('global.save') }}">
                    <a href="{{ route('scheduler.appointments.index')}}" class="btn btn-danger">{{ trans('global.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection


