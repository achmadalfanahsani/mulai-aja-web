{{--
    partials/sidebar/sidebar-nav.blade.php
    Daftar item navigasi sidebar.
    Tambahkan atau ubah menu di sini sesuai kebutuhan aplikasi.
--}}

<ul class="nav-main">

    {{-- Dashboard --}}
    <li class="nav-main-item">
        <a class="nav-main-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
            href="{{ route('dashboard') }}">
            <i class="nav-main-link-icon fa fa-house-user"></i>
            <span class="nav-main-link-name">Dashboard</span>
        </a>
    </li>

    {{-- Heading: Build --}}
    <li class="nav-main-heading">Build</li>

    {{-- Main Menu (dengan sub-menu) --}}
    <li class="nav-main-item">
        <a class="nav-main-link nav-main-link-submenu"
            data-toggle="submenu"
            aria-haspopup="true"
            aria-expanded="false"
            href="#">
            <i class="nav-main-link-icon fa fa-puzzle-piece"></i>
            <span class="nav-main-link-name">Main Menu</span>
        </a>
        <ul class="nav-main-submenu">
            <li class="nav-main-item">
                <a class="nav-main-link" href="#">
                    <span class="nav-main-link-name">Link 1-1</span>
                </a>
            </li>
            <li class="nav-main-item">
                <a class="nav-main-link" href="#">
                    <span class="nav-main-link-name">Link 1-2</span>
                </a>
            </li>

            {{-- Sub Level 2 --}}
            <li class="nav-main-item">
                <a class="nav-main-link nav-main-link-submenu"
                    data-toggle="submenu"
                    aria-haspopup="true"
                    aria-expanded="false"
                    href="#">
                    <span class="nav-main-link-name">Sub Level 2</span>
                </a>
                <ul class="nav-main-submenu">
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="#">
                            <span class="nav-main-link-name">Link 2-1</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="#">
                            <span class="nav-main-link-name">Link 2-2</span>
                        </a>
                    </li>

                    {{-- Sub Level 3 --}}
                    <li class="nav-main-item">
                        <a class="nav-main-link nav-main-link-submenu"
                            data-toggle="submenu"
                            aria-haspopup="true"
                            aria-expanded="false"
                            href="#">
                            <span class="nav-main-link-name">Sub Level 3</span>
                        </a>
                        <ul class="nav-main-submenu">
                            <li class="nav-main-item">
                                <a class="nav-main-link" href="#">
                                    <span class="nav-main-link-name">Link 3-1</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link" href="#">
                                    <span class="nav-main-link-name">Link 3-2</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    {{-- END Sub Level 3 --}}

                </ul>
            </li>
            {{-- END Sub Level 2 --}}

        </ul>
    </li>
    {{-- END Main Menu --}}

    {{-- Heading: Pages --}}
    <li class="nav-main-heading">Pages</li>

    {{-- Error Pages --}}
    <li class="nav-main-item">
        <a class="nav-main-link nav-main-link-submenu"
            data-toggle="submenu"
            aria-haspopup="true"
            aria-expanded="false"
            href="#">
            <i class="nav-main-link-icon fa fa-flag"></i>
            <span class="nav-main-link-name">Error</span>
        </a>
        <ul class="nav-main-submenu">
            <li class="nav-main-item">
                {{-- <a class="nav-main-link" href="{{ route('error.all') }}"> --}}
                <a class="nav-main-link" href="#">
                    <span class="nav-main-link-name">All</span>
                </a>
            </li>
            @foreach([400, 401, 403, 404, 500, 503] as $code)
            <li class="nav-main-item">
                <a class="nav-main-link" href="{{ route("error.page." . $code) }}">
                {{-- <a class="nav-main-link" href="#"> --}}
                    <span class="nav-main-link-name">{{ $code }}</span>
                </a>
            </li>
            @endforeach
        </ul>
    </li>
    {{-- END Error Pages --}}

</ul>