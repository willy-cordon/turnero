@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.docks.title_singular') }}</title>
@endsection
@section('content')
    <a class="back-link" href="{{route('scheduler.docks.index')}}"><i class="fas fa-long-arrow-alt-left"></i> {{trans('scheduler.docks.title')}}</a>
<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('scheduler.docks.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method($method)
            <div class="form-row">
                <div class="form-group col-md-6 {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label for="name">{{ trans('scheduler.docks.fields.name') }}*</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($dock) ? $dock->name : '') }}" >
                    @if($errors->has('name'))
                        <em class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.docks.fields.name_helper') }}
                    </p>
                </div>
                <div class="form-group col-md-6 {{ $errors->has('location') ? 'has-error' : '' }}">
                    <label for="location">{{ trans('scheduler.docks.fields.location') }}*</label>
                    <select name="location" id="location" class="form-control to-select2" data-minimum-results-for-search="-1"  data-placeholder="{{trans('scheduler.docks.fields.location_placeholder')}}">
                        <option></option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ (collect(old('location', isset($dock) ? $dock->location_id : ''))->contains($location->id)) ? 'selected':'' }}>{{ $location->name }}</option>
                        @endforeach
                    </select>

                    @if($errors->has('location'))
                        <em class="invalid-feedback">
                            {{ $errors->first('location') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.docks.fields.location_helper') }}
                    </p>
                </div>
            </div>
            <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                <label for="description">{{ trans('scheduler.docks.fields.description') }}</label>
                <input type="text" id="description" name="description" class="form-control" value="{{ old('description', isset($dock) ? $dock->description : '') }}">
                @if($errors->has('description'))
                    <em class="invalid-feedback">
                        {{ $errors->first('description') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('scheduler.docks.fields.description_helper') }}
                </p>
            </div>

            <div>
                <input class="btn btn-success" type="submit" value="{{ trans('global.save') }}">
                <a href="{{ route('scheduler.docks.index')}}" class="btn btn-danger">{{ trans('global.cancel') }}</a>
            </div>
        </form>


    </div>
</div>
@endsection