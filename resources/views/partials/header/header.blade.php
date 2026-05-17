{{--
    partials/header/header.blade.php
    Komponen header halaman utama.
    Terdiri dari: toolbar kanan/kiri, search overlay, dan loader overlay.
--}}

<header id="page-header">

    {{-- Konten utama header (toolbar) --}}
    <div class="content-header">

        {{-- Sisi Kiri Header --}}
        <div class="space-x-1">
            {{-- Tombol buka sidebar (mobile) --}}
            <button type="button"
                class="btn btn-sm btn-alt-secondary d-lg-none"
                data-toggle="layout"
                data-action="sidebar_open">
                <i class="fa fa-fw fa-bars"></i>
            </button>
        </div>
        {{-- END Sisi Kiri --}}

        {{-- Sisi Kanan Header --}}
        <div class="space-x-1">
            @include('partials.header.header-color-themes')
            @include('partials.header.header-user-dropdown')
        </div>
        {{-- END Sisi Kanan --}}

    </div>
    {{-- END Konten Header --}}

    {{-- Search Overlay --}}
    @include('partials.header.header-search')

    {{-- Loader Overlay --}}
    @include('partials.header.header-loader')

</header>