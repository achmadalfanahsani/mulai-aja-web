<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="remember-theme">

<head>
    @include('partials.head')
</head>

<body>
    {{-- Page Container --}}
    <div id="page-container" class="{{ $pageContainerClass ?? 'sidebar-o sidebar-mini enable-page-overlay side-scroll page-header-modern page-header-fixed main-content-narrow' }}">

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

    {{-- Global Alert Auto-Close --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Find all alerts that should be auto-closed
            // We target alert-success and alert-info by default, but you can adjust as needed
            const alerts = document.querySelectorAll('.alert-success, .alert-info, .alert-danger:not(.alert-permanent)');
            
            alerts.forEach(function(alert) {
                // Set timeout to close the alert after 3 seconds (3000ms)
                setTimeout(function() {
                    // Check if Bootstrap's alert instance exists and use its close method
                    // or fallback to manual removal if using a simple CSS-based alert
                    if (window.bootstrap && bootstrap.Alert) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    } else {
                        // Fallback: trigger click on the close button if it exists
                        const closeBtn = alert.querySelector('.btn-close, [data-bs-dismiss="alert"]');
                        if (closeBtn) {
                            closeBtn.click();
                        } else {
                            // Last resort: just remove it or use a fade out
                            alert.style.transition = 'opacity 0.5s ease';
                            alert.style.opacity = '0';
                            setTimeout(() => alert.remove(), 500);
                        }
                    }
                }, 3000); // 3 seconds delay
            });
        });
    </script>

    {{-- Additional page scripts --}}
    @stack('scripts')
</body>

</html>