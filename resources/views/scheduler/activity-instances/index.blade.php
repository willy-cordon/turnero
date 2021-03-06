@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{$pageTitle}}</title>
    <link href="{{ asset('css/datatables.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("scheduler.activity-instances.create") }}">
                <i class="fas fa-plus"></i> {{ trans('global.add') }} {{ trans('scheduler.activities.title_singular') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            {{$pageTitle}}
        </div>

        <div class="card-body">
            <div>
                @if($export_all == true)
                <a href="/scheduler/activity-instances/export" class="btn btn-success" target="_blank">Exportar Todos los registros <i class="fas fa-file-excel"></i></a>
                @endif
                <table class="table table-bordered table-striped table-hover datatable datatable-loading datatable-activity-instances">
                    <thead>

                    <tr>
                        <th class="not-export-col">

                        </th>
                        <th class="not-export-col">
                            {{ trans('global.action') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.activity_instances.fields.date') }}
                        </th>

                        <th class="search-column">
                            {{ trans('scheduler.activity_instances.fields.supplier_name') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.activity_instances.fields.status') }}
                        </th>

                        <th class="search-column">
                            {{ trans('scheduler.activity_instances.fields.activity_group_name') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.activity_instances.fields.activity_name') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.activity_instances.fields.activity_question') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.activity_instances.fields.activity_answer') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.activity_instances.fields.activity_action') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.activity_instances.fields.supplier_dni') }}
                        </th>

                        <th class="search-column">
                            {{ trans('scheduler.activity_instances.fields.supplier_email') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.activity_instances.fields.supplier_phone') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.activity_instances.fields.appointment_nro') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.activity_instances.fields.appointment_status') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.activity_instances.fields.created_by') }}
                        </th>


                    </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot></tfoot>
                </table>
            </div>


            <div class="modal fade" id="edit-dialog" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edici??n de actividad</h5>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="loading" style="text-align: center; /* padding-top: 30px; */ font-weight: bold; width: 100%; height: calc(100% + 40px); background: rgba(255,255,255,0.4); position: absolute; left: 0; z-index: 1; top: -40px;">
                                Guandando...
                            </div>
                            <input type="hidden" id="activity-instance-id">
                            <input type="hidden" id="activity-instance-update-url">
                            <div style="width: 100%; border: 1px solid #ededed; border-radius: 4px; margin-bottom: 20px; ">
                                <h6 class="modal-title" id="editModalSubLabel" style="border-bottom: 1px solid #adadad; margin-bottom: 5px;background: #ededed;  padding: 10px; border-radius: 5px 5px 0 0"></h6>
                                <p style="padding: 10px 10px 0 10px" id="editModalData"></p>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label id="answer_label" for="answer"></label>
                                    <select name="answer" id="answer" class="form-control to-select2" data-minimum-results-for-search="-1" style="width: 100%" data-placeholder="{{trans('scheduler.activity_instances.fields.answer_placeholder')}}">
                                        <option></option>
                                        @foreach($answers as $answer)
                                            <option value="{{ $answer }}">{{ $answer }}</option>
                                        @endforeach
                                    </select>
                                    <em class="invalid-feedback">
                                        {{ trans('scheduler.activity_instances.required') }}
                                    </em>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="action">{{ trans('scheduler.activity_instances.fields.action') }} *</label>
                                    <select name="action" id="action" class="form-control to-select2" data-minimum-results-for-search="-1" style="width: 100%" data-placeholder="{{trans('scheduler.activity_instances.fields.action_placeholder')}}">

                                    </select>
                                    <em class="invalid-feedback">
                                        {{ trans('scheduler.activity_instances.required') }}
                                    </em>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="status">{{ trans('scheduler.activity_instances.fields.status') }} *</label>
                                    <select name="status" id="status" class="form-control to-select2" data-minimum-results-for-search="-1" style="width: 100%" data-placeholder="{{trans('scheduler.activity_instances.fields.status_placeholder')}}">
                                        <option></option>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status }}">{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer" style="position: relative">
                            <div class="loading" style="width: 100%; height: 100%; background: rgba(255,255,255,0.4); position: absolute; left: 0; z-index: 1;"></div>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('global.cancel') }}</button>
                            <button type="button" class="btn btn-primary" onclick="saveActivityInstance();">{{ trans('global.save') }}</button>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <!--Lo necesario para que ande datatables-->
    <script src="{{ asset('js/datatables.js') }}"></script>
    @include('partials.datatables_globals')
    <!-- -->
    <script>
        let table;
        function showActivityInstanceEditDialog(activityInstanceId){
            $(".loading").hide();
            activity_instance_row = $('#ai-'+activityInstanceId);
            $("#editModalSubLabel").html(activity_instance_row.attr('data-edit-title'));
            $("#editModalData").html(activity_instance_row.attr('data-edit-data'));
            $("#answer").val(activity_instance_row.attr('data-answer')).trigger('change');
            $("#status").val(activity_instance_row.attr('data-status')).trigger('change');
            $("#answer_label").html(activity_instance_row.attr('data-question-name')+ ' *');

            $("#action").empty().select2(
                {
                    data:JSON.parse(activity_instance_row.attr('data-actions'))
                }
            ).val(activity_instance_row.attr('data-action')).trigger('change');

            $("#activity-instance-id").val(activityInstanceId);
            $("#activity-instance-update-url").val(activity_instance_row.attr('data-update-url'));

            $("#edit-dialog").modal("show");

        }

        function saveActivityInstance(){
            var error = false;
            var answer = $("#answer");
            if(answer.val() === '' || answer.val() === null) {
                error = true;
                answer.closest('div').addClass('has-error');
            }else{
                answer.closest('div').removeClass('has-error');
            }
            var action = $("#action");
            if(action.val() === '' || action.val() === null) {
                error = true;
                action.closest('div').addClass('has-error');
            }else{
                action.closest('div').removeClass('has-error');
            }

            if(error === false){
                $(".loading").show();
                $.ajax({
                    url: $("#activity-instance-update-url").val(),
                    type:"PUT",
                    data:{
                        "_token": "{{ csrf_token() }}",
                        answer : answer.val(),
                        action : action.val(),
                        status : $("#status").val()
                    },
                    success:function(response){
                        if(response.status === 'ok'){
                            $("#edit-dialog").modal("hide");
                            table.draw();
                        }else {
                            console.log(response);
                        }
                        $(".loading").hide();
                    },
                    error :function( data ) {

                    }
                });
            }

        }

        $(document).ready(function(){
            //Creamos una fila en el head de la tabla y lo clonamos para cada columna
            $('.datatable-activity-instances thead tr').clone(true).appendTo( '.datatable-activity-instances thead' );

            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);

            $.extend(true, $.fn.dataTable.defaults, {
                columnDefs: [
                    {
                        orderable: false,
                        className: 'select-checkbox',
                        targets: 0
                    },
                    {
                        orderable: false,
                        targets: [0,-1]
                    },
                    {
                        searchable: false,
                        targets: '_all'
                    }
                ],
                order: [[ 2, 'desc' ]],
                dom: '<"table-header" Br>t<"table-footer"lpi><"actions">',
            });
            table = $('.datatable-activity-instances:not(.ajaxTable)').removeAttr('width').DataTable({
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('scheduler.activity-instances.get-activity-instances') }}",
                    "data": function ( d ) {
                        d.show = "{{$show}}";
                    }
                },
                columns:[
                    {data:'any'},
                    {data:'anyTwo'},
                    {data:'activity_instances-date'} ,
                    {data:'suppliers-wms_name'},
                    {data:'activity_instances-status'},
                    {data:'activity_groups-name'},
                    {data:'activities-name'},
                    {data:'activities-question_name'},
                    {data:'activity_instances-answer'},
                    {data:'activity_actions-name'},
                    {data:'suppliers-wms_id'},
                    {data:'suppliers-email'},
                    {data:'suppliers-phone'},
                    {data:'appointments-id'},
                    {data:'appointment_actions-name'},
                    {data:'users-name'}
                ],
                createdRow: function( row, data, dataIndex ) {
                    $( row ).find('td:eq(13)').attr('data-export-data', data["appointment_id"]);
                    $( row ).find('td:eq(3)').attr('data-export-data', data["supplier_name"]);
                    $( row ).find('td:eq(4)').attr('data-export-data', data["activity_instance_status"]);
                    $( row ).attr('id', 'ai-' + data['activity_instance_id']);
                    $( row ).attr('data-actions',data['activity_actions']);
                    $( row ).attr('data-action', data['activity_instance_action']);
                    $( row ).attr('data-answer', data['activity_instance_answer']);
                    $( row ).attr('data-status', data['activity_instance_status']);
                    $( row ).attr('data-edit-title', data['edit_title']);
                    $( row ).attr('data-edit-data', data['edit_data']);
                    $( row ).attr('data-question-name', data['activity_question_name']);
                    $( row ).attr('data-update-url', data['update_url']);
                    $( row ).addClass(data['is_supplier_status']);
                    $( row ).addClass(data['is_intervened']);
                },

                "lengthMenu": [[10, 25, 50, 100, 500], [10, 25, 50, 100, 500]],
            });



            $('.datatable-activity-instances thead tr:eq(0)').addClass('dt-search-row');

            $('.datatable-activity-instances thead tr:eq(0) th').each( function (i) {
                let title = $(this).text().trim(); //es el nombre de la columna
                $(this).html( '<input style="display:none" type="text" placeholder="{{trans('scheduler.appointments.fields.search')}} '+title+'" />' );


                $( 'input', this ).on( 'keyup change', function () {
                    table.column($(this).data());
                    if ( table.column(i).search() !== this.value ) {
                        table
                            .column(i)
                            .search( this.value )
                            .draw();

                    }
                } );
            } );
        })

    </script>
@endsection

