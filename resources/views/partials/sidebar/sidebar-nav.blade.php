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

    @auth
        {{-- Management Section --}}
        @if(auth()->user()->isTeacher() || auth()->user()->isAdministrator() || auth()->user()->isSuperuser())
        <li class="nav-main-heading">Management</li>
        
        @if(auth()->user()->isAdministrator() || auth()->user()->isSuperuser())
        <li class="nav-main-item">
            <a class="nav-main-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                href="{{ route('admin.users.index') }}">
                <i class="nav-main-link-icon fa fa-users-cog"></i>
                <span class="nav-main-link-name">Manajemen User</span>
            </a>
        </li>
        @endif

        <li class="nav-main-item">
            <a class="nav-main-link {{ request()->routeIs('question-packages.*') || request()->routeIs('questions.*') ? 'active' : '' }}"
                href="{{ route('question-packages.index') }}">
                <i class="nav-main-link-icon fa fa-boxes"></i>
                <span class="nav-main-link-name">Manajemen Paket Soal</span>
            </a>
        </li>

        <li class="nav-main-item">
            <a class="nav-main-link {{ request()->routeIs('classrooms.*') ? 'active' : '' }}"
                href="{{ route('classrooms.index') }}">
                <i class="nav-main-link-icon fa fa-school"></i>
                <span class="nav-main-link-name">Manajemen Kelas</span>
            </a>
        </li>
        @endif

        {{-- Heading: CBT & Ujian --}}
        <li class="nav-main-heading">CBT & Ujian</li>
        
        {{-- Exam Taking: Student, Administrator, Superuser --}}
        @if(auth()->user()->isStudent() || auth()->user()->isAdministrator() || auth()->user()->isSuperuser())
        <li class="nav-main-item">
            <a class="nav-main-link {{ request()->routeIs('exams.*') ? 'active' : '' }}"
                href="{{ route('exams.index') }}">
                <i class="nav-main-link-icon fa fa-pen-nib"></i>
                <span class="nav-main-link-name">Mulai Ujian</span>
            </a>
        </li>
        @endif
        
        <li class="nav-main-item mt-4">
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
                <span class="nav-main-link-name">Masuk</span>
            </a>
        </li>
        <li class="nav-main-item">
            <a class="nav-main-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">
                <i class="nav-main-link-icon fa fa-user-plus"></i>
                <span class="nav-main-link-name">Daftar Akun</span>
            </a>
        </li>
    @endauth
</ul>
