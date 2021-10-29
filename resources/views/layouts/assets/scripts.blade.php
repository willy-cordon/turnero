@stack('before-scripts')
{!! script(mix('js/app.js')) !!}
@stack('after-scripts')
@yield('scripts')
<script src="{{ asset('js/custom.js') }}"></script>