@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.appointments.title') }}</title>
    <link href="{{ asset('css/datatables.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12 location-buttons-container">
            @foreach($locations as $location)
                <a class="btn btn-success" href="{{ route("scheduler.appointments.create",$location->id) }}">
                    <i class="fas fa-plus"></i> {{ trans('global.add') }} {{ trans('scheduler.appointments.title_singular') }} {{$location->name}}
                </a>
            @endforeach
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            {{ trans('scheduler.appointments.title') }}
        </div>

        <div class="card-body">
            <div>

                <table class="table table-bordered table-striped table-hover datatable datatable-appointment display nowrap">
                    <thead>
                    <tr>
                        <th class="not-export-col" >

                        </th>
                        <th class="not-export-col">
                            {{ trans('global.action') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointments.fields.nro') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointments.fields.date_hour') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointments.fields.dock') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointments.fields.supplier_name') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointments.fields.supplier_dni') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointments.fields.supplier_comorbidity') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointments.fields.scheme') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointments.fields.type') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointments.fields.action') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointments.fields.supplier_phone') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointments.fields.supplier_aux2') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointments.fields.supplier_email') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointments.fields.supplier_address') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointments.fields.supplier_validate_address') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointments.fields.transportation') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointments.fields.need_assistance_min') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointments.fields.next_step') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointments.fields.comments') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointments.fields.created_by') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointments.fields.created_at') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointments.fields.original_created_by') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointments.fields.updated_at') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointments.fields.updated_by') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.suppliers.fields.owner') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointments.fields.supplier_date') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointments.fields.supplier_age') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.appointments.fields.supplier_gender') }}
                        </th>

                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>



    <form id="delete_form" action="" method="POST"  style="display: inline-block;">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <div class="modal fade delete-confirm-submit" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">{{ trans('global.delete') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{ trans('global.areYouSure') }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('global.no') }}</button>
                        <button type="button" class="btn btn-primary" onclick="$(this).closest('form').submit();">{{ trans('global.yes') }}</button>
                    </div>
                </div>
            </div>
        </div>

    </form>

@endsection

@section('scripts')
    @parent
    <!--Lo necesario para que ande datatables-->
    <script src="{{ asset('js/datatables.js') }}"></script>
    @include('partials.datatables_globals')

    <!-- -->
    <script>
        var table;
        $(document).ready(function() {
            // $.fn.dataTable.ext.errMode = 'throw';
            $('.datatable-appointment thead tr').clone(true).appendTo( '.datatable-appointment thead' );

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
                        targets: [0,1,25]
                    },
                    {
                        searchable: false,
                        targets: '_all'
                    }
                ],
                order: [[ 2, 'desc' ]],
                dom: '<"table-header" Br>t<"table-footer"lpi><"actions">',
            });
            table = $('.datatable-appointment:not(.ajaxTable)').removeAttr('width').DataTable({
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                ajax:"{{ route('scheduler.appointments.get-appointments') }}",

                columns:[
                    {data:'any'} ,
                    {data:'action'} ,
                    {data:'appointments-id'},
                    {data:'appointments-start_date'},
                    {data:'docks-name'},
                    {data:'suppliers-wms_name'},
                    {data:'suppliers-wms_id'},
                    {data:'suppliers-comorbidity'},
                    {data:'schemes-name'},
                    {data:'locations-name'},
                    {data:'appointment_actions-name'},
                    {data:'suppliers-phone'},
                    {data:'suppliers-aux2'},
                    {data:'suppliers-email'},
                    {data:'suppliers-address'},
                    {data:'suppliers-validate_address'},
                    {data:'appointments-transportation'},
                    {data:'appointments-need_assistance'},
                    {data:'appointments-next_step'},
                    {data:'appointments-comments'},
                    {data:'created_by-name'},
                    {data:'appointments-created_at'},
                    {data:'original_created_by-name'},
                    {data:'appointments-updated_at'},
                    {data:'updated_by-name'},
                    {data:'recruiter-name'},
                    {data:'suppliers-wms_date'},
                    {data:'suppliers-wms_age'},
                    {data:'suppliers-wms_gender'},
                ],
                createdRow: function( row, data, dataIndex ) {
                    $( row ).find('td:eq(5)').attr('data-export-data', data["supplier_name"]);
                    $( row ).find('td:eq(6)').attr('data-export-data', data["supplier_dni"]);
                    $( row ).addClass('status-'+data['action_id']);
                    $( row ).addClass(data['is_intervened']);
                    $( row ).addClass(data['is_supplier_status']);


                },

                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos - Para exportar"]],


            });
            $('.datatable-appointment thead tr:eq(0)').addClass('dt-search-row');
            $('.datatable-appointment thead tr:eq(0) th').each( function (i) {
                let title = $(this).text().trim(); //es el nombre de la columna
                $(this).html( '<input style="display:none" type="text" placeholder="{{trans('scheduler.appointments.fields.search')}} '+title+'" />' );


                $( 'input', this ).on( 'keyup change', function () {
                    table.column($(this).data());

                    if ( table.column(i).search() !== this.value ) {
                        //console.log(this.value);
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