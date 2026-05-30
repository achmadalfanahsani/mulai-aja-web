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
            // We exclude alerts with 'alert-permanent' class
            const alerts = document.querySelectorAll('.alert-success:not(.alert-permanent), .alert-info:not(.alert-permanent), .alert-danger:not(.alert-permanent)');
            
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

    @auth
    {{-- Theme Sync Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Handle Color Theme change
            document.querySelectorAll('[data-toggle="theme"]').forEach(el => {
                el.addEventListener('click', () => {
                    const theme = el.getAttribute('data-theme');
                    updateTheme({ theme_color: theme });
                });
            });

            // Handle Dark Mode change
            document.querySelectorAll('[data-toggle="layout"][data-dark-mode]').forEach(el => {
                el.addEventListener('click', () => {
                    const mode = el.getAttribute('data-dark-mode');
                    updateTheme({ theme_mode: mode });
                });
            });

            function updateTheme(data) {
                fetch("{{ route('profile.update-theme') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => console.log('Theme synced:', data))
                .catch(error => console.error('Error syncing theme:', error));
            }
        });
    </script>
    @endauth
</body>

</html>