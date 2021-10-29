@extends('layouts.admin')
@section('style')
    <title>{{ config('app.name') }} | {{ trans('scheduler.settings.title') }}</title>
@endsection
@section('content')

<div class="card">
    <div class="card-header">
       {{ trans('scheduler.settings.title') }}
    </div>

    <div class="card-body">
        <form action="{{ route("scheduler.settings.update") }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-row">
                <div class="form-group col-sm-4 {{ $errors->has('init_hour') ? 'has-error' : '' }}">
                    <label for="init_hour">{{ trans('scheduler.settings.fields.init_hour') }}*</label>

                    <select name="init_hour" id="init_hour" class="form-control to-select2">
                        @foreach($hours as $key=>$value)
                            <option value="{{ $key }}" {{ $init_hour == $key ? 'selected':'' }}>{{ $value }}</option>
                        @endforeach
                    </select>

                    @if($errors->has('init_hour'))
                        <em class="invalid-feedback">
                            {{ $errors->first('init_hour') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.settings.fields.init_hour_helper') }}
                    </p>
                </div>
                <div class="form-group col-sm-4 {{ $errors->has('end_hour') ? 'has-error' : '' }}">
                    <label for="end_hour">{{ trans('scheduler.settings.fields.end_hour') }}*</label>
                    <select name="end_hour" id="end_hour" class="form-control to-select2">
                        @foreach($hours as $key=>$value)
                            <option value="{{ $key }}" {{ $end_hour == $key ? 'selected':'' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('end_hour'))
                        <em class="invalid-feedback">
                            {{ $errors->first('end_hour') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.settings.fields.end_hour_helper') }}
                    </p>
                </div>
                <div class="form-group col-sm-4 {{ $errors->has('appointment_init_minutes_size') ? 'has-error' : '' }}">
                    <label for="appointment_init_minutes_size">{{ trans('scheduler.settings.fields.appointment_init_minutes_size') }}*</label>
                    <select name="appointment_init_minutes_size" id="appointment_init_minutes_size" class="form-control to-select2">
                        @foreach($spots as $key=>$value)
                            <option value="{{ $key }}" {{ $appointment_init_minutes_size == $key ? 'selected':'' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('appointment_init_minutes_size'))
                        <em class="invalid-feedback">
                            {{ $errors->first('appointment_init_minutes_size') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.settings.fields.appointment_init_minutes_size_helper') }}
                    </p>
                </div>
            </div>
            <div>
                <input class="btn btn-success" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>


    </div>
</div>
@endsection