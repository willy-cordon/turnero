@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.suppliers.title_singular') }}</title>
    <link href="{{ asset('https://unpkg.com/swiper/swiper-bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('https://unpkg.com/swiper/swiper-bundle.min.css') }}" rel="stylesheet" />

@endsection
@section('content')
    <a class="back-link" href="{{route('scheduler.supplier.workflow')}}"><i class="fas fa-long-arrow-alt-left"></i> {{trans('scheduler.suppliers.title-workflow')}}</a>
    <div class="card">
        <div class="card-header">
            {{ trans('scheduler.suppliers.title_singular') }}
        </div>

        <div class="card-body">
            <div class="row">

                <div class="col-md-4">
                    <div class="form-row">
                        <div class="col-12 show-group">
                            <span>{{ trans('scheduler.suppliers.fields.wms_name') }}</span>
                            <span>{{$supplier->wms_name}}</span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-12 show-group">
                            <span>{{ trans('scheduler.suppliers.fields.wms_id') }}</span>
                            <span>{{$supplier->wms_id}}</span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-12 show-group">
                            <span>{{ trans('scheduler.suppliers.fields.phone') }}</span>
                            <span>{{$supplier->phone}}</span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-12 show-group">
                            <span>{{ trans('scheduler.suppliers.fields.email') }}</span>
                            <span>{{$supplier->email}}</span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-12 show-group">
                            <span>{{ trans('scheduler.suppliers.fields.aux4') }}</span>
                            <span>{{$supplier->aux4}}</span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-12 show-group">
                            <span>{{ trans('scheduler.suppliers.fields.aux1') }}</span>
                            <span>{{$supplier->aux1}}</span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-12 show-group">
                            <span>{{ trans('scheduler.suppliers.fields.aux2') }}</span>
                            <span>{{$supplier->aux2}}</span>
                        </div>
                    </div>
                    <!-- Datos voluntario -->

                </div>
                    <!-- Timeline & Estado voluntario -->

                <div class="col-md-8">
                    <h3 class="text-center">Estado: {{ $supplier->is_intervened == 0 ? 'NO INTERVENIDO' : 'INTERVENIDO' }}/ {{ $supplier->status  }}</h3>



                    <div id="app" class="container">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="swiper-container">
                                    <!-- Add Arrows -->
                                    <div class="swiper-button-next"></div>
                                    <div class="swiper-button-prev"></div>

                                    <div class="swiper-wrapper timeline">
                                        @foreach($appointmentsSuppliers as $appointmentsSupplier)
                                            <div class="swiper-slide" style="width: 2px !important">
                                                <div class="timestamp">
                                                    <span class="t-date">{{ $appointmentsSupplier->dateAll }}</span>
                                                </div>
                                                <div class="status" >
                                                    <span class="ballRange">
                                                        <span class="visit">{{ $appointmentsSupplier->textResult == '1. Vigilancia.' ? 'Intervenido' : $appointmentsSupplier->textResult  }}</span>

                                                        <span class="visit-status">{{ $appointmentsSupplier->action == '' ? '':$appointmentsSupplier->action  }}</span>
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach

                                    </div>
                                    <!-- End timeline -->
                                    <div class="swiper-pagination"></div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>

            </div>

        </div>
    </div>
@endsection
@section('scripts')
{{--    <script src="{{ asset('js/util.js') }}"></script>--}}
    <script src="{{ asset('https://unpkg.com/swiper/swiper-bundle.js') }}"></script>
    <script src="{{ asset('https://unpkg.com/swiper/swiper-bundle.min.js') }}"></script>
<script>
    var swiper = new Swiper('.swiper-container', {
        //pagination: '.swiper-pagination',
        slidesPerView: 4,
        paginationClickable: true,
        grabCursor: true,
        nextButton: '.next-slide',
        prevButton: '.prev-slide',
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });



</script>
@endsection