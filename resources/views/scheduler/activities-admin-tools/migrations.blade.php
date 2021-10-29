@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.activity_migrations.menu_migration') }}</title>
@endsection
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('scheduler.activity_migrations.title_migration_appointment') }}
        </div>

        <div class="card-body">
            <form action="{{ route("scheduler.activities-admin-tools.migration-appointment") }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group col-sm-6 {{ $errors->has('appointment_id') ? 'has-error' : '' }}">
                        <label for="appointment_id">{{ trans('scheduler.activity_migrations.fields.appointment_id') }}*</label>
                        <input type="text" id="appointment_id" name="appointment_id" class="form-control" value="{{ old('name', isset($activity) ? $activity->name : '') }}" required>
                        @if($errors->has('appointment_id'))
                            <em class="invalid-feedback">
                                {{ $errors->first('appointment_id') }}
                            </em>
                        @endif

                    </div>

                    <div class="form-group col-sm-6 {{ $errors->has('users_migration') ? 'has-error' : '' }}">
                        <label for="users_migration">{{ trans('scheduler.activity_migrations.fields.users_migration') }}*</label>
                        <select name="users_migration" id="users_migration" class="form-control to-select2">
                            <option></option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->dni }} - {{ $user->name }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('users_migration'))
                            <em class="invalid-feedback">
                                {{ $errors->first('users_migration') }}
                            </em>
                        @endif
                        <p class="helper-block">
                            {{ trans('scheduler.settings.fields.end_hour_helper') }}
                        </p>
                    </div>

                </div>
                <div>
                    <input class="btn btn-success" type="submit" value="{{ trans('scheduler.activity_migrations.buttonMigration') }}">
                </div>
            </form>


        </div><div class="card-header">
            {{ trans('scheduler.activity_migrations.title_migration_user') }}
        </div>

        <div class="card-body">
            <form action="{{ route("scheduler.activities-admin-tools.migration-user") }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group col-sm-6 {{ $errors->has('userfrom') ? 'has-error' : '' }}">
                        <label for="userFrom">{{ trans('scheduler.activity_migrations.fields.userFrom') }}*</label>
                        <select name="userFrom" id="userFrom" class="form-control to-select2">
                            <option></option>
                            @foreach($usersWithActivityInstances as $userWithActivityInstances)
                                <option value="{{ $userWithActivityInstances->id }}">{{ $userWithActivityInstances->dni }} - {{ $userWithActivityInstances->name }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('userFrom'))
                            <em class="invalid-feedback">
                                {{ $errors->first('userFrom') }}
                            </em>
                        @endif
                        <p class="helper-block">
                            {{ trans('scheduler.settings.fields.end_hour_helper') }}
                        </p>
                    </div>

                    <div class="form-group col-sm-6 {{ $errors->has('userTo') ? 'has-error' : '' }}">
                        <label for="userTo">{{ trans('scheduler.activity_migrations.fields.userTo') }}*</label>
                        <select name="userTo" id="userTo" class="form-control to-select2">
                            <option></option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->dni }} - {{ $user->name }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('userTo'))
                            <em class="invalid-feedback">
                                {{ $errors->first('userTo') }}
                            </em>
                        @endif
                        <p class="helper-block">
                            {{ trans('scheduler.settings.fields.end_hour_helper') }}
                        </p>
                    </div>

                </div>
                <div>
                    <input class="btn btn-success" type="submit" value="{{ trans('scheduler.activity_migrations.buttonMigration') }}">
                </div>
            </form>


        </div>



    </div>
@endsection