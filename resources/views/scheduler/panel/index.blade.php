@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }}</title>
    <link href="{{ asset('js/scheduler/dhtmlxscheduler_material.css') }}" rel="stylesheet" />
@endsection
@section('content')
<div class="content">
    <div class="row">
        <div class="col-sm-12 scheduler-container" style="overflow: auto;">

            <div style="margin-bottom: 10px;" class="row">
                <div class="col-sm-6" style="text-align: left">
                    <h4>Panel de turnos - {{$location->name}}</h4>
                    <div class="references col-sm-12">
                        <span class="reservation"></span><span class="reference-text"> Confirmado</span>
                        <span class="regular"></span><span class="reference-text"> En sitio</span>
                        <span class="synchronized"></span><span class="reference-text"> Cumplido</span>
                    </div>
                </div>
                <div class="col-sm-6" style="text-align: right">

                    <a class="btn btn-success" href="{{ route("scheduler.appointments.create",$location->id) }}">
                        <i class="fas fa-plus"></i> {{ trans('global.add') }} {{ trans('scheduler.appointments.title_singular') }} {{$location->name}}
                    </a>

                    <a class="btn btn-primary" href="{{ route("scheduler.appointments.index") }}">
                        <i class="fas fa-list"></i> {{ trans('global.view') }} {{ trans('global.list') }}
                    </a>
                </div>

            </div>


            <div style="float: left; width: 100%; min-width:1000px;background: #FFF; position:relative">
                <div style="float: left; width: 100%; padding:10px;">

                    <div class="calendar-picker-container" style="top:23px;"><i class="fas fa-calendar-alt"></i><input type="text" class="calendar-picker" id="go-to-date"></div>
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
@endsection

@section('scripts')
    @parent
    <script src="{{ asset('js/scheduler/dhtmlxscheduler.js') }}"></script>
    <script src="{{ asset('js/scheduler/dhtmlxscheduler_units.js') }}"></script>
    <script src="{{ asset('js/scheduler/dhtmlxscheduler_limit.js') }}"></script>
    <script src="{{ asset('js/scheduler/dhtmlxscheduler_timeline.js') }}"></script>
    <script src="{{ asset('js/scheduler/dhtmlxscheduler_treetimeline.js') }}"></script>
    <script src="{{ asset('js/scheduler/dhtmlxscheduler_multiselect.js') }}"></script>
    <script src="{{ asset('js/scheduler/dhtmlxscheduler_multisection.js') }}"></script>
    <script src="{{ asset('js/scheduler/dhtmlxscheduler_editors.js') }}"></script>
    <script src="{{ asset('js/scheduler/dhtmlxscheduler_collision.js') }}"></script>
    <script src="{{ asset('js/scheduler/dhtmlxscheduler_readonly.js') }}"></script>
    <script src="{{ asset('js/scheduler/dhtmlxscheduler_tooltip.js') }}"></script>
    <script src="{{ asset('js/scheduler/dhtmlxscheduler_container_autoresize.js') }}"></script>
    <script src="{{ asset('js/scheduler/locale_es.js') }}"></script>
    <script>
        var sections = @json($relatedModels["schedulerSections"]);
        var scheduler_initiated = false;
        var data = @json($relatedModels["schedulerAppointments"]);
        var init_hour = {{$relatedModels["init_hour"]}};
        var end_hour = {{$relatedModels["end_hour"]}};
        var appointment_init_minutes_size = {{$relatedModels["appointment_init_minutes_size"]}};
        var columns = (end_hour-init_hour) *(60/appointment_init_minutes_size);
        var limits= @json($relatedModels["schedulerLocks"]);
        var blocks = @json($relatedModels["schedulerCellLocks"]);
        $(function () {
            $('#go-to-date').datetimepicker({
                format: 'YYYY-MM-DD',
                locale: 'es'
            })
            $('#go-to-date').on('dp.change', function (e) {
                if(e['date'] != e['oldDate']){
                    console.log( $('#go-to-date').val())
                    scheduler.setCurrentView(new Date($('#go-to-date').val() + ' 00:00:00'));
                }
            });


            init_scheduler()
        });

        function init_scheduler() {

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
                resize_events:false,
                y_unit: sections,
                y_property: "section_id",
                render: "tree",
                folder_dy: 40,
                fit_events:true,
                round_position:false,
                dy:40,
                second_scale:{
                    x_unit: "day", // unit which should be used for second scale
                    x_date: "%F %d" // date format which should be used for second scale, "July 01"
                }
            });

            //INIT Scheduler
            scheduler.config.xml_date = "%d/%m/%Y %H:%i";
            scheduler.config.default_date = "%l %j %F de %Y";
            scheduler.config.date_format = "%d/%m/%Y %H:%i";

            scheduler.templates.timeline_cell_class=function(ev, date, section, row_idx, column_idx){
                var now = new Date();
                var day_limit = limits[moment(date).format('YYYYMMDD')];

                cell_number = ((row_idx-1) * columns) + (column_idx + 1);
                if (date < now || cell_number > day_limit ) {
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


            scheduler.attachEvent("onBeforeEventChanged", function(ev, e, is_new, original){
                return false;
            });
            scheduler.attachEvent("onBeforeDrag", function (id, mode, event) {
                return false;
            });
            /**/

            /*Template event*/
            scheduler.templates.event_class = function(start,end,ev){

                if(ev.is_reservation){
                    $other_class = ' reservation';
                }

                if(ev.action_id == 2){
                    $other_class = ' synchronized';
                }

                if(ev.action_id == 5){
                    $other_class = ' regular';
                }

                return "other_than_me section_id_"+ev.section_id + $other_class;
            };

            scheduler.templates.event_bar_text = function(start,end,ev){
                return '<div style="position: absolute; color: #fff; width: 100%; text-align: center; left: 50%; transform: translate(-50%, -50%); font-size: 10px; top: 50%;">'+addZero(start.getHours())+':'+addZero(start.getMinutes())+'</div>';
            };
            /**/
            /*lightbox*/
            scheduler.config.lightbox.sections=[];
            scheduler.attachEvent("onBeforeLightbox", function (id){
                var ev = scheduler.getEvent(id);

                if(ev.action_id != 2 && ev.action_id != 4 ){
                    window.open('{{route("scheduler.appointments.store")}}/'+ev.appointment_id+'/edit', '_blank');
                }else{
                    window.open('{{route("scheduler.appointments.store")}}/'+ev.appointment_id, '_blank');
                }
                return false;
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

        }





</script>
@endsection