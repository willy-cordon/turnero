@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.supplier_groups.title') }}</title>
    <link href="{{ asset('css/datatables.css') }}" rel="stylesheet" />
@endsection
@section('content')

    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("scheduler.supplier-groups.create") }}">
                <i class="fas fa-plus"></i> {{ trans('global.add') }} {{ trans('scheduler.supplier_groups.title_singular') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            {{ trans('scheduler.supplier_groups.title') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover datatable datatable-supplier-group">
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
                    @foreach($supplierGroups as $supplierGroup)
                        <tr data-entry-id="{{ $supplierGroup->id }}" class="@if($supplierGroup->trashed()) {{"inactive-entity"}} @endif">
                            <td>

                            </td>

                            <td>
                                {{ $supplierGroup->name ?? '' }}
                            </td>
                            <td>
                                {{ $supplierGroup->description ?? '' }}
                            </td>

                            <td>
                                @if($supplierGroup->trashed())
                                    @include('partials.restore_button', ['model'=> $supplierGroup, 'restore_method' => 'scheduler.supplier-groups.restore', 'restore_label'=> trans('global.restore')])
                                @else
                                    <a class="btn btn-xs btn-success" title="{{ trans('global.edit') }}" href="{{ route('scheduler.supplier-groups.edit', $supplierGroup->id) }}">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    @include('partials.delete_button', ['model'=> $supplierGroup, 'destroy_method' => 'scheduler.supplier-groups.destroy', 'destroy_label'=> trans('global.deactivate')])
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
            $('.datatable-supplier-group:not(.ajaxTable)').DataTable({ buttons: dtButtons });
        })

    </script>
@endsection