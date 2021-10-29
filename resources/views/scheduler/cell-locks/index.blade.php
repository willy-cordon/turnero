@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.cell-locks.title') }}</title>
    <link href="{{ asset('css/datatables.css') }}" rel="stylesheet" />
@endsection
@section('content')

    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12 location-buttons-container">

            @foreach($locations as $location)
                <a class="btn btn-success" href="{{ route("scheduler.cell-locks.create",$location->id) }}">
                    <i class="fas fa-plus"></i> {{ trans('global.add') }} {{ trans('scheduler.cell_locks.title_singular') }} {{$location->name}}
                </a>
            @endforeach
        </div>

    </div>

    <div class="card">
        <div class="card-header">
            {{ trans('scheduler.cell_locks.title') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover datatable datatable-cellLocks">
                    <thead>
                    <tr>
                        <th style="width: 20px">

                        </th>

                        <th>
                            {{ trans('scheduler.cell_locks.fields.lock_type') }}
                        </th>
                        <th>
                            {{ trans('scheduler.cell_locks.fields.dock_name') }}
                        </th>
                        <th>
                            {{ trans('scheduler.cell_locks.fields.lock_date') }}
                        </th>
                        <th>
                            {{ trans('scheduler.cell_locks.fields.hour') }}
                        </th>
                        <th>
                            {{ trans('scheduler.cell_locks.fields.location') }}

                        </th>
                        <th style="width: 120px">
                            {{ trans('global.action') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
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
        $(function () {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);


            $.extend(true, $.fn.dataTable.defaults, {
                order: [[ 3, 'desc' ]],
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
            // $('.datatable-cellLocks:not(.ajaxTable)').DataTable({ buttons: dtButtons });
            let table = $('.datatable-cellLocks:not(.ajaxTable)').removeAttr('width').DataTable({
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                ajax:"{{ route('scheduler.cell-locks.get-cell-locks') }}",

                columns:[
                    {data:'any'} ,
                    {data:'scheduler_cell_locks-lock_type'} ,
                    {data:'scheduler_cell_locks-dock_name'},
                    {data:'scheduler_cell_locks-lock_date'},
                    {data:'scheduler_cell_locks-hour'},
                    {data:'location'},
                    {data:'button-action'},

                ],

                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos - Para exportar"]],
            });
        })

    </script>
@endsection