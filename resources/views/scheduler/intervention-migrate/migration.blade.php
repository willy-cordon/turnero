@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.intervention_migration.title') }}</title>
@endsection
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('scheduler.intervention_migration.title') }} |
            <small><strong>Importante: </strong>Esta acción migrará el voluntario intervenido y los turnos asignados al mismo</small>
        </div>

        <div class="card-body">
            <form action="{{ route("scheduler.intervention-migrate.migration-intervention") }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group col-sm-6 {{ $errors->has('supplier_id') ? 'has-error' : '' }}">
                        <label for="supplier_id">{{ trans('scheduler.intervention_migration.fields.supplier_intervention') }}*</label>
                        <select name="supplier_id" id="supplier_id" class="form-control to-select2">
                            <option></option>
                            @foreach($supplierInterventions as $supplierIntervention)
                                <option value="{{ $supplierIntervention->id }}">{{ $supplierIntervention->wms_name }} </option>
                            @endforeach
                        </select>
                        @if($errors->has('supplier_id'))
                            <em class="invalid-feedback">
                                {{ $errors->first('supplier_id') }}
                            </em>
                        @endif
                        <p class="helper-block">
                            {{ trans('scheduler.intervention_migration.fields.supplier_intervention_helper') }}
                        </p>
                    </div>

                    <div class="form-group col-sm-6 {{ $errors->has('user_id') ? 'has-error' : '' }}">
                        <label for="user_id">{{ trans('scheduler.intervention_migration.fields.doctor') }}*</label>
                        <select name="user_id" id="user_id" class="form-control to-select2">
                            <option></option>
                            @foreach($userDoctors as $userDoctor)
                                <option value="{{ $userDoctor->id }}">{{ $userDoctor->name }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('user_id'))
                            <em class="invalid-feedback">
                                {{ $errors->first('user_id') }}
                            </em>
                        @endif
                        <p class="helper-block">
                            {{ trans('scheduler.intervention_migration.fields.doctor_helper') }}
                        </p>
                    </div>

                </div>
                <div>
                    <input class="btn btn-success" type="submit" value="{{ trans('scheduler.intervention_migration.buttonMigration') }}">
                </div>
            </form>


        </div>


    </div>
@endsection