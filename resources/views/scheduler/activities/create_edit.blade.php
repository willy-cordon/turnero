@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.activities.title_singular') }}</title>
@endsection
@section('content')
    <a class="back-link" href="{{route('scheduler.activities.index')}}"><i class="fas fa-long-arrow-alt-left"></i> {{trans('scheduler.activities.title')}}</a>
    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('scheduler.activities.title_singular') }}
        </div>

        <div class="card-body">
            <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method($method)
                <div class="row">

                    <div class="form-group col-6 {{ $errors->has('name') ? 'has-error' : '' }}">
                        <label for="name">{{ trans('scheduler.activities.fields.name') }}*</label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($activity) ? $activity->name : '') }}" >
                        @if($errors->has('name'))
                            <em class="invalid-feedback">
                                {{ $errors->first('name') }}
                            </em>
                        @endif

                    </div>
                    <div class="form-group col-6 {{ $errors->has('description') ? 'has-error' : '' }}">
                        <label for="description">{{ trans('scheduler.activities.fields.description') }}</label>
                        <input type="text" id="description" name="description" class="form-control" value="{{ old('description', isset($activity) ? $activity->description : '') }}">
                        @if($errors->has('name'))
                            <em class="invalid-feedback">
                                {{ $errors->first('description') }}
                            </em>
                        @endif

                    </div>

                </div>

                <div class="row">

                    <div class="form-group col-6 {{ $errors->has('question_name') ? 'has-error' : '' }}">
                        <label for="question_name">{{ trans('scheduler.activities.fields.question_name') }}*</label>
                        <input type="text" id="question_name" name="question_name" class="form-control" value="{{ old('question_name', isset($activity) ? $activity->question_name : '') }}" >
                        @if($errors->has('question_name'))
                            <em class="invalid-feedback">
                                {{ $errors->first('question_name') }}
                            </em>
                        @endif

                    </div>

                    <div class="form-group col-6 {{ $errors->has('activity_group_id') ? 'has-error' : '' }}">
                        <label for="activity_group_id">{{ trans('scheduler.activities.fields.group') }}*

                        </label>
                        <select name="activity_group_id" id="activity_group_id" class="form-control"  >
                            <option></option>
                            @foreach($activityGroups as $activityGroup)
                                <option value="{{ $activityGroup->id }}" {{ (collect(old('activity_group_id', isset($activity) ? $activity->activity_group_id : ''))->contains($activityGroup->id)) ? 'selected':'' }}> {{ $activityGroup->name }} </option>

                            @endforeach
                        </select>
                        @if($errors->has('activity_group_id'))
                            <em class="invalid-feedback">
                                {{ $errors->first('activity_group_id') }}
                            </em>
                        @endif
                        <p class="helper-block">
                            {{ trans('cruds.role.fields.permissions_helper') }}
                        </p>
                    </div>

                </div>

                <div class="row">

                    <div class="form-group col-6 {{ $errors->has('days_from_appointment') ? 'has-error' : '' }}">
                        <label for="days_from_appointment">{{ trans('scheduler.activities.fields.days_from_appointment') }}*</label>
                        <input type="text" id="days_from_appointment" name="days_from_appointment" class="form-control" value="{{ old('days_from_appointment', isset($activity) ? $activity->days_from_appointment : '') }}" >
                        @if($errors->has('days_from_appointment'))
                            <em class="invalid-feedback">
                                {{ $errors->first('days_from_appointment') }}
                            </em>
                        @endif

                    </div>
                    <div class="form-group col-6 {{ $errors->has('fire_moment') ? 'has-error' : '' }}">
                        <label for="fire_moment">{{ trans('scheduler.activities.fields.created_activity') }}*

                        </label>
                        <select name="fire_moment" id="fire_moment" class="form-control"  >
                            <option></option>
                            @foreach($answers as $answer)
                                <option value="{{ $answer }}" {{ (collect(old('fire_moment', isset($activity) ? $activity->fire_moment : ''))->contains($answer)) ? 'selected':'' }}>{{ $answer }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('fire_moment'))
                            <em class="invalid-feedback">
                                {{ $errors->first('fire_moment') }}
                            </em>
                        @endif
                        <p class="helper-block">
                            {{ trans('cruds.role.fields.permissions_helper') }}
                        </p>
                    </div>
                </div>

                <div class="form-group {{ $errors->has('activity_actions') ? 'has-error' : '' }}">
                    <label for="activity-action">{{ trans('scheduler.activities.fields.actions') }}*
                        <span class="btn btn-info btn-xs select-all">{{ trans('global.select_all') }}</span>
                        <span class="btn btn-info btn-xs deselect-all">{{ trans('global.deselect_all') }}</span></label>
                    <select name="activity_actions[]" id="activity-action" class="form-control to-select2" multiple="multiple" >
                        @foreach($activityActions as $id => $activityAction)

                            <option value="{{ $id }}" {{ (in_array($id, old('activityActions', [])) || isset($activity) && $activity->activityActions->pluck('id')->contains($id)) ? 'selected' : '' }}>{{ $activityAction }}</option>

                        @endforeach
                    </select>
                    @if($errors->has('activity_actions'))
                        <em class="invalid-feedback">
                            {{ $errors->first('activity_actions') }}
                        </em>
                    @endif

                </div>

                <div>
                    <input class="btn btn-success" type="submit" value="{{ trans('global.save') }}">
                </div>
            </form>


        </div>
    </div>
@endsection