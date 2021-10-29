@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.supplier_migrations.title') }}</title>
@endsection
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('scheduler.supplier_migrations.supplier_migration') }} |
            <small><strong>Importante: </strong>Esta acción migrará el voluntario y los turnos asignados al mismo</small>
        </div>

        <div class="card-body">
            <form action="{{ route("scheduler.appointment-admin-tools.migration-supplier") }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group col-sm-6 {{ $errors->has('supplier_id') ? 'has-error' : '' }}">
                        <label for="supplier_id">{{ trans('scheduler.supplier_migrations.fields.supplier_id') }}*</label>
                        <select name="supplier_id" id="supplier_id" class="form-control to-select2">
                            <option></option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->wms_id }} - {{ $supplier->wms_name }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('supplier_id'))
                            <em class="invalid-feedback">
                                {{ $errors->first('supplier_id') }}
                            </em>
                        @endif
                        <p class="helper-block">
                            {{ trans('scheduler.supplier_migrations.fields.supplier_helper') }}
                        </p>
                    </div>

                    <div class="form-group col-sm-6 {{ $errors->has('user_id') ? 'has-error' : '' }}">
                        <label for="user_id">{{ trans('scheduler.supplier_migrations.fields.user_id') }}*</label>
                        <select name="user_id" id="user_id" class="form-control to-select2">
                            <option></option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->dni }} - {{ $user->name }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('user_id'))
                            <em class="invalid-feedback">
                                {{ $errors->first('user_id') }}
                            </em>
                        @endif
                        <p class="helper-block">
                            {{ trans('scheduler.supplier_migrations.fields.user_helper') }}
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