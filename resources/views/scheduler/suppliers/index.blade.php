@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.suppliers.title') }}</title>
    <link href="{{ asset('css/datatables.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("scheduler.suppliers.create") }}">
                <i class="fas fa-plus"></i> {{ trans('global.add') }} {{ trans('scheduler.suppliers.title_singular') }}
            </a>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            {{ trans('scheduler.suppliers.title') }}
        </div>

        <div class="card-body">
            <div>
                <div class="loading" style="display:none; text-align: center; font-weight: bold; width: 100%; height: calc(100%); background: rgba(255,255,255,0.4); position: absolute; left: 0; z-index: 1; top: 0px; padding-top: 14px;">
                    Procesando...
                </div>
                <table class="table table-bordered table-striped table-hover datatable datatable-loading datatable-supplier">
                    <thead>
                    <tr>
                        <th class="not-export-col">

                        </th>
                        <th class="not-export-col">
                            {{ trans('global.action') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.suppliers.fields.status_intervened') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.suppliers.fields.status_supplier') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.suppliers.fields.scheme') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.suppliers.fields.supplier_group') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.suppliers.fields.wms_id') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.suppliers.fields.wms_name') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.suppliers.fields.wms_date') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.suppliers.fields.wms_age') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.suppliers.fields.wms_gender') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.suppliers.fields.email') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.suppliers.fields.address') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.suppliers.fields.aux5') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.suppliers.fields.aux4') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.suppliers.fields.phone_min') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.suppliers.fields.contact_min') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.suppliers.fields.aux1_min') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.suppliers.fields.aux2_min') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.suppliers.fields.aux3') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.suppliers.fields.created_at') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.suppliers.fields.address_validate') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.suppliers.fields.responsible') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.suppliers.fields.recruiter') }}
                        </th>
                        <th class="search-column">
                            {{ trans('scheduler.suppliers.fields.comorbidity') }}
                        </th>




                    </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot></tfoot>
                </table>
            </div>


