@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.locks.title_singular') }}</title>
@endsection
@section('content')
    <a class="back-link" href="{{route('scheduler.locks.index')}}"><i class="fas fa-long-arrow-alt-left"></i> {{trans('scheduler.locks.title')}}</a>
<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('scheduler.locks.title_singular') }}
    </div>
    <div class="card-body">
        <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method($method)
            <div class="form-row">
                <div class="form-group col-md-4 {{ $errors->has('lock_date') ? 'has-error' : '' }}">
                    <label for="lock_date">{{ trans('scheduler.locks.fields.lock_date') }}</label>
                    @if($method == 'PUT')
                    <input type="text" id="lock_date" name="lock_date" class="form-control" value="{{ old('lock_date', isset($schedulerLock) ? $schedulerLock->lock_date : '') }}" disabled >
                    @else
                    <div class='input-group lockdate'>
                        <input style="background: #FFF;" type='text' id="lock_date" name="lock_date" class="form-control"  value="{{ old('lock_date', isset($schedulerLock) ? $schedulerLock->lock_date : '') }}" readonly/>
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
                <div class="form-group col-md-4 {{ $errors->has('available_appointments') ? 'has-error' : '' }}">
                    <label for="available_appointments">{{ trans('scheduler.locks.fields.available_appointments') }}</label>
                    <input type="text" id="available_appointments" name="available_appointments" class="form-control" value="{{ old('available_appointments', isset($schedulerLock) ? $schedulerLock->available_appointments : '') }}">
                    @if($errors->has('available_appointments'))
                        <em class="invalid-feedback">
                            {{ $errors->first('available_appointments') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.locks.fields.available_appointments_helper') }}
                    </p>
                </div>
                <input type="hidden" name="location_id" value="{{$location->id}}">
            </div>
            <div>
                <input class="btn btn-success" type="submit" value="{{ trans('global.save') }}">
                <a href="{{ route('scheduler.locks.index')}}" class="btn btn-danger">{{ trans('global.cancel') }}</a>
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
                disabledDates:  @json($currentLocks),
                useCurrent: false
            });

        })

    </script>
@endsection
