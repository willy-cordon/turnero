@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.sequence.title_singular') }}</title>
@endsection
@section('content')
    <a class="back-link" href="{{route('scheduler.sequence.index')}}"><i class="fas fa-long-arrow-alt-left"></i> {{trans('scheduler.sequence.title')}}</a>
    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('scheduler.sequence.title_singular') }}
        </div>

        <div class="card-body">
            <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method($method)
                <div class="form-row">

                <div class="form-group col-sm-4 {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label for="name">{{ trans('scheduler.sequence.fields.name') }}*</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($sequence) ? $sequence->name : '') }}" >
                    @if($errors->has('name'))
                        <em class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.sequence.fields.name_helper') }}
                    </p>
                </div>
                <div class="form-group col-sm-6 {{ $errors->has('description') ? 'has-error' : '' }}">
                    <label for="description">{{ trans('scheduler.sequence.fields.description') }}</label>
                    <input type="text" id="description" name="description" class="form-control" value="{{ old('description', isset($sequence) ? $sequence->description : '') }}">
                    @if($errors->has('description'))
                        <em class="invalid-feedback">
                            {{ $errors->first('description') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.sequence.fields.description_helper') }}
                    </p>
                </div>

                <div class="form-group col-sm-2 text-center justify-content-center">
                    <label for="show_in_workflow">{{ trans('scheduler.sequence.fields.show_in_workflow') }}</label>
                    <div style="clear: both"></div>
                    <input name="show_in_workflow" type="hidden" value="0"/>
                    <input id="show_in_workflow"
                           name="show_in_workflow"
                           value="1"
                           type="checkbox"
                           data-toggle="toggle"
                           data-on="{{ trans('global.yes') }}"
                           data-off="{{ trans('global.no') }}"
                           data-onstyle="success" data-offstyle="primary"
                            {{ ( old('show_in_workflow',  isset($sequence) ? $sequence->show_in_workflow : '' ) == 1 ? 'checked':'') }}>
                </div>
                </div>


                <div>
                    <input class="btn btn-success" type="submit" value="{{ trans('global.save') }}">
                    <a href="{{ route('scheduler.sequence.index')}}" class="btn btn-danger">{{ trans('global.cancel') }}</a>
                </div>
            </form>


        </div>
    </div>
@endsection