@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('activity-groups') }}</title>
    <link href="{{ asset('css/datatables.css') }}" rel="stylesheet" />
@endsection

@section('content')

<div style="margin-bottom: 10px;" class="row">
    <div class="col-lg-12  location-buttons-container">

        @foreach($locations as $location)
            <a class="btn btn-success" href="{{ route("scheduler.activity-groups.create",$location->id) }}">
                <i class="fas fa-plus"></i> {{ trans('global.add') }} {{ trans('scheduler.activity_groups.title_singular') }} {{$location->name}}
            </a>
        @endforeach

    </div>

</div>


<div class="card">
    <div class="card-header">
        {{ trans('scheduler.activity_groups.title') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover datatable datatable-activity-groups ">
                <thead>
                <tr>
                    <th style="width: 20px">

                    </th>

                    <th>
                        {{ trans('scheduler.activity_groups.fields.name') }}
                    </th>
                    <th>
                        {{ trans('scheduler.activity_groups.fields.description') }}
                    </th>
                    <th>
                        {{ trans('scheduler.activity_groups.fields.type_min') }}
                    </th>
                    <th>
                        {{ trans('scheduler.activity_groups.fields.group_type') }}
                    </th>
                    <th>
                        {{ trans('scheduler.activity_groups.fields.location') }}
                    </th>

                    <th style="width: 120px">
                        {{ trans('global.action') }}
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($activityGroups as $activityGroup)
{{--                    @php(dd($activityGroups))--}}
                    <tr data-entry-id="{{ $activityGroup->id }}" >
                        <td>

                        </td>

                        <td>
                            {{ $activityGroup->name ?? '' }}
                        </td>
                        <td>
                            {{ $activityGroup->description ?? '' }}
                        </td>
                        <td>
                            {{ $activityGroup->type ?? '' }}
                        </td>
                        <td>

                            {{ $activityGroup->activityGroupType->name ?? '' }}

                        </td>
                        <td>
                            {{ $activityGroup->location->name ?? '' }}
                        </td>
                        <td>
                            @if($activityGroup->trashed())
                                @include('partials.restore_button', ['model'=> $activityGroup, 'restore_method' => 'scheduler.activity-groups.restore', 'restore_label'=> trans('global.restore')])
                            @else

                            <a class="btn btn-xs btn-success" title="{{ trans('global.edit') }}" href="{{ route('scheduler.activity-groups.edit', $activityGroup->id) }}">
                                <i class="fas fa-pen"></i>
                            </a>
                                @include('partials.delete_button', ['model'=> $activityGroup, 'destroy_method' => 'scheduler.activity-groups.destroy', 'destroy_label'=> trans('global.deactivate')])
                            @endif
{{--                            @include('partials.delete_button', ['model'=> $activityGroup, 'destroy_method' => 'scheduler.activity-groups.destroy'])--}}

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
            $('.datatable-activity-groups:not(.ajaxTable)').DataTable({ buttons: dtButtons });
        })

    </script>
@endsection
