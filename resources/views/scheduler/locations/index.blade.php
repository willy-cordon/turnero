@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.locations.title') }}</title>
    <link href="{{ asset('css/datatables.css') }}" rel="stylesheet" />
@endsection
@section('content')

    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("scheduler.locations.create") }}">
                <i class="fas fa-plus"></i> {{ trans('global.add') }} {{ trans('scheduler.locations.title_singular') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            {{ trans('scheduler.locations.title') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover datatable datatable-appointment-action">
                    <thead>
                    <tr>
                        <th style="width: 20px">

                        </th>

                        <th>
                            {{ trans('scheduler.locations.fields.name') }}
                        </th>
                        <th>
                            {{ trans('scheduler.settings.fields.init_hour') }}
                        </th>
                        <th>
                            {{ trans('scheduler.settings.fields.end_hour') }}
                        </th>
                        <th>
                            {{ trans('scheduler.settings.fields.appointment_init_minutes_size') }}
                        </th>
                        <th>
                            {{ trans('scheduler.locations.fields.sequence') }}
                        </th>
                        <th>
                            {{ trans('scheduler.locations.fields.scheme') }}
                        </th>


                        <th style="width: 120px">
                            {{ trans('global.action') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($locations as $key => $location)
                        <tr data-entry-id="{{ $location->id }}" class="@if($location->trashed()) {{"inactive-entity"}} @endif">
                            <td>

                            </td>

                            <td>
                                {{ $location->name ?? '' }}
                            </td>
                            <td>
                                {{ $location->init_hour ?? '' }}
                            </td>
                            <td>
                                {{ $location->end_hour ?? '' }}
                            </td>
                            <td>
                                {{ $location->appointment_init_minutes_size ?? '' }}
                            </td>
                            <td>
                                {{ $location->sequence->name ?? '' }}
                            </td>

                            <td>
                                @foreach($location->schemes()->pluck('name') as $schemes)
                                    <span class="badge badge-info p-2">{{ $schemes }}</span>
                                @endforeach
                            </td>

                            <td>
                                @if($location->trashed())
                                    @include('partials.restore_button', ['model'=> $location, 'restore_method' => 'scheduler.locations.restore', 'restore_label'=> trans('global.restore')])
                                @else
                                <a class="btn btn-xs btn-success" title="{{ trans('global.edit') }}" href="{{ route('scheduler.locations.edit', $location->id) }}">
                                    <i class="fas fa-pen"></i>
                                </a>
                                @include('partials.delete_button', ['model'=> $location, 'destroy_method' => 'scheduler.locations.destroy', 'destroy_label'=> trans('global.deactivate')])
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