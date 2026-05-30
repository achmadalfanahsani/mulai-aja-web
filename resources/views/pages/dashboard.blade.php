@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    {{-- Welcome Banner --}}
    <div class="row items-push">
        <div class="col-md-12">
            <div class="block block-rounded bg-gd-primary">
                <div class="block-content block-content-full d-flex align-items-center justify-content-between p-4 p-md-5">
                    <div class="me-3">
                        <h2 class="h1 font-w800 text-white mb-2">
                            Selamat Datang, {{ Auth::user()->name }}!
                        </h2>
                        <p class="font-size-lg text-white-75 mb-0 d-none d-sm-block">
                            Platform Ujian Online Berbasis Komputer Modern & Terintegrasi.
                        </p>
                        <div class="mt-3">
                            <span class="badge bg-white-25 text-white py-2 px-3">
                                <i class="fa fa-user-shield me-1"></i> Role: {{ ucfirst(Auth::user()->role) }}
                            </span>
                        </div>
                    </div>
                    <div class="d-none d-md-block">
                        <i class="fa fa-chart-line fa-5x text-white-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Role-Based Statistics --}}
    <h2 class="content-heading d-flex justify-content-between align-items-center">
        <span>Statistik Overview</span>
    </h2>

    @if (Auth::user()->isSuperuser() || Auth::user()->isAdministrator())
        {{-- Management Stats - Row 1 --}}
        <div class="row items-push">
            {{-- 1. Total Paket Soal --}}
            <div class="col-6 col-lg-4">
                <a class="block block-rounded block-link-shadow h-100 mb-0" href="{{ route('question-packages.index') }}">
                    <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                        <div>
                            <div class="font-size-h2 font-w700 text-elegance">{{ $stats['total_packages'] }}</div>
                            <div class="font-size-sm font-w600 text-uppercase text-muted">Total Paket</div>
                        </div>
                        <div class="item item-rounded bg-elegance-lighter">
                            <i class="fa fa-briefcase text-elegance"></i>
                        </div>
                    </div>
                </a>
            </div>
            {{-- 2. Total Kelas --}}
            <div class="col-6 col-lg-4">
                <a class="block block-rounded block-link-shadow h-100 mb-0" href="{{ route('classrooms.index') }}">
                    <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                        <div>
                            <div class="font-size-h2 font-w700 text-earth">{{ $stats['total_classrooms'] }}</div>
                            <div class="font-size-sm font-w600 text-uppercase text-muted">Total Kelas</div>
                        </div>
                        <div class="item item-rounded bg-earth-lighter">
                            <i class="fa fa-school text-earth"></i>
                        </div>
                    </div>
                </a>
            </div>
            {{-- 3. Total User --}}
            <div class="col-6 col-lg-4">
                <a class="block block-rounded block-link-shadow h-100 mb-0"
                    href="{{ Auth::user()->isSuperuser() ? route('superuser.users.index') : route('admin.users.index') }}">
                    <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                        <div>
                            <div class="font-size-h2 font-w700 text-primary">{{ $stats['total_users'] }}</div>
                            <div class="font-size-sm font-w600 text-uppercase text-muted">Total User</div>
                        </div>
                        <div class="item item-rounded bg-primary-lighter">
                            <i class="fa fa-users text-primary"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Management Stats - Row 2 --}}
        <div class="row items-push">
            @if (Auth::user()->isSuperuser())
                {{-- 3.5 Total Admin --}}
                <div class="col-6 col-lg-4">
                    <a class="block block-rounded block-link-shadow h-100 mb-0"
                        href="{{ route('superuser.users.index', ['role' => 'administrator']) }}">
                        <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                            <div>
                                <div class="font-size-h2 font-w700 text-secondary">{{ $stats['total_administrators'] }}
                                </div>
                                <div class="font-size-sm font-w600 text-uppercase text-muted">Total Admin</div>
                            </div>
                            <div class="item item-rounded bg-secondary-lighter">
                                <i class="fa fa-user-shield text-secondary"></i>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
            {{-- 4. Total Guru --}}
            <div class="col-6 col-lg-4">
                <a class="block block-rounded block-link-shadow h-100 mb-0"
                    href="{{ Auth::user()->isSuperuser() ? route('superuser.users.index', ['role' => 'teacher']) : route('admin.users.index', ['role' => 'teacher']) }}">
                    <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                        <div>
                            <div class="font-size-h2 font-w700 text-info">{{ $stats['total_teachers'] }}</div>
                            <div class="font-size-sm font-w600 text-uppercase text-muted">Total Guru</div>
                        </div>
                        <div class="item item-rounded bg-info-light">
                            <i class="fa fa-user-tie text-info"></i>
                        </div>
                    </div>
                </a>
            </div>
            {{-- 5. Total Siswa --}}
            <div class="col-6 col-lg-4">
                <a class="block block-rounded block-link-shadow h-100 mb-0"
                    href="{{ Auth::user()->isSuperuser() ? route('superuser.users.index', ['role' => 'student']) : route('admin.users.index', ['role' => 'student']) }}">
                    <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                        <div>
                            <div class="font-size-h2 font-w700 text-success">{{ $stats['total_students'] }}</div>
                            <div class="font-size-sm font-w600 text-uppercase text-muted">Total Siswa</div>
                        </div>
                        <div class="item item-rounded bg-success-lighter">
                            <i class="fa fa-graduation-cap text-success"></i>
                        </div>
                    </div>
                </a>
            </div>
            @if (Auth::user()->isAdministrator())
                {{-- 6. Menunggu Approval --}}
                <div class="col-6 col-lg-4">
                    <a class="block block-rounded block-link-shadow h-100 mb-0"
                        href="{{ route('admin.users.index', ['status' => 'pending']) }}">
                        <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                            <div>
                                <div class="font-size-h2 font-w700 text-warning">{{ $stats['pending_approvals'] }}</div>
                                <div class="font-size-sm font-w600 text-uppercase text-muted">Menunggu Approval</div>
                            </div>
                            <div class="item item-rounded bg-warning-light">
                                <i class="fa fa-user-clock text-warning"></i>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
        </div>
        @if (Auth::user()->isSuperuser())
            <div class="row items-push">
                {{-- 6. Menunggu Approval --}}
                <div class="col-6 col-lg-4">
                    <a class="block block-rounded block-link-shadow h-100 mb-0"
                        href="{{ route('superuser.users.index', ['status' => 'pending']) }}">
                        <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                            <div>
                                <div class="font-size-h2 font-w700 text-warning">{{ $stats['pending_approvals'] }}</div>
                                <div class="font-size-sm font-w600 text-uppercase text-muted">Menunggu Approval</div>
                            </div>
                            <div class="item item-rounded bg-warning-light">
                                <i class="fa fa-user-clock text-warning"></i>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endif
    @elseif(Auth::user()->isTeacher())
        {{-- Teacher Stats --}}
        <div class="row items-push">
            <div class="col-6 col-md-4">
                <a class="block block-rounded block-link-shadow h-100 mb-0" href="{{ route('question-packages.index') }}">
                    <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                        <div>
                            <div class="font-size-h2 font-w700 text-primary">{{ $stats['total_packages'] }}</div>
                            <div class="font-size-sm font-w600 text-uppercase text-muted">Paket Soal</div>
                        </div>
                        <div class="item item-rounded bg-primary-lighter">
                            <i class="fa fa-briefcase text-primary"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-4">
                <div class="block block-rounded h-100 mb-0">
                    <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                        <div>
                            <div class="font-size-h2 font-w700 text-earth">{{ $stats['total_classrooms'] }}</div>
                            <div class="font-size-sm font-w600 text-uppercase text-muted">Kelas Dikelola</div>
                        </div>
                        <div class="item item-rounded bg-earth-lighter">
                            <i class="fa fa-school text-earth"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="block block-rounded h-100 mb-0">
                    <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                        <div>
                            <div class="font-size-h2 font-w700 text-success">{{ $stats['total_attempts'] }}</div>
                            <div class="font-size-sm font-w600 text-uppercase text-muted">Total Pengerjaan</div>
                        </div>
                        <div class="item item-rounded bg-success-light">
                            <i class="fa fa-chart-bar text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif(Auth::user()->isStudent())
        {{-- Student Stats --}}
        <div class="row items-push">
            <div class="col-6 col-md-3">
                <a class="block block-rounded block-link-shadow h-100 mb-0" href="{{ route('exams.index') }}">
                    <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                        <div>
                            <div class="font-size-h2 font-w700 text-primary">{{ $stats['total_attempts'] }}</div>
                            <div class="font-size-sm font-w600 text-uppercase text-muted">Total Ujian</div>
                        </div>
                        <div class="item item-rounded bg-primary-lighter">
                            <i class="fa fa-pencil-alt text-primary"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <div class="block block-rounded h-100 mb-0">
                    <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                        <div>
                            <div class="font-size-h2 font-w700 text-success">
                                @php
                                    $totalSeconds = $stats['total_time_spent'];
                                    $hours = floor($totalSeconds / 3600);
                                    $minutes = floor(($totalSeconds % 3600) / 60);
                                @endphp
                                {{ $hours > 0 ? $hours . ' j ' : '' }}{{ $minutes }} m
                            </div>
                            <div class="font-size-sm font-w600 text-uppercase text-muted">Total Waktu</div>
                        </div>
                        <div class="item item-rounded bg-success-light">
                            <i class="fa fa-clock text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="block block-rounded h-100 mb-0">
                    <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                        <div>
                            <div class="font-size-h2 font-w700 text-info">{{ $stats['available_exams'] }}</div>
                            <div class="font-size-sm font-w600 text-uppercase text-muted">Ujian Tersedia</div>
                        </div>
                        <div class="item item-rounded bg-info-light">
                            <i class="fa fa-calendar-alt text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="block block-rounded h-100 mb-0">
                    <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                        <div>
                            <div class="font-size-h2 font-w700 text-warning">{{ round($stats['average_score'], 1) }}</div>
                            <div class="font-size-sm font-w600 text-uppercase text-muted">Rata-rata Skor</div>
                        </div>
                        <div class="item item-rounded bg-warning-light">
                            <i class="fa fa-star text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row items-push">
            <div class="col-6 col-md-6">
                <div class="block block-rounded h-100 mb-0">
                    <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                        <div>
                            <div class="font-size-h2 font-w700 text-pulse">{{ $stats['highest_score'] }}</div>
                            <div class="font-size-sm font-w600 text-uppercase text-muted">Skor Tertinggi</div>
                        </div>
                        <div class="item item-rounded bg-pulse-lighter">
                            <i class="fa fa-trophy text-pulse"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-6">
                <div class="block block-rounded h-100 mb-0">
                    <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                        <div>
                            <div class="font-size-h2 font-w700 text-flat">{{ $stats['lowest_score'] }}</div>
                            <div class="font-size-sm font-w600 text-uppercase text-muted">Nilai Terendah</div>
                        </div>
                        <div class="item item-rounded bg-flat-lighter">
                            <i class="fa fa-arrow-down text-flat"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Quick Actions --}}
    <h2 class="content-heading">Aksi Cepat</h2>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3 items-push">
        @if (Auth::user()->isSuperuser() || Auth::user()->isAdministrator())
            <div class="col">
                <a class="block block-rounded block-link-shadow h-100 mb-0"
                    href="{{ Auth::user()->isSuperuser() ? route('superuser.users.index') : route('admin.users.index') }}">
                    <div class="block-content block-content-full text-center">
                        <div class="item item-circle bg-primary-lighter mx-auto mb-3">
                            <i class="fa fa-users-cog text-primary"></i>
                        </div>
                        <div class="font-w600 mb-1">Kelola User</div>
                        <div class="font-size-sm text-muted">Manajemen pengguna</div>
                    </div>
                </a>
            </div>
        @endif

        @if (Auth::user()->isAdministrator() || Auth::user()->isTeacher() || Auth::user()->isSuperuser())
            <div class="col">
                <a class="block block-rounded block-link-shadow h-100 mb-0"
                    href="{{ route('question-packages.index') }}">
                    <div class="block-content block-content-full text-center">
                        <div class="item item-circle bg-warning-light mx-auto mb-3">
                            <i class="fa fa-folder-open text-warning"></i>
                        </div>
                        <div class="font-w600 mb-1">Paket Soal</div>
                        <div class="font-size-sm text-muted">Kelola paket ujian</div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a class="block block-rounded block-link-shadow h-100 mb-0"
                    href="{{ route('question-packages.create') }}">
                    <div class="block-content block-content-full text-center">
                        <div class="item item-circle bg-success-light mx-auto mb-3">
                            <i class="fa fa-plus-circle text-success"></i>
                        </div>
                        <div class="font-w600 mb-1">Buat Paket Baru</div>
                        <div class="font-size-sm text-muted">Tambah ujian baru</div>
                    </div>
                </a>
            </div>
        @endif

        @if (Auth::user()->isStudent() || Auth::user()->isSuperuser())
            <div class="col">
                <a class="block block-rounded block-link-shadow h-100 mb-0" href="{{ route('exams.index') }}">
                    <div class="block-content block-content-full text-center">
                        <div class="item item-circle bg-info-light mx-auto mb-3">
                            <i class="fa fa-pen-nib text-info"></i>
                        </div>
                        <div class="font-w600 mb-1">Daftar Ujian</div>
                        <div class="font-size-sm text-muted">Akses ujian tersedia</div>
                    </div>
                </a>
            </div>
        @endif
    </div>

    {{-- Recent Activity --}}
    <div class="row">
        <div class="col-md-12">
            <h2 class="content-heading">Aktivitas Terbaru</h2>
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Percobaan Ujian Terakhir</h3>
                </div>
                <div class="block-content">
                    @if ($recent_attempts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-borderless table-striped table-vcenter">
                                <thead>
                                    <tr>
                                        @if (!Auth::user()->isStudent())
                                            <th>Siswa</th>
                                        @endif
                                        <th>Paket Soal</th>
                                        <th>Tanggal</th>
                                        <th class="text-center">Skor</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recent_attempts as $attempt)
                                        <tr>
                                            @if (!Auth::user()->isStudent())
                                                <td class="font-w600">{{ $attempt->user->name }}</td>
                                            @endif
                                            <td>{{ $attempt->questionPackage->name }}</td>
                                            <td>{{ $attempt->started_at->format('d M Y, H:i') }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="font-w700 {{ $attempt->total_score >= ($attempt->questionPackage->passing_score ?? 0) ? 'text-success' : 'text-danger' }}">
                                                    {{ $attempt->total_score ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if ($attempt->is_completed)
                                                    <span class="badge bg-success">Selesai</span>
                                                @else
                                                    <span class="badge bg-warning">Berjalan</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">Belum ada aktivitas pengerjaan ujian.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
