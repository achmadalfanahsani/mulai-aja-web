{{--
    partials/header/header-user-dropdown.blade.php
    Dropdown profil pengguna yang sedang login.
    Data user diambil dari Auth::user().
--}}

<div class="dropdown d-inline-block">
    <button type="button"
        class="btn btn-sm btn-alt-secondary"
        id="page-header-user-dropdown"
        data-bs-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false">
        <i class="fa fa-user d-sm-none"></i>
        <span class="d-none d-sm-inline-block fw-semibold">
            {{ auth()->check() ? auth()->user()->name : 'Guest' }}
        </span>
        <i class="fa fa-angle-down opacity-50 ms-1"></i>
    </button>

    <div class="dropdown-menu dropdown-menu-md dropdown-menu-end p-0"
        aria-labelledby="page-header-user-dropdown">

        {{-- Info Pengguna --}}
        <div class="px-2 py-3 bg-body-light rounded-top">
            <h5 class="h6 text-center mb-0">
                {{ auth()->check() ? auth()->user()->name : 'Guest' }}
            </h5>
        </div>

        {{-- Menu Dropdown --}}
        <div class="p-2">
            <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1"
                {{-- href="{{ route('profile.show') }}"> --}}
                href="#">
                <span>Profile</span>
                <i class="fa fa-fw fa-user opacity-25"></i>
            </a>
            <a class="dropdown-item d-flex align-items-center justify-content-between"
                {{-- href="{{ route('inbox.index') }}"> --}}
                href="#">
                <span>Inbox</span>
                <i class="fa fa-fw fa-envelope-open opacity-25"></i>
            </a>
            <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1"
            {{-- href="{{ route('invoices.index') }}"> --}}
            href="#">
                <span>Invoices</span>
                <i class="fa fa-fw fa-file opacity-25"></i>
            </a>

            <div class="dropdown-divider"></div>

            {{-- Toggle Side Overlay (Settings) --}}
            <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1"
                href="javascript:void(0)"
                data-toggle="layout"
                data-action="side_overlay_toggle">
                <span>Settings</span>
                <i class="fa fa-fw fa-wrench opacity-25"></i>
            </a>

            <div class="dropdown-divider"></div>

            {{-- Sign Out --}}
            <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1"
                {{-- href="{{ route('logout') }}" --}}
                href="#"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <span>Sign Out</span>
                <i class="fa fa-fw fa-sign-out-alt opacity-25"></i>
            </a>

            {{-- Form tersembunyi untuk POST logout --}}
            {{-- <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none"> --}}
            <form id="logout-form" action="#" method="POST" class="d-none">
                @csrf
            </form>
        </div>
        {{-- END Menu Dropdown --}}

    </div>
</div>