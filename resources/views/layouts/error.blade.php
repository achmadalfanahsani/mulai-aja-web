<!doctype html>
<html>

<head>
    @include('partials.head')
</head>

<body>
    @yield('content')

    {{-- Core Scripts --}}
    <script src="{{ asset('assets/js/codebase.app.min.js') }}"></script>
</body>

</html>
