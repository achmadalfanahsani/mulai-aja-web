@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-heading', 'Dashboard')

@section('content')
    {{-- CBT Welcome Banner --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="block block-rounded block-themed">
                <div class="block-content block-content-full bg-gd-primary text-center p-5 text-white rounded">
                    <h2 class="h1 font-w800 mb-2">Selamat Datang di MulaiAja CBT</h2>
                    <p class="font-size-lg text-white-75 mb-0">Platform Ujian Online Berbasis Komputer Modern & Terintegrasi.</p>
                    <p class="font-size-md text-white-50 mt-2"><i class="fa fa-user-circle me-1"></i> Anda masuk sebagai: <strong>{{ Auth::user()->name }}</strong> ({{ ucfirst(Auth::user()->role) }})</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Role-Based Statistics --}}
    <div class="row">
        @if(Auth::user()->isSuperuser())
            {{-- Superuser Stats --}}
            <div class="col-6 col-md-3">
                <a class="block block-rounded block-link-pop text-center" href="{{ route('superuser.users.index') }}">
                    <div class="block-content block-content-full">
                        <div class="font-size-h2 font-w700">{{ $stats['total_users'] }}</div>
                        <div class="font-size-sm font-w600 text-uppercase text-muted">Total User</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a class="block block-rounded block-link-pop text-center" href="{{ route('superuser.users.index') }}">
                    <div class="block-content block-content-full">
                        <div class="font-size-h2 font-w700 text-warning">{{ $stats['pending_approvals'] }}</div>
                        <div class="font-size-sm font-w600 text-uppercase text-muted">Pending Admin</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <div class="block block-rounded text-center">
                    <div class="block-content block-content-full">
                        <div class="font-size-h2 font-w700 text-info">{{ $stats['total_teachers'] }}</div>
                        <div class="font-size-sm font-w600 text-uppercase text-muted">Guru</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="block block-rounded text-center">
                    <div class="block-content block-content-full">
                        <div class="font-size-h2 font-w700 text-success">{{ $stats['total_students'] }}</div>
                        <div class="font-size-sm font-w600 text-uppercase text-muted">Siswa</div>
                    </div>
                </div>
            </div>

        @elseif(Auth::user()->isAdministrator() || Auth::user()->isTeacher())
            {{-- Admin/Teacher Stats --}}
            <div class="col-6 col-md-6">
                <a class="block block-rounded block-link-pop text-center" href="{{ route('question-packages.index') }}">
                    <div class="block-content block-content-full">
                        <div class="font-size-h2 font-w700 text-primary">{{ $stats['total_packages'] }}</div>
                        <div class="font-size-sm font-w600 text-uppercase text-muted">Paket Soal</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-6">
                <div class="block block-rounded text-center">
                    <div class="block-content block-content-full">
                        <div class="font-size-h2 font-w700 text-success">{{ $stats['total_attempts'] }}</div>
                        <div class="font-size-sm font-w600 text-uppercase text-muted">Total Pengerjaan</div>
                    </div>
                </div>
            </div>

        @elseif(Auth::user()->isStudent())
            {{-- Student Stats --}}
            <div class="col-6 col-md-4">
                <a class="block block-rounded block-link-pop text-center" href="{{ route('exams.index') }}">
                    <div class="block-content block-content-full">
                        <div class="font-size-h2 font-w700 text-primary">{{ $stats['total_attempts'] }}</div>
                        <div class="font-size-sm font-w600 text-uppercase text-muted">Total Ujian</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-4">
                <div class="block block-rounded text-center">
                    <div class="block-content block-content-full">
                        <div class="font-size-h2 font-w700 text-success">{{ $stats['completed_exams'] }}</div>
                        <div class="font-size-sm font-w600 text-uppercase text-muted">Selesai</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="block block-rounded text-center">
                    <div class="block-content block-content-full">
                        <div class="font-size-h2 font-w700 text-warning">{{ round($stats['average_score'], 1) }}</div>
                        <div class="font-size-sm font-w600 text-uppercase text-muted">Rata-rata Skor</div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Quick Actions --}}
    <div class="row">
        <div class="col-md-12">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Aksi Cepat</h3>
                </div>
                <div class="block-content block-content-full">
                    <div class="row g-sm text-center">
                        @if(Auth::user()->isSuperuser())
                            <div class="col-6 col-md-4">
                                <a class="btn btn-block btn-alt-primary py-3" href="{{ route('superuser.users.index') }}">
                                    <i class="fa fa-users-cog fa-2x d-block mb-2"></i> Kelola User
                                </a>
                            </div>
                        @endif
                        
                        @if(Auth::user()->isAdministrator() || Auth::user()->isTeacher() || Auth::user()->isSuperuser())
                            <div class="col-6 col-md-4">
                                <a class="btn btn-block btn-alt-warning py-3" href="{{ route('question-packages.index') }}">
                                    <i class="fa fa-folder-open fa-2x d-block mb-2"></i> Paket Soal
                                </a>
                            </div>
                            <div class="col-6 col-md-4">
                                <a class="btn btn-block btn-alt-success py-3" href="{{ route('question-packages.create') }}">
                                    <i class="fa fa-plus-circle fa-2x d-block mb-2"></i> Buat Paket Baru
                                </a>
                            </div>
                        @endif

                        @if(Auth::user()->isStudent() || Auth::user()->isAdministrator() || Auth::user()->isSuperuser())
                            <div class="col-6 col-md-4">
                                <a class="btn btn-block btn-alt-info py-3" href="{{ route('exams.index') }}">
                                    <i class="fa fa-pen-nib fa-2x d-block mb-2"></i> Daftar Ujian
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
