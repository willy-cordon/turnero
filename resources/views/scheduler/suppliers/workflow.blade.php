@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.suppliers.title-workflow') }}</title>
    <link href="{{ asset('css/datatables.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('scheduler.suppliers.title-workflow') }}
        </div>

        <div class="card-body">
            <div>
                <div class="loading" style="display:none; text-align: center; font-weight: bold; width: 100%; height: calc(100%); background: rgba(255,255,255,0.4); position: absolute; left: 0; z-index: 1; top: 0px; padding-top: 14px;">
                    Procesando...
                </div>
                <table class="table table-bordered table-striped table-hover datatable datatable-loading datatable-supplier">
                    <thead>
                        <tr class="dt-search-row">
                            <th>
                            </th>
                            <th class="search-column">
                                {{ trans('scheduler.suppliers.fields.wms_id') }}
                            </th>
                            <th class="search-column">
                                {{ trans('scheduler.suppliers.fields.wms_name') }}
                            </th>
                            <th>
                            </th>
                            <th>
                            </th>
                            <th class="search-column">
                                {{ trans('scheduler.suppliers.fields.current_visit_type') }}
                            </th>
                            <th class="search-column">
                                {{ trans('scheduler.suppliers.fields.current_status') }}
                            </th>
                            <th class="search-column">
                                {{ trans('scheduler.suppliers.fields.current_date') }}
                            </th>
                            <th class="search-column">
                                {{ trans('scheduler.suppliers.fields.next_visit_type') }}
                            </th>
                            <th>

                            </th>
                            <th class="search-column">
                                {{ trans('scheduler.suppliers.fields.tracing') }}
                            </th>
                            <th class="search-column">
                                {{ trans('scheduler.suppliers.fields.recruiter') }}
                            </th>
                            <th class="search-column">
                                {{ trans('scheduler.suppliers.fields.supplier_group') }}
                            </th>
                        </tr>

                        <tr>
                            <th class="not-export-col">

                            </th>
                            <th>
                                {{ trans('scheduler.suppliers.fields.wms_id') }}
                            </th>
                            <th>
                                {{ trans('scheduler.suppliers.fields.wms_name') }}
                            </th>
                            <th >
                                {{ trans('scheduler.suppliers.fields.status_intervened') }}
                            </th>
                            <th>
                                {{ trans('scheduler.suppliers.fields.status_supplier') }}
                            </th>
                            <th>
                                {{ trans('scheduler.suppliers.fields.current_visit_type') }}
                            </th>
                            <th>
                                {{ trans('scheduler.suppliers.fields.current_status') }}
                            </th>
                            <th>
                                {{ trans('scheduler.suppliers.fields.current_date') }}
                            </th>
                            <th>
                                {{ trans('scheduler.suppliers.fields.next_visit_type') }}
                            </th>
                            <th>
                                {{ trans('scheduler.suppliers.fields.next_visit_range') }}
                            </th>
                            <th>
                                {{ trans('scheduler.suppliers.fields.tracing') }}
                            </th>
                            <th >
                                {{ trans('scheduler.suppliers.fields.recruiter') }}
                            </th>
                            <th >
                                {{ trans('scheduler.suppliers.fields.supplier_group') }}
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
        var table;
        $(document).ready(function(){
            //Creamos una fila en el head de la tabla y lo clonamos para cada columna
            // $('.datatable-supplier thead tr').clone(true).appendTo( '.datatable-supplier thead' );


            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);

            $.extend(true, $.fn.dataTable.defaults, {
                columnDefs: [
                    {
                        orderable: false,
                        className: 'select-checkbox',
                        targets: 0
                    },
                    {   orderable: false,
                        targets: '_all'
                    },
                    {
                        searchable: false,
                        targets: '_all'
                    }
                ],
                dom: '<"table-header" Br>t<"table-footer"lpi><"actions">',
            });
            table = $('.datatable-supplier:not(.ajaxTable)').removeAttr('width').DataTable({
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                ajax:"{{ route('scheduler.suppliers.get-workflow-suppliers') }}",

                columns:[
                    {data:'any'},
                    {data:'suppliers-wms_id'},
                    {data:'suppliers-wms_name'},
                    {data:'suppliers-is_intervened'},
                    {data:'suppliers-status'},
                    {data:'appointment_current_visit_link'},
                    {data:'appointment_status'},
                    {data:'appointment_date'},
                    {data:'appointment_next_step'},
                    {data:'appointment_next_step_range'},
                    {data:'status_validation'},
                    {data:'recruiter-name'},
                    {data:'supplier_groups-name'},

                ],
                createdRow: function( row, data, dataIndex ) {
                    $(row).find('td:eq(3)').attr('data-export-data', data["is_intervened_text"]);
                    $(row).find('td:eq(4)').attr('data-export-data', data["supplier_textstatus"]);
                    $(row).find('td:eq(2)').attr('data-export-data', data["supplier_textname"]);
                    $(row).find('td:eq(5)').attr('data-export-data', data["appointment_current_visit_text"]);
                    $(row).find('td:eq(8)').attr('data-export-data', data["appointment_next_step_text"]);
                    $(row).find('td:eq(10)').attr('data-export-data', data["status_validation_text"]);

                    $( row ).attr('id', 'sp-' + data['suppliers-id']);
                    $( row ).addClass('status-'+data['action_id']);
                    $( row ).addClass(data['is_intervened']);
                    $( row ).addClass(data['supplier_status_class']);
                    $( row ).attr('data-edit-data', data['edit_data']);
                },
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos - Para exportar"]],
            });

            $('.datatable-supplier thead tr.dt-search-row th').each( function (i) {
                if($(this).hasClass('search-column')) {
                    let title = $(this).text().trim(); //es el nombre de la columna

                    $(this).html('<input type="text" placeholder="{{trans('scheduler.appointments.fields.search')}} ' + title + '" />');

                    $('input', this).on('keyup change', function () {
                        table.column($(this).data());

                        if (table.column(i).search() !== this.value) {
                            // console.log(table.column());
                            table
                                .column(i)
                                .search(this.value)
                                .draw();

                        }
                    });
                }
            });
        });



    </script>

@endsection
