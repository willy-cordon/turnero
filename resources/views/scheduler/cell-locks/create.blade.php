@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.cell_locks.title_singular') }}</title>
@endsection
@section('content')
    <a class="back-link" href="{{route('scheduler.cell-locks.index')}}"><i class="fas fa-long-arrow-alt-left"></i> {{trans('scheduler.cell_locks.title')}}</a>
    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('scheduler.cell_locks.title_singular') }} - Tipo de visita: {{$location->name}}
        </div>
        <div class="card-body">
            <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method($method)
                <div class="form-row">
                    <div class="form-group col-md-3 {{ $errors->has('lock_date') ? 'has-error' : '' }}">
                        <label for="lock_date">{{ trans('scheduler.locks.fields.lock_date') }}</label>
                        @if($method == 'PUT')
                            <input type="text" id="lock_date" name="lock_date" class="form-control" disabled >
                        @else
                            <div class='input-group lockdate'>
                                <input style="background: #FFF;" type='text' id="lock_date" name="lock_date" class="form-control"  readonly/>
                                <span class="input-group-addon date-picker-button">
                            <span class="glyphicon glyphicon-calendar">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                        </span>
                            </div>
                        @endif
                        @if($errors->has('lock_date'))
                            <em class="invalid-feedback">
                                {{ $errors->first('lock_date') }}
                            </em>
                        @endif

                    </div>
                    <div class="form-group col-3 {{ $errors->has('lock_type') ? 'has-error' : '' }}">
                        <label for="lock_type">{{ trans('scheduler.cell_locks.fields.lock_type') }}*

                        </label>
                        <select name="lock_type" id="lock_type" class="form-control"  required>
                            <option></option>
                            @foreach($locksTypes as $locksType)
                                <option value="{{ $locksType }}" >{{ $locksType }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('activities'))
                            <em class="invalid-feedback">
                                {{ $errors->first('activities') }}
                            </em>
                        @endif
                        <p class="helper-block">
                            {{ trans('cruds.role.fields.permissions_helper') }}
                        </p>
                    </div>
                    <input type="hidden" name="location_id" value="{{$location->id}}">

                    <div class="form-group col-3 {{ $errors->has('dock_name') ? 'has-error' : '' }}">
                        <label for="dock_name">{{ trans('scheduler.cell_locks.fields.dock_name') }}*

                        </label>
                        <select name="dock_name" id="dock_name" class="form-control"  >
                            <option></option>
                            @foreach($docks as $key => $dock)
                                <option value="{{ $key }}" >{{ $dock}}</option>
                            @endforeach
                        </select>
                        @if($errors->has('activities'))
                            <em class="invalid-feedback">
                                {{ $errors->first('activities') }}
                            </em>
                        @endif
                        <p class="helper-block">
                            {{ trans('cruds.role.fields.permissions_helper') }}
                        </p>
                    </div>

                    <div class="form-group col-3 {{ $errors->has('hour') ? 'has-error' : '' }}">
                        <label for="hour">{{ trans('scheduler.cell_locks.fields.hour') }}*

                        </label>
                        <select name="hour" id="hour" class="form-control"  >
                            <option></option>
                            @foreach($hours as $key => $hour)
                                <option value="{{ $key }}" >{{ $hour}}</option>
                            @endforeach
                        </select>
                        @if($errors->has('activities'))
                            <em class="invalid-feedback">
                                {{ $errors->first('activities') }}
                            </em>
                        @endif
                        <p class="helper-block">
                            {{ trans('cruds.role.fields.permissions_helper') }}
                        </p>
                    </div>
                    <input type="hidden" name="hour_key" id="hour_key" value="">
                    <input type="hidden" name="dock_key" id="dock_key" value="">
                </div>



                <div>
                    <input class="btn btn-success" type="submit" value="{{ trans('global.save') }}">
                    <a href="{{ route('scheduler.cell-locks.index')}}" class="btn btn-danger">{{ trans('global.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    @parent

    <script>
        $(function () {
            $('.lockdate').datetimepicker({
                format: 'DD/MM/YYYY',
                locale: 'es',
                ignoreReadonly: true,
                useCurrent: false
            });

        });

        //Inicializamos las variables
        dock = $("#dock_name");
        dock_key = $("#dock_key");
        hour_key = $("#hour_key");
        hour = $("#hour");

        //Escuchamos los input de dock y hour para posteriormente asignarlas a un campo hidden
        dock.change(function(){
            console.log($(this).val());
            let valueDock= $('#dock_name option:selected').text();
            dock_key.val(valueDock);
        });

        hour.change(function(){
            let valueHour = $('#hour option:selected').text();
            hour_key.val(valueHour);
        });

        //Por defecto ocultos
        dock.parent().hide();
        hour.parent().hide();
        //Mostramos y ocultamos segun lo requerido en lock_type
        $('#lock_type').on("change", function(){

            switch ($(this).val()) {
                case 'Celda':
                    dock.parent().show();
                    hour.parent().show();
                    break;
                case 'Circuito':
                    dock.parent().show();
                    hour.parent().hide();
                    break;
                case 'Hora':
                    dock.parent().hide();
                    hour.parent().show();
                    break;

            }

        });



    </script>
@endsection
