@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.supplier_groups.title_singular') }}</title>
@endsection
@section('content')
    <a class="back-link" href="{{route('scheduler.supplier-groups.index')}}"><i class="fas fa-long-arrow-alt-left"></i> {{trans('scheduler.supplier_groups.title')}}</a>
    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('scheduler.supplier_groups.title_singular') }}
        </div>

        <div class="card-body">
            <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method($method)
                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label for="name">{{ trans('scheduler.supplier_groups.fields.name') }}*</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($supplierGroup) ? $supplierGroup->name : '') }}" >
                    @if($errors->has('name'))
                        <em class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.supplier_groups.fields.name_helper') }}
                    </p>
                </div>
                <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                    <label for="description">{{ trans('scheduler.supplier_groups.fields.description') }}</label>
                    <input type="text" id="description" name="description" class="form-control" value="{{ old('description', isset($supplierGroup) ? $supplierGroup->description : '') }}">
                    @if($errors->has('description'))
                        <em class="invalid-feedback">
                            {{ $errors->first('description') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.supplier_groups.fields.description_helper') }}
                    </p>
                </div>

                <div>
                    <input class="btn btn-success" type="submit" value="{{ trans('global.save') }}">
                    <a href="{{ route('scheduler.supplier-groups.index')}}" class="btn btn-danger">{{ trans('global.cancel') }}</a>
                </div>
            </form>


        </div>
    </div>
@endsection