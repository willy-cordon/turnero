@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.docks.title') }}</title>
    <link href="{{ asset('css/datatables.css') }}" rel="stylesheet" />
@endsection
@section('content')

    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("scheduler.docks.create") }}">
                <i class="fas fa-plus"></i> {{ trans('global.add') }} {{ trans('scheduler.docks.title_singular') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            {{ trans('scheduler.docks.title') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover datatable datatable-appointment-action">
                    <thead>
                    <tr>
                        <th style="width: 20px">

                        </th>

                        <th>
                            {{ trans('scheduler.docks.fields.name') }}
                        </th>
                        <th>
                            {{ trans('scheduler.docks.fields.description') }}
                        </th>
                        <th>
                            {{ trans('scheduler.docks.fields.location') }}
                        </th>
                        <th style="width: 120px">
                            {{ trans('global.action') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($docks as $key => $dock)
                        <tr data-entry-id="{{ $dock->id }}" class="@if($dock->trashed()) {{"inactive-entity"}} @endif">
                            <td>

                            </td>

                            <td>
                                {{ $dock->name ?? '' }}
                            </td>
                            <td>
                                {{ $dock->description ?? '' }}
                            </td>
                            <td>
                                {{ $dock->location->name ?? '' }}
                            </td>
                            <td>
                                @if($dock->trashed())
                                    @include('partials.restore_button', ['model'=> $dock, 'restore_method' => 'scheduler.docks.restore', 'restore_label'=> trans('global.restore')])
                                @else
                                <a class="btn btn-xs btn-success" title="{{ trans('global.edit') }}" href="{{ route('scheduler.docks.edit', $dock->id) }}">
                                    <i class="fas fa-pen"></i>
                                </a>
                                @include('partials.delete_button', ['model'=> $dock, 'destroy_method' => 'scheduler.docks.destroy', 'destroy_label'=> trans('global.deactivate')])
                                @endif
                            </td>

                        </tr>
                    @endforeach
                    </tbody>
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
    $(function () {
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);


        $.extend(true, $.fn.dataTable.defaults, {
            order: [[ 1, 'desc' ]]
        });
        $('.datatable-appointment-action:not(.ajaxTable)').DataTable({ buttons: dtButtons });
    })

</script>
@endsection