{{--
    partials/header/header-color-themes.blade.php
    Dropdown pemilih tema warna dan mode gelap/terang.
--}}

<div class="dropdown d-inline-block">
    <button type="button"
        class="btn btn-sm btn-alt-secondary"
        id="page-header-themes-dropdown"
        data-bs-toggle="dropdown"
        data-bs-auto-close="outside"
        aria-haspopup="true"
        aria-expanded="false">
        <i class="fa fa-fw fa-brush"></i>
    </button>

    <div class="dropdown-menu dropdown-menu-lg p-0" aria-labelledby="page-header-themes-dropdown">

        {{-- Judul --}}
        <div class="px-3 py-2 bg-body-light rounded-top">
            <h5 class="fs-sm text-center mb-0">Color Themes</h5>
        </div>

        {{-- Pilihan Tema Warna --}}
        <div class="p-3">
            <div class="row g-0 text-center">
                @php
                    $themes = [
                        'text-default'    => 'default',
                        'text-elegance'   => asset('assets/css/themes/elegance.min.css'),
                        'text-pulse'      => asset('assets/css/themes/pulse.min.css'),
                        'text-flat'       => asset('assets/css/themes/flat.min.css'),
                        'text-corporate'  => asset('assets/css/themes/corporate.min.css'),
                        'text-earth'      => asset('assets/css/themes/earth.min.css'),
                    ];
                @endphp

                @foreach($themes as $class => $theme)
                <div class="col-2">
                    <a class="{{ $class }}"
                        data-toggle="theme"
                        data-theme="{{ $theme }}"
                        href="javascript:void(0)">
                        <i class="fa fa-2x fa-circle"></i>
                    </a>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Judul Dark Mode --}}
        <div class="px-3 py-2 bg-body-light rounded-top">
            <h5 class="fs-sm text-center mb-0">Dark Mode</h5>
        </div>

        {{-- Pilihan Dark / Light / System --}}
        <div class="px-2 py-3">
            <div class="row g-1 text-center">
                @php
                    $darkModes = [
                        ['action' => 'dark_mode_off',    'mode' => 'off',    'icon' => 'far fa-sun',    'label' => 'Light'],
                        ['action' => 'dark_mode_on',     'mode' => 'on',     'icon' => 'fa fa-moon',    'label' => 'Dark'],
                        ['action' => 'dark_mode_system', 'mode' => 'system', 'icon' => 'fa fa-desktop', 'label' => 'System'],
                    ];
                @endphp

                @foreach($darkModes as $item)
                <div class="col-4">
                    <button type="button"
                        class="dropdown-item mb-0 d-flex align-items-center gap-2"
                        data-toggle="layout"
                        data-action="{{ $item['action'] }}"
                        data-dark-mode="{{ $item['mode'] }}">
                        <i class="{{ $item['icon'] }} fa-fw opacity-50"></i>
                        <span class="fs-sm fw-medium">{{ $item['label'] }}</span>
                    </button>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>