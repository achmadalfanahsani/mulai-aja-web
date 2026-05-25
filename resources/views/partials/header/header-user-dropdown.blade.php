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
        <i class="fa fa-user-circle d-sm-none"></i>
        <span class="d-none d-sm-inline-block fw-semibold">
            {{ auth()->check() ? auth()->user()->name : 'Guest' }}
        </span>
        <i class="fa fa-angle-down opacity-50 ms-1"></i>
    </button>

    <div class="dropdown-menu dropdown-menu-md dropdown-menu-end p-0"
        aria-labelledby="page-header-user-dropdown">

        {{-- Info Pengguna --}}
        <div class="px-2 py-3 bg-body-light rounded-top text-center">
            <h5 class="h6 mb-1">
                {{ auth()->check() ? auth()->user()->name : 'Guest' }}
            </h5>
            @auth
                <div class="mb-1">
                    @php
                        $roleColor = match(auth()->user()->role) {
                            'superuser' => 'danger',
                            'administrator' => 'warning',
                            'teacher' => 'info',
                            'student' => 'success',
                            default => 'secondary'
                        };
                    @endphp
                    <span class="badge bg-{{ $roleColor }}">
                        {{ ucfirst(auth()->user()->role) }}
                    </span>
                </div>
                <div class="fs-xs text-muted">
                    {{ auth()->user()->email }}
                </div>
            @endauth
        </div>

        {{-- Menu Dropdown --}}
        <div class="p-2">
            <a class="dropdown-item d-flex align-items-center justify-content-between"
                href="{{ route('dashboard') }}">
                <span>Dashboard</span>
                <i class="fa fa-fw fa-home opacity-25"></i>
            </a>
            
            @auth
                @if(auth()->user()->isSuperuser())
                <a class="dropdown-item d-flex align-items-center justify-content-between"
                    href="{{ route('superuser.users.index') }}">
                    <span>Manajemen User</span>
                    <i class="fa fa-fw fa-users-cog opacity-25"></i>
                </a>
                @endif
            @endauth

            <a class="dropdown-item d-flex align-items-center justify-content-between"
                href="javascript:void(0)">
                <span>Ganti Password</span>
                <i class="fa fa-fw fa-key opacity-25"></i>
            </a>

            <div class="dropdown-divider"></div>

            {{-- Sign Out --}}
            <a class="dropdown-item d-flex align-items-center justify-content-between"
                href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <span class="text-danger fw-semibold">Sign Out</span>
                <i class="fa fa-fw fa-sign-out-alt text-danger opacity-50"></i>
            </a>

            {{-- Form tersembunyi untuk POST logout --}}
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
        {{-- END Menu Dropdown --}}

    </div>
</div>
