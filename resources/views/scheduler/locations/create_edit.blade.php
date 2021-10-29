@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.locations.title_singular') }}</title>
@endsection
@section('content')
    <a class="back-link" href="{{route('scheduler.locations.index')}}"><i class="fas fa-long-arrow-alt-left"></i> {{trans('scheduler.locations.title')}}</a>
<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('scheduler.locations.title_singular') }}
    </div>

    <div class="card-body">
        <form id="locationForm" action="{{ $action }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method($method)
            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="name">{{ trans('scheduler.locations.fields.name') }}*</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($location) ? $location->name : '') }}" >
                @if($errors->has('name'))
                    <em class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('scheduler.locations.fields.name_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                <label for="description">{{ trans('scheduler.locations.fields.description') }}</label>
                <input type="text" id="description" name="description" class="form-control" value="{{ old('description', isset($location) ? $location->description : '') }}">
                @if($errors->has('description'))
                    <em class="invalid-feedback">
                        {{ $errors->first('description') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('scheduler.locations.fields.description_helper') }}
                </p>
            </div>
            <div class="row">

            <div class="form-group col-sm-6 {{ $errors->has('appointment_created_bcc_emails') ? 'has-error' : '' }}">
                <label for="appointment_created_bcc_emails">{{ trans('scheduler.locations.fields.bcc_emails_create') }}</label>
                <input type="text" id="appointment_created_bcc_emails" name="appointment_created_bcc_emails" class="form-control" value="{{ old('appointment_created_bcc_emails', isset($location) ? $location->appointment_created_bcc_emails : '') }}">
                @if($errors->has('appointment_created_bcc_emails'))
                    <em class="invalid-feedback">
                        {{ $errors->first('appointment_created_bcc_emails') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('scheduler.locations.fields.bcc_emails_create_helper') }}
                </p>
                <p class="errorEmail"></p>
            </div>
                <div class="form-group col-sm-6 {{ $errors->has('appointment_canceled_bcc_emails') ? 'has-error' : '' }}">
                    <label for="appointment_canceled_bcc_emails">{{ trans('scheduler.locations.fields.bcc_emails_canceled') }}</label>
                    <input type="text" id="appointment_canceled_bcc_emails" name="appointment_canceled_bcc_emails" class="form-control" value="{{ old('appointment_canceled_bcc_emails', isset($location) ? $location->appointment_canceled_bcc_emails : '') }}">
                    @if($errors->has('appointment_canceled_bcc_emails'))
                        <em class="invalid-feedback">
                            {{ $errors->first('appointment_canceled_bcc_emails') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.locations.fields.bcc_emails_canceled_helper') }}
                    </p>
                </div>

            </div>

            <div class="form-row">
                <div class="form-group col-sm-2 {{ $errors->has('init_hour') ? 'has-error' : '' }}">
                    <label for="init_hour">{{ trans('scheduler.settings.fields.init_hour') }}*</label>

                    <select name="init_hour" id="init_hour" class="form-control to-select2">
                        @foreach($hours as $key=>$value)
                            <option value="{{ $key }}" {{ (collect(old('location', isset($location) ? $location->init_hour : ''))->contains($key)) ? 'selected':'' }}>{{ $value }}</option>
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
                <div class="form-group col-sm-2 {{ $errors->has('end_hour') ? 'has-error' : '' }}">
                    <label for="end_hour">{{ trans('scheduler.settings.fields.end_hour') }}*</label>
                    <select name="end_hour" id="end_hour" class="form-control to-select2">
                        @foreach($hours as $key=>$value)
                            <option value="{{ $key }}" {{ (collect(old('location', isset($location) ? $location->end_hour : ''))->contains($key)) ? 'selected':'' }}>{{ $value }}</option>

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
                <div class="form-group col-sm-3 {{ $errors->has('appointment_init_minutes_size') ? 'has-error' : '' }}">
                    <label for="appointment_init_minutes_size">{{ trans('scheduler.settings.fields.appointment_init_minutes_size') }}*</label>
                    <select name="appointment_init_minutes_size" id="appointment_init_minutes_size" class="form-control to-select2">
                        @foreach($spots as $key=>$value)
                            <option value="{{ $key }}" {{ (collect(old('location', isset($location) ? $location->appointment_init_minutes_size : ''))->contains($key)) ? 'selected':'' }}>{{ $value }}</option>
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

                <div class="form-group col-sm-2 text-center justify-content-center">
                    <label for="unique_appointment">{{ trans('scheduler.locations.fields.unique_appointment') }}*</label>
                    <div style="clear: both"></div>
                    <input name="unique_appointment" type="hidden" value="0"/>
                    <input id="unique_appointment"
                           name="unique_appointment"
                           value="1"
                           type="checkbox"
                           data-toggle="toggle"
                           data-on="{{ trans('global.yes') }}"
                           data-off="{{ trans('global.no') }}"
                           data-onstyle="success" data-offstyle="primary"
                            {{ ( old('unique_appointment',  isset($location) ? $location->unique_appointment : '' ) == 1 ? 'checked':'') }}>
                </div>
                <div class="form-group col-sm-2 text-center justify-content-center">
                    <label for="enable_past_days">{{ trans('scheduler.locations.fields.enable_past_days') }}*</label>
                    <div style="clear: both"></div>
                    <input name="enable_past_days" type="hidden" value="0"/>
                    <input id="enable_past_days"
                           name="enable_past_days"
                           value="1"
                           type="checkbox"
                           data-toggle="toggle"
                           data-on="{{ trans('global.yes') }}"
                           data-off="{{ trans('global.no') }}"
                           data-onstyle="success" data-offstyle="primary"
                            {{ ( old('enable_past_days',  isset($location) ? $location->enable_past_days : '' ) == 1 ? 'checked':'') }}>
                </div>

            </div>
            <div class="form-row">
                <div class="form-group col-md-3 {{ $errors->has('prev_location_id') ? 'has-error' : '' }}">
                    <label for="prev_location_id">{{ trans('scheduler.locations.fields.prev_location') }}</label>
                    <select name="prev_location_id" id="prev_location_id" class="form-control to-select2" data-minimum-results-for-search="-1" data-placeholder="{{trans('scheduler.locations.fields.prev_location_placeholder')}}">
                        <option></option>
                        @foreach($locations as $location_p)
                            <option value="{{ $location_p->id }}" {{ (collect(old('action', isset($location) ? $location->prev_location_id : ''))->contains($location_p->id)) ? 'selected':'' }}>{{ $location_p->name }}</option>
                        @endforeach
                    </select>

                    @if($errors->has('prev_location_id'))
                        <em class="invalid-feedback">
                            {{ $errors->first('prev_location_id') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.locations.fields.prev_location_helper') }}
                    </p>
                </div>
                <div class="form-group col-md-3 {{ $errors->has('prev_action_id') ? 'has-error' : '' }}">
                    <label for="prev_action_id">{{ trans('scheduler.locations.fields.prev_action') }}</label>
                    <select name="prev_action_id" id="prev_action_id" class="form-control to-select2" data-minimum-results-for-search="-1" data-placeholder="{{trans('scheduler.locations.fields.prev_action_placeholder')}}">
                        <option></option>
                        @foreach($actions as $action)
                            <option value="{{ $action->id }}" {{ (collect(old('action', isset($location) ? $location->prev_action_id : ''))->contains($action->id)) ? 'selected':'' }}>{{ $action->name }}</option>
                        @endforeach
                    </select>

                    @if($errors->has('prev_action_id'))
                        <em class="invalid-feedback">
                            {{ $errors->first('prev_action_id') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.locations.fields.prev_action_helper') }}
                    </p>
                </div>
                <div class="form-group col-md-3 {{ $errors->has('prev_days_from') ? 'has-error' : '' }}">
                    <label for="prev_days_from">{{ trans('scheduler.locations.fields.prev_days_from') }}</label>
                    <input type="text" id="prev_days_from" name="prev_days_from" class="form-control" value="{{ old('prev_days_from', isset($location) ? $location->prev_days_from : '') }}">

                    @if($errors->has('prev_days_from'))
                        <em class="invalid-feedback">
                            {{ $errors->first('prev_days_from') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.locations.fields.prev_days_from_helper') }}
                    </p>
                </div>
                <div class="form-group col-md-3 {{ $errors->has('prev_days_to') ? 'has-error' : '' }}">
                    <label for="prev_days_to">{{ trans('scheduler.locations.fields.prev_days_to') }}</label>
                    <input type="text" id="prev_days_to" name="prev_days_to" class="form-control" value="{{ old('prev_days_to', isset($location) ? $location->prev_days_to : '') }}">

                    @if($errors->has('prev_days_to'))
                        <em class="invalid-feedback">
                            {{ $errors->first('prev_days_to') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.locations.fields.prev_days_to_helper') }}
                    </p>
                </div>

            </div>
            <div class="form-row">
                <div class="form-group col-sm-4 {{ $errors->has('schemes') ? 'has-error' : '' }}">
                    <label for="scheme">{{ trans('scheduler.locations.fields.scheme') }}*
                        <span class="btn btn-info btn-xs select-all">{{ trans('global.select_all') }}</span>
                        <span class="btn btn-info btn-xs deselect-all">{{ trans('global.deselect_all') }}</span>
                    </label>
                    <select name="schemes[]" id="scheme" class="form-control to-select2" multiple="multiple" >
                        @foreach($schemes as $id => $scheme)
                            <option value="{{ $id }}" {{ (in_array($id, old('schemes', [])) || isset($location) && $location->schemes->pluck('id')->contains($id)) ? 'selected' : '' }}>{{ $scheme }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('schemes'))
                        <em class="invalid-feedback">
                            {{ $errors->first('schemes') }}
                        </em>
                    @endif
                </div>

                <div class="form-group col-sm-4 {{ $errors->has('prev_location_id_workflow') ? 'has-error' : '' }}">
                    <label class="pb-1" for="prev_location_id_workflow">{{ trans('scheduler.locations.fields.prev_location_workflow') }} </label>
                    <select name="prev_location_id_workflow" id="prev_location_id_workflow" class="form-control to-select2" data-minimum-results-for-search="-1" data-placeholder="{{trans('scheduler.locations.fields.prev_location_workflow_placeholder')}}">
                        <option></option>
                        @foreach($locations as $location_p)
                            <option value="{{ $location_p->id }}" {{ (collect(old('action', isset($location) ? $location->prev_location_id_workflow : ''))->contains($location_p->id)) ? 'selected':'' }}>{{ $location_p->name }}</option>
                        @endforeach
                    </select>

                    @if($errors->has('prev_location_id_workflow'))
                        <em class="invalid-feedback">
                            {{ $errors->first('prev_location_id_workflow') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.locations.fields.prev_location_workflow_helper') }}
                    </p>
                </div>


                <div class="form-group col-sm-4 {{ $errors->has('sequence_id') ? 'has-error' : '' }}">
                    <label class="pb-1" for="sequence_id">{{ trans('scheduler.locations.fields.sequence') }} </label>
                    <select name="sequence_id" id="sequence_id" class="form-control to-select2" data-minimum-results-for-search="-1" >
                        <option></option>
                        @foreach($sequences as $sequence)
                            <option value="{{ $sequence->id }}" {{ (collect(old('action', isset($location) ? $location->sequence_id : ''))->contains($sequence->id)) ? 'selected':'' }}>{{ $sequence->name }}</option>
                        @endforeach
                    </select>

                    @if($errors->has('sequence_id'))
                        <em class="invalid-feedback">
                            {{ $errors->first('sequence_id') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.locations.fields.sequence_helper') }}
                    </p>
                </div>


            </div>
            <div>
                <input class="btn btn-success" type="submit" value="{{ trans('global.save') }}">
                <a href="{{ route('scheduler.locations.index')}}" class="btn btn-danger">{{ trans('global.cancel') }}</a>
            </div>
        </form>


    </div>
</div>
@endsection
@section('scripts')

    <script>
        $("#appointment_created_bcc_emails").change(function (){
                validarEmail();
        });

        let error = false;
        let textError = $('.errorEmail');
        function validarEmail() {
           const expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            let value = $('#appointment_created_bcc_emails').val()
            let recordValues = value.split(',');

            recordValues.forEach(function(value){
                // validarEmail(value);
                if (!expr.test(value) ){
                    error = true;
                    textError.css('color','red');
                    textError.html('Debe ingresar un email valido!');
                    setTimeout(function() {
                        textError.html('');
                    }, 5000);
                }else{
                    error = false;
                }
            });



        }

        $('#locationForm').submit(function(e){
            e.preventDefault();
console.log(error);
            if (!error){
                $(this).unbind('submit').submit();
            }else{
                textError.html('Debe ingresar un email valido!');
                setTimeout(function() {
                    textError.html('');
                }, 5000);
            }
        })
    </script>
@endsection












