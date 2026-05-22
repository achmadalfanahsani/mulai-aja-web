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

    @if(Auth::user()->isSuperuser())
        {{-- Superuser Stats - Row 1 (Priority) --}}
        <div class="row items-push">
            <div class="col-6 col-lg-6">
                <a class="block block-rounded block-link-shadow h-100 mb-0" href="{{ route('superuser.users.index') }}">
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
            <div class="col-6 col-lg-6">
                <a class="block block-rounded block-link-shadow h-100 mb-0" href="{{ route('superuser.users.index', ['role' => 'administrator', 'status' => 'pending']) }}">
                    <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                        <div>
                            <div class="font-size-h2 font-w700 text-warning">{{ $stats['pending_approvals'] }}</div>
                            <div class="font-size-sm font-w600 text-uppercase text-muted">Pending Admin</div>
                        </div>
                        <div class="item item-rounded bg-warning-light">
                            <i class="fa fa-user-clock text-warning"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Superuser Stats - Row 2 (Details) --}}
        <div class="row items-push">
            <div class="col-4">
                <div class="block block-rounded h-100 mb-0">
                    <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                        <div>
                            <div class="font-size-h2 font-w700 text-warning">{{ $stats['total_administrators'] }}</div>
                            <div class="font-size-sm font-w600 text-uppercase text-muted">Administrator</div>
                        </div>
                        <div class="item item-rounded bg-warning-light">
                            <i class="fa fa-user-shield text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="block block-rounded h-100 mb-0">
                    <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                        <div>
                            <div class="font-size-h2 font-w700 text-info">{{ $stats['total_teachers'] }}</div>
                            <div class="font-size-sm font-w600 text-uppercase text-muted">Guru</div>
                        </div>
                        <div class="item item-rounded bg-info-light">
                            <i class="fa fa-user-tie text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="block block-rounded h-100 mb-0">
                    <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                        <div>
                            <div class="font-size-h2 font-w700 text-success">{{ $stats['total_students'] }}</div>
                            <div class="font-size-sm font-w600 text-uppercase text-muted">Siswa</div>
                        </div>
                        <div class="item item-rounded bg-success-light">
                            <i class="fa fa-graduation-cap text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif(Auth::user()->isAdministrator() || Auth::user()->isTeacher())
        {{-- Admin/Teacher Stats --}}
        <div class="row items-push">
            <div class="col-6">
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
            <div class="col-6">
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
            <div class="col-6 col-md-4">
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
            <div class="col-6 col-md-4">
                <div class="block block-rounded h-100 mb-0">
                    <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                        <div>
                            <div class="font-size-h2 font-w700 text-success">{{ $stats['completed_exams'] }}</div>
                            <div class="font-size-sm font-w600 text-uppercase text-muted">Selesai</div>
                        </div>
                        <div class="item item-rounded bg-success-light">
                            <i class="fa fa-check-circle text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
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
    @endif

    {{-- Quick Actions --}}
    <h2 class="content-heading">Aksi Cepat</h2>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3 items-push">
        @if(Auth::user()->isSuperuser())
            <div class="col">
                <a class="block block-rounded block-link-shadow h-100 mb-0" href="{{ route('superuser.users.index') }}">
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
        
        @if(Auth::user()->isAdministrator() || Auth::user()->isTeacher() || Auth::user()->isSuperuser())
            <div class="col">
                <a class="block block-rounded block-link-shadow h-100 mb-0" href="{{ route('question-packages.index') }}">
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
                <a class="block block-rounded block-link-shadow h-100 mb-0" href="{{ route('question-packages.create') }}">
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

        @if(Auth::user()->isStudent() || Auth::user()->isAdministrator() || Auth::user()->isSuperuser())
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
@endsection
