{{--
    partials/header/header-search.blade.php
    Overlay pencarian yang muncul saat tombol search diklik.
--}}

<div id="page-header-search" class="overlay-header bg-body-extra-light">
    <div class="content-header">
        {{-- <form class="w-100" action="{{ route('search') }}" method="GET"> --}}
        <form class="w-100" action="#" method="GET">
            <div class="input-group">

                {{-- Tombol Tutup Pencarian --}}
                <button type="button"
                    class="btn btn-secondary"
                    data-toggle="layout"
                    data-action="header_search_off">
                    <i class="fa fa-fw fa-times"></i>
                </button>

                {{-- Input Pencarian --}}
                <input type="text"
                    class="form-control"
                    placeholder="Cari atau tekan ESC..."
                    id="page-header-search-input"
                    name="q">

                {{-- Tombol Submit --}}
                <button type="submit" class="btn btn-secondary">
                    <i class="fa fa-fw fa-search"></i>
                </button>

            </div>
        </form>
    </div>
</div>