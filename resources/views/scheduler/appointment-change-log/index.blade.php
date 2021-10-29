@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.appointment_change_log.title') }}</title>
    <link href="{{ asset('css/datatables.css') }}" rel="stylesheet" />


@endsection
@section('content')

    <div class="container">
        <div class="row">
            <div class='col-md-4'>
                <div class="form-group">
                    <div class='input-group date' id='datetimepicker6'>
                        <input style="background: #FFF;" type='text' class="form-control" id="date_timepicker_start" readonly/>
                        <span class="input-group-addon date-picker-button">
                            <span class="glyphicon glyphicon-calendar">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                        </span>
                    </div>


                </div>

            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <div class='input-group date' id='datetimepicker7'>
                        <input style="background: #FFF;" type="text" id="date_timepicker_end"  name="departureDate"  class="form-control"  readonly/>
                        <span class="input-group-addon date-picker-button">
                            <span class="glyphicon glyphicon-calendar">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                        </span>

                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <button id="search-button" class="btn btn-primary" onclick="search()" disabled>Buscar</button>

            </div>
        </div>


    </div>

    <div class="card">
        <div class="card-header">
            {{ trans('scheduler.appointment_change_log.title') }}
        </div>

        <div class="card-body">
            <div>

                <table class="table table-bordered table-striped table-hover datatable datatable-loading datatable-appointment-change-log">
                    <thead>
                    <tr>
                        <th class="not-export-col">

                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointment_change_log.fields.update_date') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointment_change_log.fields.change_log_type') }}
                        </th>

                        <th class="search-column">
                            {{ trans('scheduler.appointment_change_log.fields.appointment_id') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointment_change_log.fields.appointment_date') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointment_change_log.fields.appointment_status') }}
                        </th>

                        <th class="search-column">
                            {{ trans('scheduler.appointment_change_log.fields.supplier_name') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointment_change_log.fields.supplier_dni') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointment_change_log.fields.supplier_phone') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointment_change_log.fields.supplier_address') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointment_change_log.fields.supplier_validate_address') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointment_change_log.fields.user_update') }}
                        </th>


                    </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot></tfoot>
                </table>
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
        var oOptions = {
            icons: {
                time: "fas fa-clock",
                date: "fa fa-calendar"
            },
            format: "DD/MM/YYYY HH:mm",
            locale: 'es',
            ignoreReadonly: true,
            useCurrent: false
        };

        function toggleButton(){
            if($("#date_timepicker_start").val() === '' || $("#date_timepicker_end").val() === ''){
                $('#search-button').attr('disabled', true);
            }else{
                $('#search-button').attr('disabled', false);
            }
        }

        jQuery(function(){
            $('#datetimepicker6').datetimepicker(oOptions);
            $('#datetimepicker7').datetimepicker(oOptions);

            $("#datetimepicker6").on("dp.change", function (e) {
                $('#datetimepicker7').data("DateTimePicker").minDate(e.date);
                toggleButton();
            });
            $("#datetimepicker7").on("dp.change", function (e) {
                $('#datetimepicker6').data("DateTimePicker").maxDate(e.date);
                toggleButton();
            });


        });

        function search(){
            table.draw();
        }


        $(document).ready(function(){

            //Creamos una fila en el head de la tabla y lo clonamos para cada columna
            $('.datatable-appointment-change-log thead tr').clone(true).appendTo( '.datatable-appointment-change-log thead' );


            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);

            $.extend(true, $.fn.dataTable.defaults, {
                order: [[ 1, 'desc' ]],
                columnDefs: [
                    {
                        orderable: false,
                        className: 'select-checkbox',
                        targets: 0
                    },
                    {
                        orderable: false,
                        targets: [0]
                    },
                    {
                        searchable: false,
                        targets: '_all'
                    }
                ],
                dom: '<"table-header" Br>t<"table-footer"lpi><"actions">',
            });
            table = $('.datatable-appointment-change-log:not(.ajaxTable)').removeAttr('width').DataTable({
                // buttons: dtButtons,
                processing: true,
                serverSide: true,
                ajax:{
                    "url": "{{ route('scheduler.appointment-change-logs.getAppointmentChangeLog') }}",
                    "data": function(d) {
                        d.dataInit = $("#date_timepicker_start").val();
                        d.dataEnd =$("#date_timepicker_end").val();
                    }
                },

                columns:[
                    {data:'any'},
                    {data:'appointment_change_logs-updated_at'},
                    {data:'appointment_change_logs-field_name'},
                    {data:'appointments-id'} ,
                    {data:'appointments-start_date'},
                    {data:'appointment_actions-name'},
                    {data:'suppliers-wms_name'},
                    {data:'suppliers-wms_id'},
                    {data:'suppliers-phone'},
                    {data:'suppliers-address'},
                    {data:'suppliers-validate_address'},
                    {data:'updated_by-name'}

                ],


                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos - Para exportar"]],
            });

            $('.datatable-appointment-change-log thead tr:eq(0)').addClass('dt-search-row');
            $('.datatable-appointment-change-log thead tr:eq(0) th').each( function (i) {
                let title = $(this).text().trim(); //es el nombre de la columna
                $(this).html( '<input style="display:none" type="text" placeholder="{{trans('scheduler.appointments.fields.search')}} '+title+'" />' );


                $( 'input', this ).on( 'keyup change', function () {
                    table.column($(this).data());

                    if ( table.column(i).search() !== this.value ) {
                      //  console.log(this.value);
                        table
                            .column(i)
                            .search( this.value )
                            .draw();

                    }
                } );
            } );


        });





    </script>
@endsection
