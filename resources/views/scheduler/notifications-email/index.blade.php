@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.notifications.title') }}</title>
    <link href="{{ asset('css/datatables.css') }}" rel="stylesheet" />
@endsection
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('scheduler.notifications.title') }} | Leidos: <span class="notRead">{{ $notificationsRead }}</span>  /  Totales: <span class="notAll">{{ $notificationsAll }}</span>
        </div>

        <div class="card-body">
            <div>

                <table class="table table-bordered table-striped table-hover datatable datatable-loading datatable-notifications">
                    <thead>
                    <tr>
                        <th class="not-export-col">

                        </th>

                        <th class="search-column">
                            {{ trans('scheduler.notifications.fields.status') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.notifications.fields.date') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.notifications.fields.email_subject') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.notifications.fields.supplier_name') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.notifications.fields.supplier_dni') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.notifications.fields.type') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.notifications.fields.notified_to') }}
                        </th>

                    </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot></tfoot>
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
            let table;
            //Cambiar el status a leido

        function getStatus(reg,reg2){


            $.ajax({
                url: "{{ route('scheduler.notifications-email.edit-status') }}",
                type:"POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                    id : reg,
                    status : reg2
                },
                success:function(response){

                    if(response.status === 'ok'){
                        let notAll = $(".notAll").text(response.notificationAll);
                        let notRead = $(".notRead").text(response.notificationRead);

                        table.draw();
                    }else {
                        console.log(response);
                    }

                },
                error :function( data ) {

                }
            });
        }


        $(document).ready(function(){

            //Creamos una fila en el head de la tabla y lo clonamos para cada columna
            $('.datatable-notifications thead tr').clone(true).appendTo( '.datatable-notifications thead' );


            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);

            $.extend(true, $.fn.dataTable.defaults, {
                order: [[ 2, 'desc' ]],
                columnDefs: [
                    {
                        orderable: false,
                        className: 'select-checkbox',
                        targets: 0
                    },
                    {
                        orderable: false,
                        targets: [0]
                    },
                    {
                        searchable: false,
                        targets: '_all'
                    }
                ],
                dom: '<"table-header" Br>t<"table-footer"lpi><"actions">',
            });
             table = $('.datatable-notifications:not(.ajaxTable)').removeAttr('width').DataTable({
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                ajax:"{{ route('scheduler.notifications-email.get-notifications') }}",

                columns:[
                    {data:'any'} ,
                    {data:'notifications-status'} ,
                    {data:'notifications-created_at'},
                    {data:'notifications-email_subject'},
                    {data:'suppliers-wms_name'},
                    {data:'suppliers-wms_id'},
                    {data:'notifications-type'},
                    {data:'created_by-name'},

                ],
                 createdRow: function( row, data, dataIndex ) {
                     $( row ).attr('class', 'notificationClass');
                     $( row ).attr('data-status', data['notification-status']);
                 },

                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos - Para exportar"]],
            });

            $('.datatable-notifications thead tr:eq(0)').addClass('dt-search-row');
            $('.datatable-notifications thead tr:eq(0) th').each( function (i) {
                let title = $(this).text().trim(); //es el nombre de la columna
                $(this).html( '<input style="display:none" type="text" placeholder="{{trans('scheduler.appointments.fields.search')}} '+title+'" />' );


                $( 'input', this ).on( 'keyup change', function () {
                    table.column($(this).data());

                    if ( table.column(i).search() !== this.value ) {
                     //   console.log(this.value);
                        table
                            .column(i)
                            .search( this.value )
                            .draw();

                    }
                } );
            } );


            let data = $(".notificationClass");
            if(data === 0){
                data.appendChild('<p>No leido </p>');

            }else{
                data.text('Leido');
            }



        });



    </script>
@endsection
