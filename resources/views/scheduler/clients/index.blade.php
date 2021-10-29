@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.clients.title') }}</title>
    <link href="{{ asset('css/datatables.css') }}" rel="stylesheet" />
@endsection
@section('content')

    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("scheduler.clients.create") }}">
                <i class="fas fa-plus"></i> {{ trans('global.add') }} {{ trans('scheduler.clients.title_singular') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            {{ trans('scheduler.clients.title') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover datatable datatable-client">
                    <thead>
                    <tr>
                        <th class="not-export-col" width="10">

                        </th>
                        <th>
                            {{ trans('scheduler.clients.fields.id') }}
                        </th>
                        <th>
                            {{ trans('scheduler.clients.fields.name') }}
                        </th>
                        <th>
                            {{ trans('scheduler.clients.fields.token') }}
                        </th>
                        <th class="not-export-col">
                            {{ trans('global.action') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($clients as $key => $client)
                        <tr data-entry-id="{{ $client->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $client->id ?? '' }}
                            </td>
                            <td>
                                {{ $client->name ?? '' }}
                            </td>
                            <td>
                                {{ $client->api_token ?? '' }}
                            </td>

                            <td>
                                <a class="btn btn-xs btn-success" title="{{ trans('global.edit') }}" href="{{ route('scheduler.clients.edit', $client->id) }}">
                                    <i class="fas fa-pen"></i>
                                </a>
                                @include('partials.delete_button', ['model'=> $client, 'destroy_method' => 'scheduler.clients.destroy'])
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
        $('.datatable-client:not(.ajaxTable)').DataTable({ buttons: dtButtons });
    })

</script>
@endsection