{{--     Modal       --}}
            <div class="modal fade" id="edit-dialog" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel"> {{ trans('scheduler.supplier_intervention_log.title') }}</h5>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <input type="hidden" id="supplier_id">

                            <div style="width: 100%; border: 1px solid #ededed; border-radius: 4px; margin-bottom: 20px; ">
                                <h6 class="modal-title" id="editModalData" style="border-bottom: 1px solid #adadad; margin-bottom: 5px;background: #ededed;  padding: 10px; border-radius: 5px 5px 0 0"></h6>
                            </div>

                            <div class="form-row">


                                <div class="form-group col-md-4 {{ $errors->has('intervention_reason') ? 'has-error' : '' }}">
                                    <label for="reasons">{{ trans('scheduler.supplier_intervention_log.fields.reasons_intervention') }} *</label>
                                    <select name="reasons" id="reasons" class="form-control to-select2"  style="width: 100%"  data-placeholder="{{trans('scheduler.supplier_intervention_log.fields.reasons_placeHolder')}}">
                                        <option></option>
                                        @foreach($reasons as $reason)
                                            <option value="{{ $reason }}" {{ $reason == $defaultReason ? 'selected' : ''  }}>{{ $reason }}</option>
                                        @endforeach
                                    </select>
                                    <p class="error"></p>
                                </div>

                                <div style="display: none" class="form-group col-md-8 {{ $errors->has('description') ? 'has-error' : '' }}">
                                    <label for="description">{{ trans('scheduler.supplier_intervention_log.fields.description') }}</label>
                                    <input type="text" id="description" name="description" class="form-control"  >
                                    @if($errors->has('description'))
                                        <em class="invalid-feedback">
                                            {{ $errors->first('description') }}
                                        </em>
                                    @endif
                                    <p class="helper-block">
                                        {{ trans('scheduler.supplier_intervention_log.fields.description_helper') }}
                                    </p>
                                </div>


                            </div>

                        </div>
                        <div class="modal-footer" style="position: relative">

                            <button type="button" class="btn btn-secondary" onclick="buttonCancel()" data-dismiss="modal">{{ trans('global.cancel') }}</button>
                            <button type="button" class="btn btn-primary" onclick="saveSupplierInterventionLog();">{{ trans('global.save') }}</button>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>


    <form id="delete_form" action="" method="POST"  style="display: inline-block;">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <div class="modal fade delete-confirm-submit" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">{{ trans('global.delete') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{ trans('global.areYouSure') }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('global.no') }}</button>
                        <button type="button" class="btn btn-primary" onclick="$(this).closest('form').submit();">{{ trans('global.yes') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('scripts')
    @parent
    <!--Lo necesario para que ande datatables-->
    <script src="{{ asset('js/datatables.js') }}"></script>
    @include('partials.datatables_globals')
    <!-- -->
    <script>
        var table;
        $(document).ready(function(){

            //Creamos una fila en el head de la tabla y lo clonamos para cada columna
            $('.datatable-supplier thead tr').clone(true).appendTo( '.datatable-supplier thead' );


            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);

            $.extend(true, $.fn.dataTable.defaults, {
                order: [[ 3, 'desc' ]],
                columnDefs: [
                    {
                        orderable: false,
                        className: 'select-checkbox',
                        targets: 0
                    },
                    {
                        orderable: false,
                        targets: [0,1,6]
                    },
                    {
                        searchable: false,
                        targets: '_all'
                    }
                ],
                dom: '<"table-header" Br>t<"table-footer"lpi><"actions">',
            });
            table = $('.datatable-supplier:not(.ajaxTable)').removeAttr('width').DataTable({
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                ajax:"{{ route('scheduler.suppliers.get-suppliers') }}",

                columns:[
                    {data:'any'} ,
                    {data:'anyTwo'},
                    {data:'suppliers-is_intervened'} ,
                    {data:'suppliers-status'},
                    {data:'schemes-name'},
                    {data:'supplier_groups-name'},
                    {data:'suppliers-wms_id'} ,
                    {data:'suppliers-wms_name'},
                    {data:'suppliers-wms_date'},
                    {data:'suppliers-wms_age'},
                    {data:'suppliers-wms_gender'},
                    {data:'suppliers-email'},
                    {data:'suppliers-address'},
                    {data:'suppliers-aux5'},
                    {data:'suppliers-aux4'},
                    {data:'suppliers-phone'},
                    {data:'suppliers-contact'},
                    {data:'suppliers-aux1'},
                    {data:'suppliers-aux2'},
                    {data:'suppliers-aux3'},
                    {data:'suppliers-created_at'},
                    {data:'suppliers-validate_address'},
                    {data:'users-name'},
                    {data:'recruiter-name'},
                    {data:'suppliers-comorbidity'},


                ],
                createdRow: function( row, data, dataIndex ) {
                    $(row).find('td:eq(2)').attr('data-export-data', data["is_intervened_text"]);
                    $(row).find('td:eq(3)').attr('data-export-data', data["supplier_status_text"]);
                    $( row ).attr('id', 'sp-' + data['suppliers-id']);
                    $( row ).addClass('status-'+data['action_id']);
                    $( row ).addClass(data['is_intervened']);
                    $( row ).addClass(data['status_outside']);
                    $( row ).attr('data-edit-data', data['edit_data']);
                },
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos - Para exportar"]],
            });

            $('.datatable-supplier thead tr:eq(0)').addClass('dt-search-row');
            $('.datatable-supplier thead tr:eq(0) th').each( function (i) {
                let title = $(this).text().trim(); //es el nombre de la columna
                $(this).html( '<input style="display:none" type="text" placeholder="{{trans('scheduler.appointments.fields.search')}} '+title+'" />' );


                $( 'input', this ).on( 'keyup change', function () {
                    table.column($(this).data());

                    if ( table.column(i).search() !== this.value ) {
                      //  console.log(this.value);
                        table
                            .column(i)
                            .search( this.value )
                            .draw();

                    }
                } );
            } );
        });



        function buttonCancel()
        {
            $(".loading").hide();
        }

        function enableSupplierIntervention(supplierId){
            $(".loading").show();
            $("#edit-dialog").modal("show");
            // toggleIntervention(supplierId, true)
           supplier_row = $("#sp-"+supplierId);
            $("#editModalData").html(supplier_row.attr('data-edit-data'));
            $("#supplier_id").val(supplierId);



        }

        function saveSupplierInterventionLog(){

            let reasons = $("#reasons").val();
            let description = $("#description").val();
            let supplier_id = $("#supplier_id").val();
            let error = $(".error");
            console.log(reasons);
            error.hide();
            if( reasons !== ''){

                $.ajax({
                    url: "{{route('scheduler.supplier-intervention-logs.save')}}",
                    type:"POST",
                    data:{
                        "_token": "{{ csrf_token() }}",
                        supplier_id : supplier_id,
                        description : description,
                        reasons     : reasons

                    },
                    success:function(response){
                        if(response.status === 'ok'){
                            $(".loading").show();
                            toggleIntervention(supplier_id, true);
                            $("#edit-dialog").modal("hide");
                            table.draw();
                        }else {
                            console.log(response);
                        }

                    },
                    error :function( data ) {

                    }
                });

            }else{
                error.text('El campo ' +'{{ trans("scheduler.supplier_intervention_log.fields.reasons_intervention") }}' + ' es requerido' );
                error.css('color','red');
                error.show();

            }



        }
        function disableSupplierIntervention(supplierId){
            $(".loading").show();
            toggleIntervention(supplierId, false)
        }

        function toggleIntervention(supplierId, interventionStatus){

            $.ajax({
                url: "{{route('scheduler.supplier.toggle-intervention')}}",
                type:"PUT",
                data:{
                    "_token": "{{ csrf_token() }}",
                    supplier_id : supplierId,
                    intervention_status : interventionStatus

                },
                success:function(response){
                    if(response.status === 'ok'){
                        table.draw();
                    }else {
                        console.log(response);
                    }
                    $(".loading").hide();
                },
                error :function( data ) {

                }
            });

        }

    </script>
@endsection




