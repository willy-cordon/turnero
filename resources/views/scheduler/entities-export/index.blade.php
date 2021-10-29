@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.intervention_migration.title') }}</title>
@endsection
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('scheduler.entities_export.title') }}

        </div>

        <div class="card-body">
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        <a href="/scheduler/entities-export/activities" target="_blank" class="btn btn-info"> Actividades  <i class="fa-fw fas fa-tasks text-white">

                            </i></a>
                    </div>
                    <div class="col-md-3">
                        <a href="/scheduler/entities-export/suppliers" target="_blank" class="btn btn-info"> Voluntarios <i class="fa-fw fas fa-users nav-icon"></i></a>
                    </div>
                    <div class="col-md-3">
                        <a href="/scheduler/entities-export/appointments" target="_blank" class="btn btn-info"> Turnos  <i class="fas fa-calendar nav-icon"></i></a>
                    </div>
                    <div class="col-md-3">
                        <a href="/scheduler/entities-export/intervened" target="_blank" class="btn btn-info"> Registros de intervencion <i class="fas fa-clipboard-list nav-icon"></i></a>
                    </div>
                </div>
            </div>


        </div>


    </div>
@endsection