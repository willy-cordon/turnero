@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.home.title') }}</title>
    <link href="{{ asset('css/datatables.css') }}" rel="stylesheet" />
@endsection
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('scheduler.home.title') }}
        </div>
        @foreach($sequences as $sequence)
            <div class="card-header ">
                 {{$sequence->name}}
            </div>
        <div class="card-body d-inline ">

            @foreach($locations as $location)
                @if($sequence->id == $location->sequence_id)
                        <a class="btn btn-success " href="{{ route("scheduler.appointments.create",$location->id) }}">
                            <i class="fas fa-plus"></i> {{ trans('global.add') }} {{ trans('scheduler.appointments.title_singular') }} {{$location->name}}
                        </a>
                @endif

            @endforeach
        </div>


        @endforeach

         <div class="card-header ">
             {{ trans('scheduler.home.fields.no_sequence') }}
         </div>
         <div class="card-body">

            @foreach($locations as $location)
                @if(empty($location->sequence_id))
                    <div class=" d-inline ">
                        <a class="btn btn-success " href="{{ route("scheduler.appointments.create",$location->id) }}">
                            <i class="fas fa-plus"></i> {{ trans('global.add') }} {{ trans('scheduler.appointments.title_singular') }} {{$location->name}}
                        </a>
                    </div>
                @endif
            @endforeach
         </div>
    </div>



@endsection
@section('scripts')

@endsection




