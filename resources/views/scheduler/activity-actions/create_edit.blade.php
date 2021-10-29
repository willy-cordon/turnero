@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.activity_actions.title_singular') }}</title>
@endsection
@section('content')
    <a class="back-link" href="{{route('scheduler.activity-actions.index')}}"><i class="fas fa-long-arrow-alt-left"></i> {{trans('scheduler.activity_actions.title')}}</a>
    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('scheduler.activity_actions.title_singular') }}
        </div>
        <div class="card-body">
            <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method($method)
                <div class="row">
                    <div class="form-group col-sm-12 {{ $errors->has('name') ? 'has-error' : '' }} mr-2">
                        <label for="name">{{ trans('scheduler.activity_actions.fields.name') }}*</label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($activityAction) ? $activityAction->name : '') }}" >
                        @if($errors->has('name'))
                            <em class="invalid-feedback">
                                {{ $errors->first('name') }}
                            </em>
                        @endif

                    </div>
                    <div class="form-group col-sm-12 {{ $errors->has('description') ? 'has-error' : '' }}">
                        <label for="description">{{ trans('scheduler.activity_actions.fields.description') }}</label>
                        <input type="text" id="description" name="description" class="form-control" value="{{ old('description', isset($activityAction) ? $activityAction->description : '') }}">
                        @if($errors->has('description'))
                            <em class="invalid-feedback">
                                {{ $errors->first('description') }}
                            </em>
                        @endif

                    </div>
                </div>
            <div class="row">

                <div class="form-group col-sm-6 {{ $errors->has('day') ? 'has-error' : '' }}">
                    <label for="activity_status_triggered">{{ trans('scheduler.activity_actions.fields.status') }}</label>
                    <select name="activity_status_triggered" id="activity_status_triggered" class="form-control to-select2">
                        <option></option>
                        @foreach($status as $state )
                            <option value="{{ $state }}" {{ (collect(old('activity_status_triggered', isset($activityAction) ? $activityAction->activity_status_triggered : ''))->contains($state)) ? 'selected':'' }}>{{ $state }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('activity_status_triggered'))
                        <em class="invalid-feedback">
                            {{ $errors->first('activity_status_triggered') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.activity_actions.fields.activity_status_triggered_helper') }}
                    </p>
                </div>

                <div class="form-group col-sm-6 {{ $errors->has('day') ? 'has-error' : '' }}">
                    <label for="activity_fired">{{ trans('scheduler.activity_actions.fields.activity') }}</label>
                    <select name="activity_fired" id="activity_fired" class="form-control to-select2">
                        <option></option>
                        @foreach($activitiesManuals as $activity )
                            <option value="{{ $activity->id }}" {{ (collect(old('activity_fired', isset($activityAction) ? $activityAction->activity_fired : ''))->contains($activity->id)) ? 'selected':'' }}>{{ $activity->name }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('activity_fired'))
                        <em class="invalid-feedback">
                            {{ $errors->first('activity_fired') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.activity_actions.fields.activity_fired_helper') }}
                    </p>
                </div>

            </div>


                <div>
                    <input class="btn btn-success" type="submit" value="{{ trans('global.save') }}">
                    <a href="{{ route('scheduler.activity-actions.index')}}" class="btn btn-danger">{{ trans('global.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection
