@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.activities.title') }}</title>
    <link href="{{ asset('css/datatables.css') }}" rel="stylesheet" />
@endsection

@section('content')

    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">


                <a class="btn btn-success" href="{{ route("scheduler.activities.create") }}">
                    <i class="fas fa-plus"></i> {{ trans('global.add') }} {{ trans('scheduler.activities.title_singular') }}
                </a>


        </div>

    </div>


    <div class="card">
        <div class="card-header">
            {{ trans('scheduler.activities.title') }}
        </div>

        <div class="card-body">
            <div>
                <table class="table table-bordered table-striped table-hover datatable datatable-activity-groups ">
                    <thead>
                    <tr>
                        <th>

                        </th>

                        <th>
                            {{ trans('scheduler.activities.fields.name') }}
                        </th>
                        <th>
                            {{ trans('scheduler.activities.fields.description') }}
                        </th>
                        <th>
                            {{ trans('scheduler.activities.fields.question_name') }}
                        </th>
                        <th>
                            {{ trans('scheduler.activities.fields.days_from_appointment') }}
                        </th>
                        <th>
                            {{ trans('scheduler.activities.fields.group') }}
                        </th>
                        <th>
                            {{ trans('scheduler.activities.fields.group_type') }}
                        </th>
                        <th>
                            {{ trans('scheduler.activities.fields.actions') }}
                        </th>

                        <th style="width: 120px">
                            {{ trans('global.action') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($activities as $activity)

                        <tr data-entry-id="{{ $activity->id }}" >
                            <td>

                            </td>

                            <td>
                                {{ $activity->name ?? '' }}
                            </td>
                            <td>
                                {{ $activity->description ?? '' }}
                            </td>
                            <td>
                                {{ $activity->question_name ?? '' }}
                            </td>
                            <td>
                                {{ $activity->days_from_appointment ?? '' }}
                            </td>
                            <td>
                                {{ $activity->activityGroup->name ?? '' }}
                            </td>
                            <td>
                                @if ($activity->activityGroup->activity_group_type_id == 1) Tipo de grupo: E-Diary @endif
                                @if ($activity->activityGroup->activity_group_type_id == 2) Tipo de grupo: Turnos @endif
                                @if ($activity->activityGroup->activity_group_type_id == 3) Tipo de grupo: Vigilancia @endif
                            </td>
                            <td>
                                @foreach($activity->activityActions()->pluck('name') as $activities)
                                    <span class="badge badge-info p-2">{{ $activities }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if($activity->trashed())
                                    @include('partials.restore_button', ['model'=> $activity, 'restore_method' => 'scheduler.activity.restore', 'restore_label'=> trans('global.restore')])
                                @else

                                    <a class="btn btn-xs btn-success" title="{{ trans('global.edit') }}" href="{{ route('scheduler.activities.edit', $activity->id) }}">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    @include('partials.delete_button', ['model'=> $activity, 'destroy_method' => 'scheduler.activities.destroy', 'destroy_label'=> trans('global.deactivate')])
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
            $('.datatable-activity-groups:not(.ajaxTable)').DataTable({ buttons: dtButtons });
        })

    </script>
@endsection
