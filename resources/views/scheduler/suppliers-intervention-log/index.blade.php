@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.supplier_intervention_log.title_singular') }}</title>
    <link href="{{ asset('css/datatables.css') }}" rel="stylesheet" />
@endsection
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('scheduler.supplier_intervention_log.title_or') }}
        </div>

        <div class="card-body">
            <div>

                <table class="table table-bordered table-striped table-hover datatable datatable-loading datatable-supplier-intervention">
                    <thead>
                    <tr>
                        <th class="not-export-col">

                        </th>

                        <th class="search-column">
                            {{ trans('scheduler.supplier_intervention_log.fields.supplierInterventionId') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.supplier_intervention_log.fields.supplier_name') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.supplier_intervention_log.fields.supplier_dni') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.supplier_intervention_log.fields.reasons_intervention') }}
                        </th>
                        {{-- <th class="search-column">
                             {{ trans('scheduler.supplier_intervention_log.fields.description') }}
                         </th>--}}
                         <th class="search-column">
                             {{ trans('scheduler.supplier_intervention_log.fields.created_by') }}
                         </th>
 {{--                        <th class="search-column">--}}
{{--                            {{ trans('scheduler.supplier_intervention_log.fields.updated_by') }}--}}
{{--                        </th>--}}
                        <th class="search-column">
                            {{ trans('scheduler.supplier_intervention_log.fields.date_created') }}
                        </th>
{{--                        <th class="search-column">--}}
{{--                            {{ trans('scheduler.supplier_intervention_log.fields.date_updated') }}--}}
{{--                        </th>--}}


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
            let table = $('.datatable-supplier-intervention:not(.ajaxTable)').removeAttr('width').DataTable({
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                ajax:"{{ route('scheduler.supplier-intervention-logs.save.get-interventions') }}",

                columns:[
                    {data:'any'} ,
                    {data:'supplier_intervention_logs-id'} ,
                    {data:'suppliers-wms_name'},
                    {data:'suppliers-wms_id'},
                    {data:'supplier_intervention_logs-intervention_reason'},
                    //{data:'supplier_intervention_logs-description'},
                    {data:'supplier_intervention_logs-created_by'},
                    // {data:'updated_by-name'},
                    {data:'supplier_intervention_logs-created_at'},
                    // {data:'supplier_intervention_logs-updated_at'},


                ],

                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos - Para exportar"]],
            });


        })

    </script>
@endsection
