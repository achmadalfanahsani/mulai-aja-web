<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="remember-theme">

<head>
    @include('partials.head')
</head>

<body>
    {{-- Page Container --}}
    <div id="page-container" class="{{ $pageContainerClass ?? 'sidebar-o sidebar-mini enable-page-overlay side-scroll page-header-modern main-content-narrow' }}">

        {{-- Sidebar --}}
        @include('partials.sidebar.sidebar')
        {{-- END Sidebar --}}

        {{-- Header --}}
        @include('partials.header.header')
        {{-- END Header --}}

        {{-- Main Container --}}
        <main id="main-container">
            <div class="content">
                {{-- Page Heading --}}
                @hasSection('page-heading')
                    <h2 class="content-heading">@yield('page-heading')</h2>
                @endif

                {{-- Page Content --}}
                @yield('content')
            </div>
        </main>
        {{-- END Main Container --}}

        {{-- Footer --}}
        @include('partials.footer')
        {{-- END Footer --}}

    </div>
    {{-- END Page Container --}}

    {{-- Core Scripts --}}
    <script src="{{ asset('assets/js/codebase.app.min.js') }}"></script>

    {{-- Additional page scripts --}}
    @stack('scripts')
</body>

</html>