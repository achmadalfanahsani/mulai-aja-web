@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-heading', 'Dashboard')

@section('content')
    {{-- CBT Welcome Banner --}}
    <div class="row">
        <div class="col-md-12">
            <div class="block block-rounded block-themed">
                <div class="block-content block-content-full bg-gd-dusk text-center p-5 text-white rounded">
                    <h2 class="h1 font-w800 mb-2">Selamat Datang di MulaiAja CBT</h2>
                    <p class="font-size-lg text-white-75 mb-4">Platform Ujian Online Berbasis Komputer Modern & Terintegrasi.</p>
                    
                    @auth
                        <div class="d-flex justify-content-center align-items-center flex-wrap">
                            <span class="badge bg-white-10 text-white font-size-sm py-2 px-3 mr-3 mb-2">
                                <i class="fa fa-user-circle mr-1"></i> {{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})
                            </span>
                            @if(Auth::user()->isAdmin() || Auth::user()->isTeacher())
                                <a href="{{ route('question-packages.index') }}" class="btn btn-warning font-w700 mr-2 mb-2">
                                    <i class="fa fa-folder-open mr-1"></i> Kelola Paket Soal
                                </a>
                            @endif
                            @if(Auth::user()->isAdmin() || Auth::user()->isStudent())
                                <a href="{{ route('exams.index') }}" class="btn btn-info font-w700 mb-2">
                                    <i class="fa fa-pen-nib mr-1"></i> Mulai Ujian CBT
                                </a>
                            @endif
                        </div>
                    @else
                        <p class="mb-4">Silakan masuk menggunakan sistem pengujian instan untuk memulai petualangan belajar Anda.</p>
                        <a href="{{ route('login') }}" class="btn btn-lg btn-alt-secondary font-w700 px-4">
                            <i class="fa fa-sign-in-alt mr-1"></i> Masuk via Mock Login
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <div class="block block-rounded block-fx-shadow">
        <div class="block-content bg-body-light">
            <!-- Search -->
            <form>
                <div class="mb-4">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search orders..">
                        <button type="submit" class="btn btn-secondary">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
            <!-- END Search -->
        </div>
        <div class="block-content">
            <!-- Orders Table -->
            <table class="table table-borderless table-striped">
                <thead>
                    <tr>
                        <th style="width: 100px;">ID</th>
                        <th>Status</th>
                        <th class="d-none d-sm-table-cell">Submitted</th>
                        <th class="d-none d-sm-table-cell">Products</th>
                        <th class="d-none d-sm-table-cell">Customer</th>
                        <th class="d-none d-sm-table-cell text-end">Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a class="fw-semibold" href="javascript:void(0)">ORD.1851</a>
                        </td>
                        <td>
                            <span class="badge bg-info">Processing</span>
                        </td>
                        <td class="d-none d-sm-table-cell">
                            2024/10/27
                        </td>
                        <td class="d-none d-sm-table-cell">
                            <a href="javascript:void(0)">1</a>
                        </td>
                        <td class="d-none d-sm-table-cell">
                            <a href="javascript:void(0)">Jack Estrada</a>
                        </td>
                        <td class="d-none d-sm-table-cell text-end">$103</td>
                    </tr>
                    <tr>
                        <td>
                            <a class="fw-semibold" href="javascript:void(0)">ORD.1850</a>
                        </td>
                        <td>
                            <span class="badge bg-info">Processing</span>
                        </td>
                        <td class="d-none d-sm-table-cell">
                            2024/10/26
                        </td>
                        <td class="d-none d-sm-table-cell">
                            <a href="javascript:void(0)">7</a>
                        </td>
                        <td class="d-none d-sm-table-cell">
                            <a href="javascript:void(0)">Jack Estrada</a>
                        </td>
                        <td class="d-none d-sm-table-cell text-end">$244</td>
                    </tr>
                    <tr>
                        <td>
                            <a class="fw-semibold" href="javascript:void(0)">ORD.1849</a>
                        </td>
                        <td>
                            <span class="badge bg-info">Processing</span>
                        </td>
                        <td class="d-none d-sm-table-cell">
                            2024/10/25
                        </td>
                        <td class="d-none d-sm-table-cell">
                            <a href="javascript:void(0)">2</a>
                        </td>
                        <td class="d-none d-sm-table-cell">
                            <a href="javascript:void(0)">Carol Ray</a>
                        </td>
                        <td class="d-none d-sm-table-cell text-end">$561</td>
                    </tr>
                    <tr>
                        <td>
                            <a class="fw-semibold" href="javascript:void(0)">ORD.1848</a>
                        </td>
                        <td>
                            <span class="badge bg-danger">Canceled</span>
                        </td>
                        <td class="d-none d-sm-table-cell">
                            2024/10/24
                        </td>
                        <td class="d-none d-sm-table-cell">
                            <a href="javascript:void(0)">3</a>
                        </td>
                        <td class="d-none d-sm-table-cell">
                            <a href="javascript:void(0)">Sara Fields</a>
                        </td>
                        <td class="d-none d-sm-table-cell text-end">$651</td>
                    </tr>
                    <tr>
                        <td>
                            <a class="fw-semibold" href="javascript:void(0)">ORD.1837</a>
                        </td>
                        <td>
                            <span class="badge bg-danger">Canceled</span>
                        </td>
                        <td class="d-none d-sm-table-cell">
                            2024/10/13
                        </td>
                        <td class="d-none d-sm-table-cell">
                            <a href="javascript:void(0)">5</a>
                        </td>
                        <td class="d-none d-sm-table-cell">
                            <a href="javascript:void(0)">Lisa Jenkins</a>
                        </td>
                        <td class="d-none d-sm-table-cell text-end">$971</td>
                    </tr>
                </tbody>
            </table>
            <!-- END Orders Table -->

            <!-- Navigation -->
            <nav aria-label="Orders navigation">
                <ul class="pagination justify-content-end">
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0)" aria-label="Previous">
                            <span aria-hidden="true">
                                <i class="fa fa-angle-left"></i>
                            </span>
                            <span class="sr-only">Previous</span>
                        </a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link" href="javascript:void(0)">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0)">2</a>
                    </li>
                    <li class="page-item disabled">
                        <a class="page-link" href="javascript:void(0)">...</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0)">8</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0)">9</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0)" aria-label="Next">
                            <span aria-hidden="true">
                                <i class="fa fa-angle-right"></i>
                            </span>
                            <span class="sr-only">Next</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- END Navigation -->
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
