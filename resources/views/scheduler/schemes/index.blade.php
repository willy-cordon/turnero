@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.schemes.title') }}</title>
    <link href="{{ asset('css/datatables.css') }}" rel="stylesheet" />
@endsection
@section('content')

    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("scheduler.schemes.create") }}">
                <i class="fas fa-plus"></i> {{ trans('global.add') }} {{ trans('scheduler.schemes.title_singular') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            {{ trans('scheduler.schemes.title') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover datatable datatable-schema">
                    <thead>
                    <tr>
                        <th style="width: 20px">

                        </th>

                        <th>
                            {{ trans('scheduler.actions.fields.name') }}
                        </th>
                        <th>
                            {{ trans('scheduler.actions.fields.description') }}
                        </th>
                        <th style="width: 120px">
                            {{ trans('global.action') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($schemes as $key => $scheme)
                        <tr data-entry-id="{{ $scheme->id }}" class="@if($scheme->trashed()) {{"inactive-entity"}} @endif">
                            <td>

                            </td>

                            <td>
                                {{ $scheme->name ?? '' }}
                            </td>
                            <td>
                                {{ $scheme->description ?? '' }}
                            </td>

                            <td>
                                @if($scheme->trashed())
                                    @include('partials.restore_button', ['model'=> $scheme, 'restore_method' => 'scheduler.schemes.restore', 'restore_label'=> trans('global.restore')])
                                @else
                                    <a class="btn btn-xs btn-success" title="{{ trans('global.edit') }}" href="{{ route('scheduler.schemes.edit', $scheme->id) }}">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    @include('partials.delete_button', ['model'=> $scheme, 'destroy_method' => 'scheduler.schemes.destroy', 'destroy_label'=> trans('global.deactivate')])
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
            $('.datatable-schema:not(.ajaxTable)').DataTable({ buttons: dtButtons });
        })

    </script>
@endsection