<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">

    <title>@yield('title') - MulaiAja</title>

    <meta name="description" content="MulaiAja - Platform CBT Online">
    <meta name="author" content="pixelcave">
    <meta name="robots" content="index, follow">

    <!-- Icons -->
    <link rel="shortcut icon" href="{{ asset('assets/media/favicons/favicon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/media/favicons/favicon-192x192.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/media/favicons/apple-touch-icon-180x180.png') }}">

    <!-- Stylesheets -->
    <link rel="stylesheet" id="css-main" href="{{ asset('assets/css/codebase.min.css') }}">
    @stack('css')
</head>
<body>
    <div id="page-container" class="main-content-boxed">
        <main id="main-container">
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/codebase.app.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/jquery.min.js') }}"></script>
    @stack('js')
</body>
</html>
