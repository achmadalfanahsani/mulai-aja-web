{{--
    partials/head.blade.php
    Berisi semua tag <meta>, <link> stylesheet, favicon, dan script inisialisasi tema.
--}}

<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>
    @hasSection('title')
        @yield('title')
    @else
        {{ $title ?? config('app.name', 'HelloWorld') }}
    @endif
</title>

<meta name="description" content="@yield('meta-description', 'Aplikasi berbasis Codebase Bootstrap 5')">
<meta name="author" content="achmadalfanahsani">
<meta name="robots" content="index, follow">

{{-- Open Graph Meta --}}
<meta property="og:title" content="@yield('title', config('app.name'))">
<meta property="og:site_name" content="{{ config('app.name') }}">
<meta property="og:description" content="@yield('meta-description', '')">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="@yield('og-image', '')">

{{-- Favicon & Icons --}}
<link rel="shortcut icon" href="{{ asset('assets/media/favicons/favicon.png') }}">
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/media/favicons/favicon-192x192.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/media/favicons/apple-touch-icon-180x180.png') }}">

{{-- Codebase Core CSS --}}
<link rel="stylesheet" id="css-main" href="{{ asset('assets/css/codebase.min.css') }}">

{{-- Theme CSS (untuk mencegah flash, langsung diload jika ada) --}}
@auth
    @if(auth()->user()->theme_color && auth()->user()->theme_color !== 'default')
        <link rel="stylesheet" id="css-theme" href="{{ auth()->user()->theme_color }}">
    @endif
@endauth

{{-- Vite CSS & JS --}}
@vite(['resources/css/app.css', 'resources/js/app.js'])

{{-- Theme CSS (opsional, diisi dari halaman jika diperlukan) --}}
@stack('styles')

{{-- Script pemilih tema (blocking, untuk mencegah flash dark mode) --}}
<script>
    (function() {
        const mode = {!! json_encode(auth()->check() ? auth()->user()->theme_mode : 'system', JSON_UNESCAPED_SLASHES) !!};
        const lHtml = document.documentElement;
        
        if (mode === "on") {
            lHtml.classList.add("dark");
        } else if (mode === "off") {
            lHtml.classList.remove("dark");
        } else if (mode === "system") {
            if (window.matchMedia("(prefers-color-scheme: dark)").matches) {
                lHtml.classList.add("dark");
            } else {
                lHtml.classList.remove("dark");
            }
        }
    })();
</script>