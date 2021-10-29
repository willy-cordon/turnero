@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.locks.title') }}</title>
    <link href="{{ asset('css/datatables.css') }}" rel="stylesheet" />
@endsection
@section('content')

    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12 location-buttons-container">

            @foreach($locations as $location)
                <a class="btn btn-success" href="{{ route("scheduler.locks.create",$location->id) }}">
                    <i class="fas fa-plus"></i> {{ trans('global.add') }} {{ trans('scheduler.locks.title_singular') }} {{$location->name}}
                </a>
            @endforeach
        </div>

    </div>

    <div class="card">
        <div class="card-header">
            {{ trans('scheduler.locks.title') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover datatable datatable-locks">
                    <thead>
                    <tr>
                        <th style="width: 20px">

                        </th>

                        <th>
                            {{ trans('scheduler.locks.fields.lock_date') }}
                        </th>
                        <th>
                            {{ trans('scheduler.locks.fields.available_appointments') }}
                        </th>
                        <th>
                            {{ trans('scheduler.locks.fields.location') }}
                        </th>
                        <th style="width: 120px">
                            {{ trans('global.action') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($schedulerLocks as $key => $schedulerLock)
                        <tr data-entry-id="{{ $schedulerLock->id }}" >
                            <td>

                            </td>

                            <td data-sort="{{ $schedulerLock->getLockDateOrder()}}">
                                {{ $schedulerLock->lock_date ?? '' }}
                            </td>
                            <td>
                                {{ $schedulerLock->available_appointments ?? '' }}
                            </td>
                            <td>
                                {{ $schedulerLock->location->name ?? '' }}
                            </td>
                            <td>

                                <a class="btn btn-xs btn-success" title="{{ trans('global.edit') }}" href="{{ route('scheduler.locks.edit', $schedulerLock->id) }}">
                                    <i class="fas fa-pen"></i>
                                </a>
                                @include('partials.delete_button', ['model'=> $schedulerLock, 'destroy_method' => 'scheduler.locks.destroy'])

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
        $('.datatable-locks:not(.ajaxTable)').DataTable({ buttons: dtButtons });
    })

</script>
@endsection