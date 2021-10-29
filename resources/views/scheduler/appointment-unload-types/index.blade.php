@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.unload_types.title') }}</title>
    <link href="{{ asset('css/datatables.css') }}" rel="stylesheet" />
@endsection
@section('content')

    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("scheduler.appointment-unload-types.create") }}">
                <i class="fas fa-plus"></i> {{ trans('global.add') }} {{ trans('scheduler.unload_types.title_singular') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            {{ trans('scheduler.unload_types.title') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover datatable datatable-appointment-unload-type">
                    <thead>
                    <tr>
                        <th style="width: 20px">

                        </th>

                        <th>
                            {{ trans('scheduler.unload_types.fields.name') }}
                        </th>
                        <th>
                            {{ trans('scheduler.unload_types.fields.description') }}
                        </th>
                        <th style="width: 120px">
                            {{ trans('global.action') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($appointmentUnloadTypes as $key => $appointmentUnloadType)
                        <tr data-entry-id="{{ $appointmentUnloadType->id }}" class="@if($appointmentUnloadType->trashed()) {{"inactive-entity"}} @endif">
                            <td>

                            </td>

                            <td>
                                {{ $appointmentUnloadType->name ?? '' }}
                            </td>
                            <td>
                                {{ $appointmentUnloadType->description ?? '' }}
                            </td>

                            <td>
                                @if($appointmentUnloadType->trashed())
                                    @include('partials.restore_button', ['model'=> $appointmentUnloadType, 'restore_method' => 'scheduler.appointment-unload-types.restore', 'restore_label'=> trans('global.restore')])
                                @else
                                <a class="btn btn-xs btn-success" title="{{ trans('global.edit') }}" href="{{ route('scheduler.appointment-unload-types.edit', $appointmentUnloadType->id) }}">
                                   <i class="fas fa-pen"></i>
                                </a>
                                @include('partials.delete_button', ['model'=> $appointmentUnloadType, 'destroy_method' => 'scheduler.appointment-unload-types.destroy', 'destroy_label'=> trans('global.deactivate')])
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
        $('.datatable-appointment-unload-type:not(.ajaxTable)').DataTable({ buttons: dtButtons });
    })

</script>
@endsection