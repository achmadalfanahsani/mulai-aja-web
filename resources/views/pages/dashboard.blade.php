@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-heading', 'Dashboard')

@section('content')
    {{-- CBT Welcome Banner --}}
    <div class="row">
        <div class="col-md-12">
            <div class="block block-rounded block-themed">
                <div class="block-content block-content-full bg-gd-primary text-center p-5 text-white rounded">
                    <h2 class="h1 font-w800 mb-2">Selamat Datang di MulaiAja CBT</h2>
                    <p class="font-size-lg text-white-75 mb-4">Platform Ujian Online Berbasis Komputer Modern & Terintegrasi.</p>
                    
                    @auth
                        <div class="d-flex justify-content-center align-items-center flex-wrap">
                            <span class="badge bg-white-10 text-white font-size-sm py-2 px-3 me-3 mb-2">
                                <i class="fa fa-user-circle me-1"></i> {{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})
                            </span>
                            @if(Auth::user()->isAdmin() || Auth::user()->isTeacher())
                                <a href="{{ route('question-packages.index') }}" class="btn btn-warning font-w700 me-2 mb-2">
                                    <i class="fa fa-folder-open me-1"></i> Kelola Paket Soal
                                </a>
                            @endif
                            @if(Auth::user()->isAdmin() || Auth::user()->isStudent())
                                <a href="{{ route('exams.index') }}" class="btn btn-info font-w700 mb-2">
                                    <i class="fa fa-pen-nib me-1"></i> Mulai Ujian CBT
                                </a>
                            @endif
                        </div>
                    @else
                        <p class="mb-4">Silakan masuk menggunakan sistem pengujian instan untuk memulai petualangan belajar Anda.</p>
                        <a href="{{ route('login') }}" class="btn btn-lg btn-alt-secondary font-w700 px-4">
                            <i class="fa fa-sign-in-alt me-1"></i> Masuk via Mock Login
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- Tambahkan script khusus halaman ini jika diperlukan --}}
@push('scripts')
    <script>
        // Script spesifik halaman dashboard
        console.log('Dashboard loaded');
    </script>
@endpush
