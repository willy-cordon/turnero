@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('global.create') }} {{ trans('scheduler.appointments.title_singular') }}</title>

@endsection
@section('content')
<a class="back-link" href="{{route('scheduler.activity-instances.allVigilance')}}"><i class="fas fa-long-arrow-alt-left"></i> {{trans('scheduler.activities.title')}}</a>
<div class="card">
    <div class="card-header">
        {{ trans('global.create') }}
        {{ trans('scheduler.activities.title_singular') }}

    </div>

    <div class="card-body" style="min-height: 650px">
        <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method($method)

            <div class="form-row">
                <div class="form-group col-md-2 {{ $errors->has('client') ? 'has-error' : '' }}">
                    <label for="client">{{ trans('scheduler.appointments.fields.client') }}*</label>
                    <select name="client" id="client" class="form-control to-select2" data-placeholder="{{trans('scheduler.appointments.fields.client_placeholder')}}">
                        @if(count($relatedModels["clients"])>1)
                            <option></option>
                            @foreach($relatedModels["clients"] as $client)
                                <option value="{{ $client->id }}" {{ (collect(old('client'))->contains($client->id)) ? 'selected':'' }}>{{ $client->name }}</option>
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
                    <label for="supplier_select">{{ trans('scheduler.activity_instances.fields.supplier') }}*</label>
                    <div class="controls">
                        <div class="input-group">
                            <input type="hidden" name="supplier" id="supplier" value="{{ old('supplier') }}">
                            <select name="supplier_select" id="supplier_select" class="form-control"  disabled data-allow-clear="true" data-placeholder="{{trans('scheduler.activity_instances.fields.supplier_placeholder')}}">

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
                    <p class="helper-block">
                        {{ trans('scheduler.activity_instances.fields.supplier_helper') }}
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
                <div class="form-group col-md-6 {{ $errors->has('appointments') ? 'has-error' : '' }}">
                    <label for="appointment_select">{{ trans('scheduler.activity_instances.fields.appointment') }}*</label>
                    <div class="controls">
                        <div class="input-group">
                            <input type="hidden" name="appointment" id="appointment" value="{{ old('appointment') }}">
                            <select name="appointment_select" id="appointment_select" class="form-control to-select2"  disabled data-allow-clear="false" data-placeholder="{{trans('scheduler.activity_instances.fields.appointment_placeholder')}}">

                            </select>

                        </div>
                    </div>
                    @if($errors->has('appointment'))
                        <em class="invalid-feedback">
                            {{ $errors->first('appointment') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.activity_instances.fields.appointment_helper') }}
                    </p>
                </div>

            </div>
            <div class="form-row">
                <div class="form-group col-md-8 {{ $errors->has('activity') ? 'has-error' : '' }}">
                    <label for="activity_select">{{ trans('scheduler.activity_instances.fields.activity') }}*</label>
                    <div class="controls">
                        <div class="input-group">
                            <input type="hidden" name="activity" id="activity" value="{{ old('activity') }}">
                            <select name="activity_select" id="activity_select" class="form-control to-select2"  disabled data-allow-clear="false" data-placeholder="{{trans('scheduler.activity_instances.fields.activity_placeholder')}}">

                            </select>

                        </div>
                    </div>
                    @if($errors->has('activity'))
                        <em class="invalid-feedback">
                            {{ $errors->first('activity') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.activity_instances.fields.activity_helper') }}
                    </p>
                </div>
                <div class="col-md-4 form-group {{ $errors->has('date') ? 'has-error' : '' }}">
                    <label for="date">{{ trans('scheduler.activity_instances.fields.date') }}*</label>
                    <div class='input-group ai-date'>
                        <input style="background: #FFF;" type='text' id="date" name="date" class="form-control"  value="{{ old('date') }}" readonly/>
                        <span class="input-group-addon date-picker-button">
                            <span class="glyphicon glyphicon-calendar">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                        </span>
                    </div>
                    @if($errors->has('date'))
                        <em class="invalid-feedback">
                            {{ $errors->first('date') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.activity_instances.fields.date_helper') }}
                    </p>
                </div>
            </div>

            <div class="form-row" id="today_activity" style="display: none">
                <div class="form-group col-md-6">
                    <label for="action">{{ trans('scheduler.activity_instances.fields.action') }} *</label>
                    <select name="action" id="action" class="form-control to-select2" data-minimum-results-for-search="-1" style="width: 100%" data-placeholder="{{trans('scheduler.activity_instances.fields.action_placeholder')}}">

                    </select>
                    <em class="invalid-feedback">
                        {{ trans('scheduler.activity_instances.required') }}
                    </em>
                </div>
                <div class="form-group col-md-6">
                    <label for="status">{{ trans('scheduler.activity_instances.fields.status') }} *</label>
                    <select name="status" id="status" class="form-control to-select2" data-minimum-results-for-search="-1" style="width: 100%" data-placeholder="{{trans('scheduler.activity_instances.fields.status_placeholder')}}">

                        @foreach($statuses as $status)
                            <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
            </div>


            <div>
                <input class="btn btn-success" type="submit" value="{{ trans('global.save') }}">
                <a href="{{ route('scheduler.appointments.index')}}" class="btn btn-danger">{{ trans('global.cancel') }}</a>
            </div>
        </form>



    </div>

    <div id="save-loader" style="display:none; background: rgba(255,255,255,.8); width: 100%; height: 100%; position: absolute; top: 0; text-align: center; padding-top: 100px; z-index: 99999;" >
        <i class="fas fa-circle-notch fa-spin fa-3x fa-fw"></i>
        <span class="sr-only">Guardando...</span>
    </div>
</div>

@endsection
@section('scripts')
    @parent

    <script>
        var activities = @json($relatedModels["activities"]);
        var activityActions = @json($relatedModels["activity_actions"]);
        var activityQuestions = @json($relatedModels["activity_questions"]);
        $(function () {
            $('.ai-date').datetimepicker({
                format: 'DD/MM/YYYY',
                locale: 'es',
                ignoreReadonly: true,
                useCurrent: false,
            }).on("dp.change",function(e){
                if(e.date.isSame(new Date(), 'day')){
                    $("#today_activity").show();
                }else{
                    $("#today_activity").hide();
                }
            });

            /*date data*/
            var supplier_select = $("#supplier_select");

            var clientSelect = $("#client");

            /*Client*/
            clientSelect.on("select2:select", function (evt) {
                var data = evt.params.data;
                supplier_select.prop('disabled',false);
            });


            if($("#client option").length === 1){
                clientSelect.prop('disabled', true);
                supplier_select.prop('disabled',false);
            }

            /*Supplier*/
            supplier_select.on("change", function(){
                var supplier = $(this).val();
                $("#supplier").val(supplier);

                //Set
                if(supplier === '' || supplier === null) {
                    $("#view-supplier-button").prop('disabled',true);
                }else{
                    $("#view-supplier-button").prop('disabled',false);
                }

                //Set Appointments
                setAppointments(supplier);
            });



            supplier_select.select2({
                containerCssClass: "container-suppliers",
                placeholder: '{{trans('scheduler.suppliers.search')}}',
                minimumInputLength: 0,
                ajax : {
                    url: "{{ route('scheduler.suppliers.search') }}",
                    dataType: 'json',
                    type: 'GET',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            client_id: $("#client").val()
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    },
                }
            });

            /*Appointment*/
            $('#appointment_select').on("change", function(){
                var $appointment = $(this).val();

                $("#appointment").val($appointment);
                //Set Activities
                setActivities($(this).select2('data')[0].location_id);
            });
            /*Activity*/
            $('#activity_select').on("change", function(){
                var $activity = $(this).val();
                $("#activity").val($activity);
                //Set Activity Actions
                setActivityActions($activity);
                //Set today
                $('#date').val(moment(new Date()).format("DD/MM/YYYY")).trigger('change');
                //$('.ai-date').trigger('dp.change');
            });

        });



        function viewSupplier() {
            var supplier = $("#supplier").val();
            if(supplier !== '' && supplier !== null) {
                $("#supplier-show .modal-body").load("/scheduler/suppliers/" + supplier + " .card-body");
                $("#supplier-show").modal('show');
            }
        }

        function setAppointments(supplier_id) {
            $.ajax({
                method: "GET",
                url: "{{ route('scheduler.appointments.get-by-supplier') }}",
                data: { supplier_id: supplier_id
                },
                success: function(response){
                    response.push({id:"", text:""});
                    var appointments_select = $("#appointment_select");

                    appointments_select.empty().select2(
                        {
                            data:response,
                            templateSelection: function (data, container) {
                                $(data.element).attr('data-location_id', data.location_id);
                                return data.text;
                            }
                        }
                    ).trigger('change');

                    if(response.length === 1){
                        appointments_select.prop('disabled', true);
                    }else {
                        appointments_select.prop('disabled', false);
                    }
                }
            })
        }

        function setActivities($location_id) {

            var activity_select = $("#activity_select");

            var location_activities = activities[$location_id];
            if(location_activities === undefined){
                location_activities=[];
            }
            activity_select.empty().select2(
                {
                    data:location_activities,
                    templateSelection: function (data, container) {

                        return data.text;
                    }
                }
            ).trigger('change');

            if(location_activities.length === 0){
                activity_select.prop('disabled', true);
            }else {
                activity_select.prop('disabled', false);
            }
        }

        function setActivityActions($activity_id) {
            var action_select = $("#action");
            $("#answer_label").html(activityQuestions[$activity_id]);
            var activity_actions = activityActions[$activity_id];
            if(activity_actions === undefined){
                activity_actions=[];
            }

            console.log(activity_actions);
            action_select.empty().select2(
                {
                    data:activity_actions,
                    templateSelection: function (data, container) {

                        return data.text;
                    }
                }
            );

            if(activity_actions.length === 0){
                action_select.prop('disabled', true);
            }else {
                action_select.prop('disabled', false);
            }
        }



    </script>
@endsection
