@extends('layouts.admin')
@section('style')
    <title>{{ config('app.name') }} | {{ trans('scheduler.intervention_migration.title') }}</title>
@endsection
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('scheduler.activity_instance_change_date.title') }}

        </div>

        <div class="card-body">
            <form action="{{ route("scheduler.activity-instance-change-date.activityInstanceChangeDay") }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group col-sm-4 {{ $errors->has('supplier_id') ? 'has-error' : '' }}">
                        <label for="supplier_id">{{ trans('scheduler.activity_instance_change_date.fields.supplier') }}*</label>
                        <select name="supplier_id" id="supplier_id" class="form-control to-select2">
                            <option></option>
                            @foreach($dateChangeActivity['supplierPendings'] as $supplier)
                                <option value="{{ $supplier->id }}">{{$supplier->wms_id .' - '.$supplier->wms_name }} </option>
                            @endforeach
                        </select>
                        @if($errors->has('supplier'))
                            <em class="invalid-feedback">
                                {{ $errors->first('supplier') }}
                            </em>
                        @endif
                        <p class="helper-block">
                            {{ trans('scheduler.activity_instance_change_date.fields.supplier_helper') }}
                        </p>
                    </div>

                    <input type="hidden" id="activity_type_id" name="activity_type_id" value="1">

                    <div class="form-group col-sm-4 {{ $errors->has('day') ? 'has-error' : '' }}">
                        <label for="day">{{ trans('scheduler.activity_instance_change_date.fields.day') }}*</label>
                        <select name="day" id="day" class="form-control to-select2">
                            <option></option>
                            @foreach($dateChangeActivity['days'] as $key => $day)
                                <option value="{{ $key }}">{{ $day }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('day'))
                            <em class="invalid-feedback">
                                {{ $errors->first('day') }}
                            </em>
                        @endif
                        <p class="helper-block">
                            {{ trans('scheduler.activity_instance_change_date.fields.day_helper') }}
                        </p>
                    </div>

                </div>
                <div>
                    <input class="btn btn-success" type="submit" value="{{ trans('scheduler.activity_instance_change_date.send') }}">
                </div>
            </form>


        </div>


    </div>
@endsection