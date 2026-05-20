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

    {{-- Heading: CBT & Ujian --}}
    <li class="nav-main-heading">CBT & Ujian</li>
    
    @auth
        @if(auth()->user()->isAdmin() || auth()->user()->isTeacher())
        <li class="nav-main-item">
            <a class="nav-main-link {{ request()->routeIs('question-packages.*') || request()->routeIs('questions.*') ? 'active' : '' }}"
                href="{{ route('question-packages.index') }}">
                <i class="nav-main-link-icon fa fa-folder-open"></i>
                <span class="nav-main-link-name">Kelola Paket Soal</span>
            </a>
        </li>
        @endif

        @if(auth()->user()->isAdmin() || auth()->user()->isStudent())
        <li class="nav-main-item">
            <a class="nav-main-link {{ request()->routeIs('exams.*') ? 'active' : '' }}"
                href="{{ route('exams.index') }}">
                <i class="nav-main-link-icon fa fa-pen-nib"></i>
                <span class="nav-main-link-name">Mulai Ujian</span>
            </a>
        </li>
        @endif
        
        <li class="nav-main-item">
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
            <a class="nav-main-link text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="nav-main-link-icon fa fa-sign-out-alt text-danger"></i>
                <span class="nav-main-link-name">Logout ({{ ucfirst(auth()->user()->role) }})</span>
            </a>
        </li>
    @else
        <li class="nav-main-item">
            <a class="nav-main-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">
                <i class="nav-main-link-icon fa fa-sign-in-alt"></i>
                <span class="nav-main-link-name">Mock Login</span>
            </a>
        </li>
    @endauth




    <li class="nav-main-item">
        <a class="nav-main-link {{ request()->routeIs('exams.history') ? 'active' : '' }}"
            href="{{ route('exams.history') }}">
            <i class="nav-main-link-icon fa fa-history"></i>
            <span class="nav-main-link-name">Riwayat Ujian</span>
        </a>
    </li>
</ul>