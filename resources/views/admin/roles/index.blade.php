@extends('layouts.admin')

@section('styles')
    <title>{{ config('app.name') }} | {{ trans('cruds.role.title') }}</title>
    <link href="{{ asset('css/datatables.css') }}" rel="stylesheet" />
@endsection

@section('content')
<div style="margin-bottom: 10px;" class="row">
    <div class="col-lg-12">
        <a class="btn btn-success" href="{{ route("admin.roles.create") }}">
            {{ trans('global.add') }} {{ trans('cruds.role.title_singular') }}
        </a>
    </div>
</div>
<div class="card">
    <div class="card-header">
        {{ trans('cruds.role.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div>
            <table class=" table table-bordered table-striped table-hover datatable datatable-Role">
                <thead>
                    <tr>
                        <th class="not-export-col" width="10">

                        </th>
                        <th>
                            {{ trans('cruds.role.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.role.fields.title') }}
                        </th>
                        <th>
                            {{ trans('cruds.role.fields.permissions') }}
                        </th>
                        <th class="not-export-col">
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $key => $role)
                        <tr data-entry-id="{{ $role->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $role->id ?? '' }}
                            </td>
                            <td>
                                {{ $role->name ?? '' }}
                            </td>
                            <td>
                                @foreach($role->permissions()->pluck('name') as $permission)
                                    <span class="badge badge-info">{{ $permission }}</span>
                                @endforeach
                            </td>
                            <td>
                                <a class="btn btn-xs btn-primary" href="{{ route('admin.roles.show', $role->id) }}">
                                    {{ trans('global.view') }}
                                </a>

                                <a class="btn btn-xs btn-info" href="{{ route('admin.roles.edit', $role->id) }}">
                                    {{ trans('global.edit') }}
                                </a>

                                @include('partials.delete_button', ['model'=> $role, 'destroy_method' => 'admin.roles.destroy'])

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
      let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
      let deleteButton = {
        text: deleteButtonTrans,
        url: "{{ route('admin.roles.mass_destroy') }}",
        className: 'btn-danger',
        action: function (e, dt, node, config) {
          var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
              return $(entry).data('entry-id')
          });

          if (ids.length === 0) {
            alert('{{ trans('global.datatables.zero_selected') }}')

            return
          }

          if (confirm('{{ trans('global.areYouSure') }}')) {
            $.ajax({
              headers: {'x-csrf-token': _token},
              method: 'POST',
              url: config.url,
              data: { ids: ids, _method: 'DELETE' }})
              .done(function () { location.reload() })
          }
        }
      }
      dtButtons.push(deleteButton)

      $.extend(true, $.fn.dataTable.defaults, {
        order: [[ 1, 'desc' ]],
        pageLength: 10,
      });
      $('.datatable-Role:not(.ajaxTable)').DataTable({ buttons: dtButtons })
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust();
        });
    })
</script>
@endsection