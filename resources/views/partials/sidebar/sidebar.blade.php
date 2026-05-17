{{--
    partials/sidebar/sidebar.blade.php
    Komponen navigasi sidebar utama.
    Gunakan @include('partials.sidebar.sidebar') di layout.
--}}

<nav id="sidebar">
    <div class="sidebar-content">

        {{-- Logo & Tombol Tutup Sidebar --}}
        @include('partials.sidebar.sidebar-header')

        {{-- Navigasi Menu --}}
        <div class="js-sidebar-scroll">
            <div class="content-side content-side-full">
                @include('partials.sidebar.sidebar-nav')
            </div>
        </div>
        {{-- END Sidebar Scrolling --}}

    </div>
</nav>