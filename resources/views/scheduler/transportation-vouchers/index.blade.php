@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.transportation_vouchers.title') }}</title>
    <link href="{{ asset('css/datatables.css') }}" rel="stylesheet" />
@endsection
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('scheduler.transportation_vouchers.title') }}
        </div>

        <div class="card-body">
            <div>

                <table class="table table-bordered table-striped table-hover datatable datatable-loading datatable-voucher">
                    <thead>
                    <tr>
                        <th class="not-export-col">

                        </th>

                        <th class="search-column">
                            {{ trans('scheduler.transportation_vouchers.fields.id') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.transportation_vouchers.fields.date') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.transportation_vouchers.fields.supplier') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.transportation_vouchers.fields.dni') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.transportation_vouchers.fields.address') }}
                        </th>

                        <th class="not-export-col">
                            {{ trans('global.action') }}
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
        $(document).ready(function(){

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

            });
            let table = $('.datatable-voucher:not(.ajaxTable)').removeAttr('width').DataTable({
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                ajax:"{{ route('scheduler.transportation-vouchers.get-vouchers') }}",

                columns:[
                    {data:'any'} ,
                    {data:'transportation_vouchers-id'} ,
                    {data:'transportation_vouchers-created_at'},
                    {data:'suppliers-wms_name'},
                    {data:'suppliers-wms_id'},
                    {data:'suppliers-address'},
                    {data:'button-action'},

                ],

                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos - Para exportar"]],
            });


        })

    </script>
@endsection
