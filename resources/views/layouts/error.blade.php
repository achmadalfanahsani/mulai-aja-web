<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="remember-theme">

<head>
    @include('partials.head')
</head>

<body>
    @yield('content')

    {{-- Core Scripts --}}
    <script src="{{ asset('assets/js/codebase.app.min.js') }}"></script>
</body>

</html>
