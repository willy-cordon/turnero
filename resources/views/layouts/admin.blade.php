<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" href="{{ asset('/img/favicon.png') }}" type="image/x-icon"/>
        @include('layouts.assets.styles')
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-174449749-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'UA-174449749-1');
        </script>
    </head>
    <body class="app header-fixed sidebar-fixed aside-menu-fixed pace-done sidebar-lg-show">
       @include('partials.header')
        <div class="app-body">
            @include('partials.menu')
            <main class="main">
                @include('partials.errors')
                <div style="padding-top: 20px" class="container-fluid">
                    @yield('content')
                </div>
            </main>
        </div>
       @include('layouts.assets.scripts')
    </body>


    <script>
        jQuery(function (){
            $.ajax({
                url:"{{ route('scheduler.activity-instances.get-counters') }}",
                type:"GET",
                data:{
                    "_token": "{{ csrf_token() }}"
                },
                success:function(response){
                    jQuery('#ed-all').html(response.countEDiaryAll);
                    jQuery('#ed-pending').html(response.countEDiaryPending);
                    jQuery('#ed-expired').html(response.countEDiaryExpired);
                    jQuery('#ed-in-progress').html(response.countEDiaryInProgress);

                    jQuery('#vi-all').html(response.countVigilanciaAll);
                    jQuery('#vi-in-progress').html(response.countVigilanciaInProgress);

                    jQuery('#tu-all').html(response.countTurnosAll);
                    jQuery('#tu-pending').html(response.countTurnosPending);
                    jQuery('#tu-in-progress').html(response.countTurnosInProgress);
                    jQuery('#tu-expired').html(response.countTurnosExpired);



                    // console.log(response);
                },
                error :function( data ) {

                }
            });

            $.ajax({
                url:"{{ route('scheduler.activity-instances.update-activities-eDiary') }}",
                type:"GET",
                success:function(response){

                    // console.log(response);

                },
                error :function( data ) {

                }
            });

        })
    </script>
</html>




