@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.sequence.title') }}</title>
    <link href="{{ asset('css/datatables.css') }}" rel="stylesheet" />
@endsection
@section('content')

    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("scheduler.sequence.create") }}">
                <i class="fas fa-plus"></i> {{ trans('global.add') }} {{ trans('scheduler.sequence.title_singular') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            {{ trans('scheduler.sequence.title') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover datatable datatable-sequence">
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
                        <th>
                            {{ trans('scheduler.sequence.fields.show_in_workflow') }}
                        </th>

                        <th style="width: 120px">
                            {{ trans('global.action') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($sequences as $key => $sequence)
                        <tr data-entry-id="{{ $sequence->id }}" class="@if($sequence->trashed()) {{"inactive-entity"}} @endif">
                            <td>

                            </td>

                            <td>
                                {{ $sequence->name ?? '' }}
                            </td>
                            <td>
                                {{ $sequence->description ?? '' }}
                            </td>

                            <td>
                                {{$sequence->show_in_workflow == 0 ? 'No' : 'Si'}}
{{--                                @if(!empty($sequence->show_in_workflow))--}}
{{--                                {{--}}
{{--                                    $sequence->show_in_workflow == 0 ? 'No' : 'Si'--}}
{{--                                }}--}}
{{--                                @endif--}}
                            </td>

                            <td>
                                @if($sequence->trashed())
                                    @include('partials.restore_button', ['model'=> $sequence, 'restore_method' => 'scheduler.sequence.restore', 'restore_label'=> trans('global.restore')])
                                @else
                                    <a class="btn btn-xs btn-success" title="{{ trans('global.edit') }}" href="{{ route('scheduler.sequence.edit', $sequence->id) }}">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    @include('partials.delete_button', ['model'=> $sequence, 'destroy_method' => 'scheduler.sequence.destroy', 'destroy_label'=> trans('global.deactivate')])
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
            $('.datatable-sequence:not(.ajaxTable)').DataTable({ buttons: dtButtons });
        })

    </script>
@endsection