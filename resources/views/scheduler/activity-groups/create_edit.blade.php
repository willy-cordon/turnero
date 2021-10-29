@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.activity_groups.title_singular') }}</title>
@endsection
@section('content')
    <a class="back-link" href="{{route('scheduler.activity-groups.index')}}"><i class="fas fa-long-arrow-alt-left"></i> {{trans('scheduler.activity_groups.title')}}</a>
    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('scheduler.activity_groups.title_singular') }}
        </div>
        <div class="card-body">
            <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method($method)

                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }} mr-2">
                    <label for="name">{{ trans('scheduler.activity_groups.fields.name') }}*</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($activityGroup) ? $activityGroup->name : '') }}" >
                    @if($errors->has('name'))
                        <em class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.actions.fields.name_helper') }}
                    </p>
                </div>
                <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                    <label for="description">{{ trans('scheduler.activity_groups.fields.description') }}</label>
                    <input type="text" id="description" name="description" class="form-control" value="{{ old('description', isset($activityGroup) ? $activityGroup->description : '') }}">
                    @if($errors->has('description'))
                        <em class="invalid-feedback">
                            {{ $errors->first('description') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.actions.fields.description_helper') }}
                    </p>

                </div>

                <div class="form-group {{ $errors->has('type') ? 'has-error' : '' }}">
                    <label for="description">{{ trans('scheduler.activity_groups.fields.type') }}</label>
                    <select name="type" id="type" class="form-control"  >
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ (collect(old('type',isset($activityGroup) ? $activityGroup->type : '' ))->contains($type)) ? 'selected':'' }}>{{ $type}}</option>

                        @endforeach
                    </select>
                    @if($errors->has('type'))
                        <em class="invalid-feedback">
                            {{ $errors->first('type') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.activity_groups.fields.type_helper') }}
                    </p>
                </div>

                <div class="form-group {{ $errors->has('type') ? 'has-error' : '' }}">
                    <label for="activity_group_type_id">{{ trans('scheduler.activity_groups.fields.group_type') }}</label>
                    <select name="activity_group_type_id" id="activity_group_type_id" class="form-control"  >
                        <option ></option>
                        @foreach($typeGroups as $typeGroup)
                            <option value="{{ $typeGroup->id }}" {{ (collect(old('typeGroup',isset($activityGroup) ? $activityGroup->activity_group_type_id : '' ))->contains($typeGroup->id)) ? 'selected':'' }}>{{ $typeGroup->name}}</option>

                        @endforeach
                    </select>
                    @if($errors->has('activity_group_type_id'))
                        <em class="invalid-feedback">
                            {{ $errors->first('activity_group_type_id') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.activity_groups.fields.type_helper') }}
                    </p>
                </div>


                <input type="hidden" name="location_id" value="{{$location->id}}">
                <div>
                    <input class="btn btn-success" type="submit" value="{{ trans('global.save') }}">
                    <a href="{{ route('scheduler.activity-groups.index')}}" class="btn btn-danger">{{ trans('global.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection


