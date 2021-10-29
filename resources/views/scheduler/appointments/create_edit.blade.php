@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('global.create') }} {{ trans('scheduler.appointments.title_singular') }}</title>
    <link href="{{ asset_versioned('js/scheduler/dhtmlxscheduler_material.css') }}" rel="stylesheet" />
@endsection
@section('content')
<a class="back-link" href="{{route('scheduler.appointments.index')}}"><i class="fas fa-long-arrow-alt-left"></i> {{trans('scheduler.appointments.title')}}</a>
<div class="card">
    <div class="card-header">
        {{ isset($appointment)? trans('global.edit') : trans('global.create') }}
        {{ trans('scheduler.appointments.title_singular') }}
        @if(isset($appointment)) - <b>Nro: {{ $appointment->id}}</b>@endif
        - {{ trans('scheduler.appointments.fields.type') }}: <strong>{{$location->name}}</strong>
    </div>

    <div class="card-body" style="min-height: 650px">
        <form action="{{ $action }}" method="POST" enctype="multipart/form-data" onsubmit="return checkAvailableSpot();">
            @csrf
            @method($method)
            <div  style="position: absolute; top: 6px; right: 0; margin-right: 18px; visibility: hidden">
                <input name="is_reservation" type="hidden" value="0"/>
                <input id="is_reservation"
                       name="is_reservation"
                       value="1"
                       type="checkbox"
                       data-toggle="toggle"
                       data-on="{{ trans('scheduler.appointments.reservation') }}"
                       data-off="{{ trans('scheduler.appointments.title_singular') }}"
                       data-onstyle="warning" data-offstyle="primary"
                       checked >


            </div>
            <div class="form-row">
                <div class="form-group col-md-2 {{ $errors->has('client') ? 'has-error' : '' }}">
                    <label for="client">{{ trans('scheduler.appointments.fields.client') }}*</label>
                    <select name="client" id="client" class="form-control to-select2" data-placeholder="{{trans('scheduler.appointments.fields.client_placeholder')}}">
                        @if(count($relatedModels["clients"])>1)
                            <option></option>
                            @foreach($relatedModels["clients"] as $client)
                                <option value="{{ $client->id }}" {{ (collect(old('client', isset($appointment) ? $appointment->client_id : '' ))->contains($client->id)) ? 'selected':'' }}>{{ $client->name }}</option>
                            @endforeach
                        @else
                            @if(count($relatedModels["clients"])==1)
                                <option value="{{ $relatedModels["clients"][0]->id }}" selected>{{ $relatedModels["clients"][0]->name }}</option>
                            @endif
                        @endif
                    </select>

                    @if($errors->has('client'))
                        <em class="invalid-feedback">
                            {{ $errors->first('client') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.appointments.fields.client_helper') }}
                    </p>
                </div>
                <div class="form-group col-md-4 {{ $errors->has('supplier') ? 'has-error' : '' }}">
                    <label for="supplier">{{ trans('scheduler.appointments.fields.supplier') }}*</label>
                    <div class="controls">
                        <div class="input-group">
                            <input type="hidden" name="supplier" id="supplier" value="{{ old('supplier',  isset($appointment) ? $appointment->supplier_id : '') }}">
                            <select name="supplier_select" id="supplier_select" class="form-control to-select2"  disabled data-allow-clear="true" data-placeholder="{{trans('scheduler.appointments.fields.supplier_placeholder')}}">

                            </select>
                            <span class="input-group-append">
                                <button id="view-supplier-button" class="btn btn-primary" type="button" disabled onclick="viewSupplier();" style="min-width: 42px"><i class="fas fa-eye"></i></button>
                            </span>
                        </div>
                    </div>
                    @if($errors->has('supplier'))
                        <em class="invalid-feedback">
                            {{ $errors->first('supplier') }}
                        </em>
                    @endif
                    @if($errors->any())
                        <h4>{{$errors->first()}}</h4>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.appointments.fields.supplier_helper') }}
                    </p>
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
                </div>

                <div class="form-group col-md-2 {{ $errors->has('action') ? 'has-error' : '' }}">
                    <label for="action">{{ trans('scheduler.appointments.fields.action') }}*</label>
                    <select name="action" id="action" class="form-control to-select2" data-minimum-results-for-search="-1" data-placeholder="{{trans('scheduler.appointments.fields.action_placeholder')}}">
                        <option></option>
                        @foreach($relatedModels["appointmentActions"] as $appointmentAction)
                            <option value="{{ $appointmentAction->id }}" {{ (collect(old('action', isset($appointment) ? $appointment->action_id : ''))->contains($appointmentAction->id)) ? 'selected':'' }}>{{ $appointmentAction->name }}</option>
                        @endforeach
                    </select>

                    @if($errors->has('action'))
                        <em class="invalid-feedback">
                            {{ $errors->first('action') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.appointments.fields.action_helper') }}
                    </p>
                </div>
                <div class="form-group col-md-2 {{ $errors->has('transportation') ? 'has-error' : '' }}">
                    <label for="transportation">{{ trans('scheduler.appointments.fields.transportation') }}*</label>
                    <select name="transportation" id="transportation" class="form-control to-select2" data-minimum-results-for-search="-1" data-placeholder="{{trans('scheduler.appointments.fields.transportation_placeholder')}}">
                        <option></option>
                        @foreach($relatedModels["appointmentTransportation"] as $transportation)
                            <option value="{{ $transportation }}" {{ (collect(old('type', isset($appointment) ? $appointment->transportation : ''))->contains($transportation)) ? 'selected':'' }}>{{ $transportation }}</option>
                        @endforeach
                    </select>

                    @if($errors->has('transportation'))
                        <em class="invalid-feedback">
                            {{ $errors->first('transportation') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.appointments.fields.transportation_helper') }}
                    </p>
                </div>
                <div class="form-group col-md-2">
                    <label for="need_assistance">{{ trans('scheduler.appointments.fields.need_assistance') }}*</label>
                    <div style="clear: both"></div>
                    <input name="need_assistance" type="hidden" value="0"/>
                    <input id="need_assistance"
                           name="need_assistance"
                           value="1"
                           type="checkbox"
                           data-toggle="toggle"
                           data-on="{{ trans('global.yes') }}"
                           data-off="{{ trans('global.no') }}"
                           data-onstyle="success" data-offstyle="primary"
                            {{ ( old('need_assistance',  isset($appointment) ? $appointment->need_assistance : '' ) == 1 ? 'checked':'') }}>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-2 {{ $errors->has('next_step') ? 'has-error' : '' }}" style="display: none">
                    <label for="next_step">{{ trans('scheduler.appointments.fields.next_step') }}*</label>
                    <select name="next_step" id="next_step" class="form-control to-select2" data-minimum-results-for-search="-1" data-placeholder="{{trans('scheduler.appointments.fields.next_step_placeholder')}}">
                        <option></option>
                        @foreach($relatedModels["appointmentNextSteps"] as $nextStep)
                            <option value="{{ $nextStep }}" {{ (collect(old('type', isset($appointment) ? $appointment->next_step : ''))->contains($nextStep)) ? 'selected':'' }}>{{ $nextStep }}</option>
                        @endforeach
                    </select>

                    @if($errors->has('next_step'))
                        <em class="invalid-feedback">
                            {{ $errors->first('next_step') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.appointments.fields.next_step_helper') }}
                    </p>
                </div>


            </div>

            <div class="form-row">
               <div class="form-group col-md-12">
                    <div class="form-row">
                        <div class="form-group col-md-12 {{ $errors->has('comments') ? 'has-error' : '' }}">
                            <label for="comments">{{ trans('scheduler.appointments.fields.comments') }}</label>
                            <textarea id="comments" name="comments" style="min-height: 60px" class="form-control">{{ old('comments', isset($appointment) ? $appointment->comments : '') }} </textarea>
                            @if($errors->has('comments'))
                                <em class="invalid-feedback">
                                    {{ $errors->first('comments') }}
                                </em>
                            @endif
                            <p class="helper-block">
                                {{ trans('scheduler.appointments.fields.comments_helper') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="rangeDayFrom" name="rangeDayFrom">
            <input type="hidden" id="rangeDayTo" name="rangeDayTo">

            <div class="form-row">

            </div>

            <div class="form-row">

                <fieldset class="form-group col-md-12" style="border-bottom: 1px solid #e5e5e5;">
                    <legend>{{ trans('scheduler.appointments.fields.date_hour') }}</legend>
                    <h6 style="margin-left:0; margin-bottom: 10px" class="valid-dates-for-appointment"></h6>
                    <div class="form-row">
                        <div  id="appointment_data" class="form-group col-md-8 {{ $errors->has('start_date') ? 'has-error' : '' }}" >
                            <div class="col-md-3 date-data-field" style="padding-left: 0">
                                <label>{{ trans('scheduler.appointments.fields.date') }}</label>
                                <input type="text" disabled class="form-control a-date">
                            </div>
                            <div class="col-md-2 date-data-field">
                                <label>{{ trans('scheduler.appointments.fields.hour') }}</label>
                                <input type="text" disabled class="form-control from">
                            </div>

                            <div class="col-md-2 date-data-field">
                                <label>{{ trans('scheduler.appointments.fields.dock') }}</label>
                                <input type="text" disabled class="form-control dock">
                            </div>
                            <div class="col-md-3">
                                <label style="float: left; width: 100%; height: 21px;"></label>
                                <button class="btn btn-primary" type="button" onclick="$('#appointment-scheduler-show').modal('show'); setTimeout(function(){init_scheduler()},500);" style="min-width: 42px"> <i class="far fa-calendar-alt"></i> {{trans('scheduler.appointments.fields.pick_date')}}</button>
                            </div>
                            @if($errors->has('start_date'))
                                <em class="invalid-feedback" style="float:left;">
                                    {{ $errors->first('start_date') }}
                                </em>
                            @endif
                        </div>

                        <div class="form-group col-md-2 offset-md-2 {{ $errors->has('required_date') ? 'has-error' : '' }}" style="display: none">
                            <label for="pallets_qty">{{ trans('scheduler.appointments.fields.required_date') }}*</label>
                            <input type="text" id="required_date" name="required_date" class="form-control datetime" value="{{ old('required_date',isset($appointment) ? $appointment->required_date : '') }}">
                            @if($errors->has('required_date'))
                                <em class="invalid-feedback">
                                    {{ $errors->first('required_date') }}
                                </em>
                            @endif
                            <p class="helper-block">
                                {{ trans('scheduler.appointments.fields.required_date_helper') }}
                            </p>
                        </div>
                    </div>
                </fieldset>


                <input type="hidden" name="start_date" id="start_date" value="{{ old('start_date',isset($appointment) ? $appointment->start_date : '') }}"/>
                <input type="hidden" name="end_date" id="end_date" value="{{ old('end_date',isset($appointment) ? $appointment->end_date : '') }}"/>
                <input type="hidden" name="dock" id="dock" data-dock-name="{{ old('dock',isset($appointment) ? $appointment->dock->name : '') }}" value="{{ old('dock',isset($appointment) ? $appointment->dock->id : '') }}"/>
                <input type="hidden" name="event_id" id="event_id" value="{{ old('event_id',isset($appointment) ? $appointment->id : '') }}"/>
                <input type="hidden" id="appointment_id" name="appointment_id" value="{{ old('appointment_id',isset($appointment) ? $appointment->id : '') }}"/>

                <div class="modal fade" id="appointment-scheduler-show" tabindex="-1" role="dialog" aria-labelledby="appointmentSchedulerShowModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="appointmentSchedulerShowModalLabel">{{ trans('scheduler.appointments.fields.pick_date') }}</h5>
                                <h6 class="valid-dates-for-appointment"></h6>

                                <input class="btn btn-success" style="right: 0; position: absolute; top: 12px; transform: translate(-50%, 0%);" type="button" onclick="$('#appointment-scheduler-show').modal('hide');" value="{{trans('global.accept')}}">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body" style="overflow: auto">
                                <div style="float: left; width: 100%; min-width:1100px; ">
                                    <div style="float: left; height: 800px; width: 100%">
                                        <div class="calendar-picker-container" style="top:30px"><i class="fas fa-calendar-alt"></i><input type="text" class="calendar-picker" id="go-to-date"></div>
                                        <div id="scheduler_here" class="dhx_cal_container" style='width:100%; height:100%;'>
                                            <div class="dhx_cal_navline">
                                                <div class="dhx_cal_prev_button">&nbsp;</div>
                                                <div class="dhx_cal_next_button">&nbsp;</div>
                                                <div class="dhx_cal_today_button"></div>
                                                <div class="dhx_cal_date"></div>

                                            </div>
                                            <div class="dhx_cal_header">
                                            </div>
                                            <div class="dhx_cal_data">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>


            </div>


            <div>
                <input class="btn btn-success" type="submit" value="{{ trans('global.save') }}">
                <a href="{{ route('scheduler.appointments.index')}}" class="btn btn-danger">{{ trans('global.cancel') }}</a>
            </div>
        </form>

        <div class="modal fade" id="already-taken-show" tabindex="-1" role="dialog" aria-labelledby="alreadyTakenModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="alreadyTakenModalLabel">El turno ya fue tomado por otro reclutador</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Mientras completabas los datos, otro reclutador utilizó tu misma fecha, hora y circuito. Por favor seleccioná otra.
                    </div>

                </div>
            </div>
        </div>

    </div>

    <div id="save-loader" style="display:none; background: rgba(255,255,255,.8); width: 100%; height: 100%; position: absolute; top: 0; text-align: center; padding-top: 100px; z-index: 99999;" >
        <i class="fas fa-circle-notch fa-spin fa-3x fa-fw"></i>
        <span class="sr-only">Guardando...</span>
    </div>
</div>
<input type="hidden" id="prev_action_id" value="{{$location->prev_action_id}}">
<input type="hidden" id="prev_location_id" value="{{$location->prev_location_id}}">
<input type="hidden" id="current_location_id" value="{{$location->id}}">
<input type="hidden" id="current_supplier_id" value="{{isset($appointment) ? $appointment->supplier_id : ''}}">
<input type="hidden" id="enable_past_days" value="{{$location->enable_past_days}}">
<input type="hidden" id="prev_location_id_workflow" value="{{$location->prev_location_id_workflow}}">


@endsection
@section('scripts')
    @parent
    <script src="{{ asset_versioned('js/scheduler/dhtmlxscheduler.js') }}"></script>
    <script src="{{ asset_versioned('js/scheduler/dhtmlxscheduler_units.js') }}"></script>
    <script src="{{ asset_versioned('js/scheduler/dhtmlxscheduler_limit.js') }}"></script>
    <script src="{{ asset_versioned('js/scheduler/dhtmlxscheduler_timeline.js') }}"></script>
    <script src="{{ asset_versioned('js/scheduler/dhtmlxscheduler_treetimeline.js') }}"></script>
    <script src="{{ asset_versioned('js/scheduler/dhtmlxscheduler_multiselect.js') }}"></script>
    <script src="{{ asset_versioned('js/scheduler/dhtmlxscheduler_multisection.js') }}"></script>
    <script src="{{ asset_versioned('js/scheduler/dhtmlxscheduler_editors.js') }}"></script>
    <script src="{{ asset_versioned('js/scheduler/dhtmlxscheduler_collision.js') }}"></script>
    <script src="{{ asset_versioned('js/scheduler/dhtmlxscheduler_readonly.js') }}"></script>
    <script src="{{ asset_versioned('js/scheduler/dhtmlxscheduler_tooltip.js') }}"></script>
    <script src="{{ asset_versioned('js/scheduler/dhtmlxscheduler_container_autoresize.js') }}"></script>
    <script src="{{ asset_versioned('js/scheduler/locale_es.js') }}"></script>
    <script>
        var sections = @json($relatedModels["schedulerSections"]);
        var scheduler_initiated = false;
        var data = @json($relatedModels["schedulerAppointments"]);
        var init_hour = {{$relatedModels["init_hour"]}};
        var end_hour = {{$relatedModels["end_hour"]}};
        var appointment_init_minutes_size = {{$relatedModels["appointment_init_minutes_size"]}};

        var columns = (end_hour-init_hour) *(60/appointment_init_minutes_size);
        var limits= @json($relatedModels["schedulerLocks"]);
        var prev_days_from= '{{$relatedModels["prev_days_from"]}}';
        var prev_days_to= '{{$relatedModels["prev_days_to"]}}';
        var limit_from = null;
        var limit_to = null;

        var blocks = @json($relatedModels["schedulerCellLocks"]);

        function init_scheduler() {

           if(scheduler_initiated === false){
               scheduler_initiated = true;
               var event_id = $("#event_id").val();
               var appointment_id =$("#appointment_id").val();

               if(appointment_id != ''){
                   //edición
                   data = data.filter(function (obj) {
                       return obj.appointment_id != appointment_id;
                   });
               }
               if(event_id != ''){
                   data.push(
                       {   id: event_id,
                           start_date: $('#start_date').val(),
                           end_date: $('#end_date').val(),
                           text:"new_appointment",
                           section_id:$('#dock').val()
                       }
                   )
               }
               $('#go-to-date').datetimepicker({
                   format: 'YYYY-MM-DD',
                   locale: 'es'
               })
               $('#go-to-date').on('dp.change', function (e) {
                   if(e['date'] != e['oldDate']){
                       scheduler.setCurrentView(new Date($('#go-to-date').val() + ' 00:00:00'));
                   }
               });
           }else{

               scheduler.updateView();
               update_calendar_limit();
               return;
           }

            scheduler.config.multisection = true;

            scheduler.createTimelineView({
                name: "timeline",
                x_unit: "minute",
                x_date: "%H:%i",
                x_step:    appointment_init_minutes_size, //30
                x_start:   init_hour*(60/appointment_init_minutes_size), //16
                x_size:    (end_hour-init_hour) *(60/appointment_init_minutes_size), //24
                x_length:  (24*60)/appointment_init_minutes_size, //48
                event_dy:  36,
                resize_events:true,
                y_unit: sections,
                y_property: "section_id",
                render: "tree",
                folder_dy: 40,
                fit_events:true,
                round_position:true,
                dy:40,
                second_scale:{
                    x_unit: "day", // unit which should be used for second scale
                    x_date: "%F %d" // date format which should be used for second scale, "July 01"
                }
            });

            //scheduler.date.timeline_start = scheduler.date.day_start;

            //INIT Scheduler
            scheduler.config.xml_date = "%d/%m/%Y %H:%i";
            scheduler.config.default_date = "%l %j %F de %Y";
            scheduler.config.date_format = "%d/%m/%Y %H:%i";
            scheduler.config.drag_resize= true;
            scheduler.config.time_step = 15;
            scheduler.config.event_duration =  90;
            scheduler.config.first_hour = 8;
            scheduler.config.last_hour = 20;
            scheduler.config.limit_time_select = true;
            scheduler.config.drag_create = false;
            scheduler.config.collision_limit = 1;

            scheduler.locale.labels.confirm_deleting = null;

            scheduler.templates.timeline_cell_class=function(ev, date, section, row_idx, column_idx){
                var now = new Date();
                var day_limit = limits[moment(date).format('YYYYMMDD')];

                cell_number = ((row_idx-1) * columns) + (column_idx + 1);


                if(jQuery("#enable_past_days").val() != 1 && date < now) {
                    return "disabled-cell";
                }

                if (cell_number > day_limit ) {
                    return "disabled-cell";
                }

                var day_block = moment(date).format('YYYYMMDD');

                if( blocks[day_block +'_'+row_idx+'-'+column_idx] === 1){ //CELL
                    return "disabled-cell";
                }

                if( blocks[day_block +'_C-'+column_idx] === 1){ //COLUMN
                    return "disabled-cell";
                }

                if( blocks[day_block +'_R-'+row_idx] === 1){ //ROW
                    return "disabled-cell";
                }


                if($('#event_id').val() === '')
                    return "available-cell";

                return '';
            };


            /*control de arrastre y edicion de fecha al soltar*/
            var dragged_event;
            scheduler.attachEvent("onBeforeDrag", function (id, mode, e){
                dragged_event=scheduler.getEvent(id); //use it to get the object of the dragged event
                return true;
            });

            scheduler.attachEvent("onDragEnd", function(){
                var event_obj = dragged_event;
                if(typeof dragged_event != 'undefined' && event_obj.id == jQuery('#event_id').val()){
                    section = scheduler.getSection(event_obj.section_id);
                    update_new_appointment_data(formatDate(event_obj.start_date), formatDate(event_obj.end_date), event_obj.section_id,section.label, event_obj.id);
                }

            });

            scheduler.attachEvent("onBeforeEventChanged", function(ev, e, is_new, original){

                cell_id = '#'+ev.section_id + ev.start_date.getFullYear()+ev.start_date.getMonth()+ev.start_date.getDate()+ev.start_date.getHours()+ev.start_date.getMinutes();

                if(jQuery(cell_id).hasClass('disabled-cell')) {
                    return false;
                }

                if(jQuery("#enable_past_days").val() == 1) {
                    if (ev.id == jQuery('#event_id').val()) {
                        scheduler.getEvent(ev.id).start_date = original.start_date;
                        scheduler.getEvent(ev.id).end_date = original.end_date;
                        scheduler.updateEvent(ev.id);
                        scheduler.updateEvent(original);
                        return true;
                    } else {
                        if (jQuery('#event_id').val() == '') {
                            return true
                        }
                        return false;
                    }
                }else{
                    if (ev.start_date < +new Date()){
                        if(ev.id ==  jQuery('#event_id').val()) {
                            scheduler.getEvent(ev.id).start_date = original.start_date;
                            scheduler.getEvent(ev.id).end_date = original.end_date;
                            scheduler.updateEvent(ev.id);
                            scheduler.updateEvent(original);
                            return true;
                        }else{
                            return false;
                        }
                    }else{
                        return ev.id == jQuery('#event_id').val() || jQuery('#event_id').val() == '';
                    }
                }

            });

            scheduler.attachEvent("onEventCollision", function (ev, evs){
                return true;
            });

            scheduler.attachEvent("onBeforeDrag", function (id, mode, event) {
                if(jQuery("#enable_past_days").val() == 1){
                    return id == jQuery('#event_id').val()
                }else{
                    return !(+scheduler.getActionData(event).date < +new Date() || id != jQuery('#event_id').val())
                }

            });

            scheduler.attachEvent("onBeforeViewChange", function(old_mode,old_date,mode,date){

                //ES ESPECIFICO POR UN DIA
                if(moment(date).format('YMD') === '2020828' && jQuery("#current_location_id").val() == 1){
                    special_end_hour = 24;
                    scheduler.matrix["timeline"].x_size = (special_end_hour-init_hour) *(60/appointment_init_minutes_size);
                }else{
                    scheduler.matrix["timeline"].x_size = (end_hour-init_hour) *(60/appointment_init_minutes_size);
                }



               /* if(limit_from != null) {
                    var limitStart = moment(limit_from)._d;
                    var limitEnd = moment(limit_to).add(1, 'days')._d;

                    if (old_mode && (date.getTime() < limitStart.getTime()) || (date.getTime() > limitEnd.getTime()))
                        return false;
                }*/
                return true;

            });
            /**/

            /*Template event*/
            scheduler.templates.event_class = function(start,end,ev){

                $other_class = ' its_me';
                if(ev.text === 'appointment'){
                    $other_class = ' other_than_me';
                }
                return "section_id_"+ev.section_id + $other_class;
            };

            scheduler.templates.event_bar_text = function(start,end,ev){
                return '<div style="position: absolute; color: #fff; width: 100%; text-align: center; left: 50%; transform: translate(-50%, -50%); font-size: 10px; top: 50%;">'+addZero(start.getHours())+':'+addZero(start.getMinutes())+'</div>';
            };
            /**/

            /*lightbox*/
            scheduler.config.lightbox.sections=[];
            scheduler.templates.lightbox_header = function(start,end,ev){
                $warning = '';
                if(limit_from != null && (moment(start).isAfter(limit_to, 'day') || moment(start).isBefore(limit_from, 'day'))){
                    $warning = ' - <span style="background: red">FECHA FUERA DE RANGO</span>';
                }

                return scheduler.templates.event_date(start) + $warning;
            };
            scheduler.attachEvent("onBeforeLightbox", function (id){
                var ev = scheduler.getEvent(id);
                if(ev.text === 'appointment'){
                    return false; //los otros no se pueden editar
                }
                return true;
            });

            scheduler.attachEvent("onEventSave",function(id,ev,is_new){
                var event = scheduler.getEvent(id);
                section = scheduler.getSection(event.section_id);
                update_new_appointment_data(formatDate(event.start_date), formatDate(event.end_date), event.section_id,section.label, event.id);
                ev.text = 'current_appointment';
                return true;
            });


            scheduler.attachEvent("onEventDeleted", function(id,ev){
                if($('#event_id').val() == id){
                    update_new_appointment_data('', '', '', '','');
                    scheduler.updateView();
                }
                return true;
            });

            /*---------------------------*/

            /*Create on click*/
            scheduler.attachEvent("onClick", function (id, e) {
                scheduler._on_dbl_click(e);
                return false;
            });

            var setter = scheduler._click.dhx_cal_data;
            scheduler._click.dhx_cal_data = function(e) {

                if (!scheduler._locate_event(e ? e.target : event.srcElement)) {
                    scheduler._on_dbl_click(e || event);
                }
                setter.apply(this, arguments);
            };

            scheduler.attachEvent("onEmptyClick", function (date, e) {
                return false;
            });
            ///

            scheduler.templates.tooltip_text = function(start,end,event) {
                $tooltip = '';
                if(event.start_date !== undefined) {
                    $tooltip += '<b>{{trans("scheduler.appointments.fields.date_hour")}}:</b>' + formatDate(event.start_date) + ' - ' + formatDate(event.end_date).slice(11,16) + "<br/>";
                }
                if(event.client !== undefined) {
                    $tooltip += '<b>{{trans("scheduler.appointments.fields.client")}}:</b>' + event.client + "<br/>";
                }
                if(event.supplier !== undefined) {
                    $tooltip += '<b>{{trans("scheduler.appointments.fields.supplier")}}:</b>' + event.supplier + "<br/>";
                }
                if(event.type !== undefined) {
                    $tooltip += '<b>{{trans("scheduler.appointments.fields.type")}}:</b>' + event.type + "<br/>";
                }
                if(event.action !== undefined) {
                    $tooltip += '<b>{{trans("scheduler.appointments.fields.action")}}:</b>' + event.action + "<br/>";
                }
                if(event.comments !== undefined && event.comments != null) {
                    $tooltip += '<b>{{trans("scheduler.appointments.fields.comments")}}:</b>' + event.comments + "<br/>";
                }
                return $tooltip;
            };

            scheduler.init('scheduler_here', new Date(), "timeline");
            scheduler.parse(data, "json");
            update_calendar_limit();
        }

        function update_calendar_limit() {
            if(limit_from != null){

                //Si tiene dias seteados, no pueden turnar para atras del limite pero si para adelante
                scheduler.config.limit_start = moment(limit_from)._d;
                //scheduler.config.limit_end = moment(limit_to).add(1, 'days')._d // No le da bola al limit_to, peor se necesita
                scheduler.config.limit_end = moment(limit_from).add(365, 'days')._d
                scheduler.config.limit_view = true;
                scheduler.setCurrentView(scheduler.config.limit_start);


                 //Esto que sigue es por si quieren sacar la prohibicion
                /*scheduler.config.limit_view = false;
                scheduler.setCurrentView(moment(limit_from)._d);*/
            }else{
                scheduler.config.limit_view = false;
                scheduler.setCurrentView(new Date());
            }

        }


        function update_new_appointment_data(start_date, end_date, section_id, section_name, id){
            jQuery("#start_date").val(start_date).trigger('change');
            jQuery("#end_date").val(end_date).trigger('change');
            jQuery("#dock").val(section_id).data('dock-name',section_name).trigger('change');
            jQuery("#event_id").val(id);
        }

        function show_appointment_data(){
            var start_date = $("#start_date").val();
            if(start_date !== '') {
                $("#appointment_data .a-date").val(start_date.slice(0, 10));
                $("#appointment_data .from").val(start_date.slice(11, 16));
               // $("#appointment_data .to").val($("#end_date").val().slice(11, 16));
                $("#appointment_data .dock").val($("#dock").data('dock-name'));
                /*if($("#required_date").val() === ""){
                    $("#required_date").val(start_date);
                }*/
                $('.date-data-field').show();

            }else{
                $("#appointment_data .a-date").val('');
                $("#appointment_data .from").val('');
               // $("#appointment_data .to").val('');
                $("#appointment_data .dock").val('');
                $('.date-data-field').hide();
            }

        }



        $(function () {

            /*date data*/
            show_appointment_data();
            $("#start_date, #end_date, #dock").on('change', function () {
                show_appointment_data();
            });

            var clientSelect = $("#client");

            /*is reservation*/
            if($('#is_reservation').prop('checked')){
                $('#supplier_select').prop('disabled', false);
            }

            $('#is_reservation').change(function() {
                if($(this).prop('checked')){
                    $('#supplier_select').prop('disabled', false);
                }
            });

            /*Client*/
            clientSelect.on("select2:select", function (evt) {
                var data = evt.params.data;
                setSuppliers(data.id);

            });

            if($("#client option").length === 1){
                setSuppliers(clientSelect.val());
                clientSelect.prop('disabled', true);
            }

            if(clientSelect.val()){
                setSuppliers(clientSelect.val());
            }

            /*Supplier*/
            $('#supplier_select').on("change", function(){

                var supplier = $(this).val();
                $("#supplier").val(supplier);

                if(prev_days_from !== '' && prev_days_to !== '') {
                    var last_prev_appointment_date = $(this).select2('data')[0].last_prev_appointment_date;
                    if (last_prev_appointment_date != null) {
                        limit_from_date = moment(last_prev_appointment_date, 'DD/MM/YYYY').add(parseInt(prev_days_from), 'days')
                        limit_from = limit_from_date.format('YYYY-MM-DD');
                        limit_to_date = moment(last_prev_appointment_date,'DD/MM/YYYY').add(parseInt(prev_days_to), 'days');
                        limit_to = limit_to_date.format('YYYY-MM-DD');
                        jQuery(".valid-dates-for-appointment").html('Días válidos para el turno - Entre el ' + limit_from_date.format('DD/MM/YYYY') + ' y el ' + limit_to_date.format('DD/MM/YYYY'));
                        jQuery("#rangeDayFrom").val(limit_from_date.format('YYYY-MM-DD'));
                        jQuery("#rangeDayTo").val(limit_to_date.format('YYYY-MM-DD'));
                    } else {
                        limit_from = null;
                        limit_to = null;
                        jQuery(".valid-dates-for-appointment").html('');
                        jQuery("#rangeDayFrom").val('');
                        jQuery("#rangeDayTo").val('');
                    }

                    if($("#appointment_id").val() != '') {
                        $(this).prop('disabled', true);
                    }else {
                        jQuery('#start_date').val('');
                        jQuery('#end_date').val('');
                        jQuery('#dock').val('');
                        if (jQuery('#event_id').val() !== '' && scheduler_initiated === true) {
                            scheduler.deleteEvent(jQuery('#event_id').val());
                            jQuery('#event_id').val('');
                        }
                        $("#appointment_data .a-date").val('');
                        $("#appointment_data .from").val('');
                        $("#appointment_data .dock").val('');
                        $('.date-data-field').hide();
                    }
                }else{
                    if($("#appointment_id").val() != '') {
                        $(this).prop('disabled', true);
                    }
                }

                if(supplier === '' || supplier === null) {
                    $("#view-supplier-button").prop('disabled',true);
                }else{
                    $("#view-supplier-button").prop('disabled',false);
                }
            });

            /*NextStep*/
            if( $('#action').val() === "2") {
                $("#next_step").parent().show();
            }else{
                $("#next_step").parent().hide();
            }
            $('#action').on("change", function(){
                if( $(this).val() === "2") {
                    $("#next_step").parent().show();
                }else{
                    $("#next_step").parent().hide();
                }
            });

        });



        function viewSupplier() {
            var supplier = $("#supplier").val();
            if(supplier !== '' && supplier !== null) {
                $("#supplier-show .modal-body").load("/scheduler/suppliers/" + supplier + " .card-body");
                $("#supplier-show").modal('show');
            }
        }

        function setSuppliers(client_id) {
            $.ajax({
                method: "GET",
                url: "{{ route('scheduler.supplier.get-by-client') }}",
                data: { client_id: client_id,
                        prev_location_id: jQuery('#prev_location_id').val(),
                        prev_action_id: jQuery('#prev_action_id').val(),
                    current_location_id: jQuery('#current_location_id').val(),
                    current_supplier_id: jQuery('#current_supplier_id').val(),
                    prev_location_id_workflow: jQuery('#prev_location_id_workflow').val(),
                },
                success: function(response){
                    response.push({id:"", text:""});
                    var supplier_select = $("#supplier_select");
                    var prev_value = $("#supplier").val();

                    supplier_select.empty().select2(
                        {
                            data:response,
                            templateSelection: function (data, container) {
                                // Add custom attributes to the <option> tag for the selected option
                                $(data.element).attr('data-last_appointment_date', data.last_appointment_date);
                                $(data.element).attr('data-last_prev_appointment_date', data.last_prev_appointment_date);
                                return data.text;
                            }
                        }
                    ).val(prev_value).trigger('change');


                }
            })
        }

        function checkAvailableSpot() {
            jQuery("#save-loader").show();
            var start_date = jQuery('#start_date').val();

            var dock = jQuery('#dock').val()

            if(start_date === '')
                return true;


            $.ajax({
                method: "GET",
                async: false,
                url: "{{ route('scheduler.appointments.check-appointment') }}",
                data: { dock_id: dock, date: start_date, appointment_id:jQuery("#appointment_id").val()},
                success: function(response){
                    var start_date = jQuery('#start_date').val();
                    var end_date = moment(jQuery('#end_date').val(), 'DD/MM/YYYY HH:mm').add(-15, 'minutes').format('DD/MM/YYYY HH:mm');

                    spot1 = response.filter(function (obj) {
                        return obj === start_date;
                    });

                    spot2 = response.filter(function (obj) {
                        return obj === end_date;
                    });

                    if(spot1[0] === start_date || spot2[0] === end_date){
                        jQuery('#start_date').val('');
                        jQuery('#end_date').val('');
                        jQuery('#dock').val('');
                        jQuery('#event_id').val('');
                        alert("Mientras completabas los datos, otro reclutador utilizó tu misma fecha, hora y circuito. Por favor seleccioná otra.");
                    }
                }
            })
        }




    </script>
@endsection
