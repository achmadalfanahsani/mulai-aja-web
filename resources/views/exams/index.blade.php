@extends('layouts.app')

@section('title', 'Daftar Ujian CBT | MulaiAja')

@section('content')
<div class="row">
    <div class="col-md-12">
        @if (session('success'))
            <div class="alert alert-success d-flex align-items-center justify-content-between" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fa fa-check-circle mr-2"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger d-flex align-items-center justify-content-between" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fa fa-exclamation-circle mr-2"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Ujian CBT yang Tersedia --}}
        <h2 class="content-heading d-flex align-items-center">
            <i class="fa fa-pen-nib mr-2 text-primary"></i> Ujian CBT yang Tersedia
        </h2>
        
        @if ($packages->isEmpty())
            <div class="block block-rounded block-bordered p-5 text-center">
                <i class="fa fa-calendar-times fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Tidak ada Ujian CBT yang Aktif saat ini</h4>
                <p class="text-muted mb-0">Hubungi guru Anda jika Anda merasa ada kesalahan penjadwalan ujian.</p>
            </div>
        @else
            <div class="row">
                @foreach ($packages as $package)
                    <div class="col-md-4 mb-4">
                        <div class="block block-rounded block-bordered h-100 d-flex flex-column mb-0">
                            <div class="block-content block-content-full bg-primary-dark text-center py-4">
                                <i class="fa fa-file-invoice fa-3x text-white-50 mb-3"></i>
                                <h4 class="font-w700 text-white mb-0 text-truncate px-2">{{ $package->name }}</h4>
                            </div>
                            
                            <div class="block-content flex-grow-1 py-3">
                                <p class="text-muted font-size-sm mb-3 text-break-word">
                                    {{ $package->description ?? 'Tidak ada deskripsi rincian paket soal.' }}
                                </p>
                                
                                <div class="row text-center font-size-sm">
                                    <div class="col-6 border-right">
                                        <div class="font-size-h5 font-w700 text-dark">{{ $package->active_questions_count }}</div>
                                        <div class="text-muted text-uppercase font-size-xs font-w600">Pertanyaan</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="font-size-h5 font-w700 text-dark">{{ $package->duration_minutes }}</div>
                                        <div class="text-muted text-uppercase font-size-xs font-w600">Menit</div>
                                    </div>
                                </div>
                                
                                @if($package->passing_score)
                                    <div class="text-center font-size-xs text-warning font-w700 mt-3">
                                        <i class="fa fa-award mr-1"></i> Nilai Kelulusan minimum: {{ $package->passing_score }}%
                                    </div>
                                @endif
                            </div>

                            <div class="block-content block-content-full block-content-sm bg-body-light border-top">
                                <form action="{{ route('exams.start', $package->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100 font-w600">
                                        <i class="fa fa-play-circle mr-1"></i> Mulai Ujian
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="d-flex justify-content-end mt-2 mb-4">
                {{ $packages->links() }}
            </div>
        @endif

        {{-- Riwayat Ujian --}}
        <h2 class="content-heading d-flex align-items-center mt-4">
            <i class="fa fa-history mr-2 text-primary"></i> Riwayat Ujian Anda
        </h2>

        <div class="block block-rounded block-themed">
            <div class="block-header block-header-default bg-primary-dark">
                <h3 class="block-title">Riwayat Pengerjaan</h3>
            </div>
            
            <div class="block-content block-content-full">
                @if ($attempts->isEmpty())
                    <div class="text-center py-4">
                        <p class="text-muted mb-0">Anda belum pernah mengambil ujian CBT apa pun.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover table-vcenter">
                            <thead>
                                <tr>
                                    <th>Paket Ujian</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Durasi Spent</th>
                                    <th class="text-center">Skor</th>
                                    <th class="text-center">Status Kelulusan</th>
                                    <th class="text-center">Evaluasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($attempts as $attempt)
                                    <tr>
                                        <td class="font-w600">{{ $attempt->questionPackage->name }}</td>
                                        <td class="font-size-sm text-muted">
                                            {{ $attempt->started_at->format('d M Y, H:i') }}
                                        </td>
                                        <td class="font-size-sm">
                                            <i class="fa fa-clock text-muted mr-1"></i> {{ $attempt->getFormattedDuration() }}
                                        </td>
                                        <td class="text-center font-w700 font-size-lg text-primary">
                                            @if (!$attempt->is_completed)
                                                <span class="text-muted">-</span>
                                            @else
                                                {{ $attempt->total_score ?? '0' }}
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if (!$attempt->is_completed)
                                                <span class="badge bg-warning-light text-warning font-w700 font-size-sm rounded-pill px-3">
                                                    <i class="fa fa-spinner fa-spin mr-1"></i> DALAM PENGERJAAN
                                                </span>
                                            @elseif ($attempt->isPassed() === true)
                                                <span class="badge bg-success-light text-success font-w700 font-size-sm rounded-pill px-3">
                                                    <i class="fa fa-check-circle mr-1"></i> LULUS
                                                </span>
                                            @elseif ($attempt->isPassed() === false)
                                                <span class="badge bg-danger-light text-danger font-w700 font-size-sm rounded-pill px-3">
                                                    <i class="fa fa-times-circle mr-1"></i> TIDAK LULUS
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-light text-secondary font-w700 font-size-sm rounded-pill px-3">
                                                    Selesai
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if (!$attempt->is_completed)
                                                <a href="{{ route('exams.attempt', $attempt->id) }}" class="btn btn-sm btn-alt-warning">
                                                    <i class="fa fa-play mr-1"></i> Lanjutkan Ujian
                                                </a>
                                            @else
                                                <a href="{{ route('exams.results', $attempt->id) }}" class="btn btn-sm btn-alt-info">
                                                    <i class="fa fa-chart-pie mr-1"></i> Review Hasil
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $attempts->appends(['packages_page' => request('packages_page')])->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
