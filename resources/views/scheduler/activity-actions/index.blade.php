@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.activity_actions.title') }}</title>
    <link href="{{ asset('css/datatables.css') }}" rel="stylesheet" />
@endsection

@section('content')



    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">


                <a class="btn btn-success" href="{{ route("scheduler.activity-actions.create") }}">
                    <i class="fas fa-plus"></i> {{ trans('global.add') }} {{ trans('scheduler.activity_actions.title_singular') }}
                </a>


        </div>

    </div>


    <div class="card">
        <div class="card-header">
            {{ trans('scheduler.activity_actions.title') }}
        </div>

        <div class="card-body">
            <div>
                <table class="table table-bordered table-striped table-hover datatable datatable-activity-groups ">
                    <thead>
                    <tr>
                        <th>

                        </th>

                        <th>
                            {{ trans('scheduler.activity_groups.fields.name') }}
                        </th>
                        <th>
                            {{ trans('scheduler.activity_groups.fields.description') }}
                        </th>


                        <th style="width: 120px">
                            {{ trans('global.action') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($activityActions as $activityAction)
                        {{--                    @php(dd($activityGroups))--}}
                        <tr data-entry-id="{{ $activityAction->id }}" >
                            <td>

                            </td>

                            <td>
                                {{ $activityAction->name ?? '' }}
                            </td>
                            <td>
                                {{ $activityAction->description ?? '' }}
                            </td>

                            <td>
                                @if($activityAction->trashed())
                                    @include('partials.restore_button', ['model'=> $activityAction, 'restore_method' => 'scheduler.activity-actions.restore', 'restore_label'=> trans('global.restore')])
                                @else

                                    <a class="btn btn-xs btn-success" title="{{ trans('global.edit') }}" href="{{ route('scheduler.activity-actions.edit', $activityAction->id) }}">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    @include('partials.delete_button', ['model'=> $activityAction, 'destroy_method' => 'scheduler.activity-actions.destroy', 'destroy_label'=> trans('global.deactivate')])
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
