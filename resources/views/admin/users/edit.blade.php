@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('cruds.user.title_singular') }}</title>
@endsection
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.user.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.users.update", [$user->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-row">
                <div class="form-group  col-md-4  {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label for="name">{{ trans('cruds.user.fields.name') }}*</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($user) ? $user->name : '') }}" required>
                    @if($errors->has('name'))
                        <em class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.user.fields.name_helper') }}
                    </p>
                </div>
                <div class="form-group  col-md-4  {{ $errors->has('email') ? 'has-error' : '' }}">
                    <label for="email">{{ trans('cruds.user.fields.email') }}*</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email', isset($user) ? $user->email : '') }}" required>
                    @if($errors->has('email'))
                        <em class="invalid-feedback">
                            {{ $errors->first('email') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.user.fields.email_helper') }}
                    </p>
                </div>
                <div class="form-group  col-md-4  {{ $errors->has('dni') ? 'has-error' : '' }}">
                    <label for="dni">{{ trans('cruds.user.fields.dni') }}*</label>
                    <input type="text" id="dni" name="dni" class="form-control" value="{{ old('dni', isset($user) ? $user->dni : '') }}" required>
                    @if($errors->has('dni'))
                        <em class="invalid-feedback">
                            {{ $errors->first('dni') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.user.fields.dni_helper') }}
                    </p>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group  col-md-4  {{ $errors->has('phone') ? 'has-error' : '' }}">
                    <label for="phone">{{ trans('cruds.user.fields.phone') }}*</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', isset($user) ? $user->phone : '') }}" required placeholder="{{trans('cruds.user.fields.phone_placeholder')}}">
                    @if($errors->has('phone'))
                        <em class="invalid-feedback">
                            {{ $errors->first('phone') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.user.fields.phone_helper') }}
                    </p>
                </div>
                <div class="form-group  col-md-4  {{ $errors->has('password') ? 'has-error' : '' }}">
                    <label for="password">{{ trans('cruds.user.fields.password') }}</label>
                    <input type="password" id="password" name="password" class="form-control">
                    @if($errors->has('password'))
                        <em class="invalid-feedback">
                            {{ $errors->first('password') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.user.fields.password_helper') }}
                    </p>
                </div>

                <div class="form-group col-md-4 {{ $errors->has('supervisor_id') ? 'has-error' : '' }}">
                    <label for="supervisor_id">{{ trans('cruds.user.fields.supervisor') }}</label>
                    <select name="supervisor_id" id="supervisor_id" class="form-control to-select2">
                        <option></option>
                        @foreach($supervisors as $supervisor)
                            <option value="{{ $supervisor->id }}"  {{ (collect(old('type',isset($user) ? $user->supervisor_id : '' ))->contains($supervisor->id)) ? 'selected':'' }}>{{ $supervisor->name }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('supervisor_id'))
                        <em class="invalid-feedback">
                            {{ $errors->first('supervisor_id') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.user.fields.supervisor_helper') }}
                    </p>
                </div>
            </div>
            <div class="form-group {{ $errors->has('roles') ? 'has-error' : '' }}">
                <label for="roles">{{ trans('cruds.user.fields.roles') }}*
                    <span class="btn btn-info btn-xs select-all">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all">{{ trans('global.deselect_all') }}</span></label>
                <select name="roles[]" id="roles" class="form-control to-select2" multiple="multiple" required style="width: 100%">
                    @foreach($roles as $id => $roles)
                        <option value="{{ $id }}" {{ (in_array($id, old('roles', [])) || isset($user) && $user->roles()->pluck('name', 'id')->contains($id)) ? 'selected' : '' }}>{{ $roles }}</option>
                    @endforeach
                </select>
                @if($errors->has('roles'))
                    <em class="invalid-feedback">
                        {{ $errors->first('roles') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.user.fields.roles_helper') }}
                </p>
            </div>
            <div>
                <input class="btn btn-success" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>


    </div>
</div>
@endsection