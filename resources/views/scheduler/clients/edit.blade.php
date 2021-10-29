@extends('layouts.admin')
@section('style')
    <title>{{ config('app.name') }} |  {{ trans('global.edit') }} {{ trans('scheduler.clients.title_singular') }}</title>
@endsection
@section('content')
<a class="back-link" href="{{route('scheduler.clients.index')}}"><i class="fas fa-long-arrow-alt-left"></i> {{trans('scheduler.clients.title')}}</a>

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('scheduler.clients.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("scheduler.clients.update", [$client->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="name">{{ trans('scheduler.clients.fields.name') }}*</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($client) ? $client->name : '') }}" required>
                @if($errors->has('name'))
                    <em class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('scheduler.clients.fields.name_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('token') ? 'has-error' : '' }}">
                <label for="description">{{ trans('scheduler.clients.fields.token') }}*</label>
                <input type="text" id="token" name="token" disabled class="form-control" value="{{ old('token', isset($client) ? $client->api_token : '') }}" required>

            </div>

            <div>
                <input class="btn btn-success" type="submit" value="{{ trans('global.save') }}">
                <a href="{{ route('scheduler.clients.index')}}" class="btn btn-danger">{{ trans('global.cancel') }}</a>
            </div>
        </form>


    </div>
</div>
@endsection