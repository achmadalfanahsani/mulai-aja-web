{{--
    partials/sidebar/sidebar-header.blade.php
    Berisi logo aplikasi dan tombol tutup sidebar (khusus mobile).
--}}

<div class="content-header justify-content-lg-center">

    {{-- Logo --}}
    <div>
        {{-- Versi mini (tampil saat sidebar diciutkan) --}}
        <span class="smini-visible fw-bold tracking-wide fs-lg">
            M<span class="text-primary">A</span>
        </span>

        {{-- Versi penuh --}}
        <a class="link-fx fw-bold tracking-wide mx-auto" href="{{ route('dashboard') }}">
            <span class="smini-hidden">
                <span class="fs-4 text-dual">Mulai</span><span class="fs-4 text-primary">Aja</span>
            </span>
        </a>
    </div>
    {{-- END Logo --}}

    {{-- Tombol Tutup Sidebar (hanya tampil di mobile) --}}
    <div>
        <button type="button"
            class="btn btn-sm btn-alt-danger d-lg-none"
            data-toggle="layout"
            data-action="sidebar_close">
            <i class="fa fa-fw fa-times"></i>
        </button>
    </div>
    {{-- END Options --}}

</div>