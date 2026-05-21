@extends('layouts.app')

@section('page-heading', 'Riwayat Ujian')

@section('content')
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
                                    <td class="font-size-sm text-muted">{{ $attempt->started_at->format('d M Y, H:i') }}</td>
                                    <td class="font-size-sm"><i class="fa fa-clock text-muted mr-1"></i> {{ $attempt->getFormattedDuration() }}</td>
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
@endsection
