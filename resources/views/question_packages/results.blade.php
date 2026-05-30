@extends('layouts.app')

@section('title', 'Hasil Pengerjaan Siswa | MulaiAja')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="block block-rounded block-themed">
            <div class="block-header block-header-default bg-primary-dark">
                <h3 class="block-title">Hasil Pengerjaan: {{ $questionPackage->name }}</h3>
                <div class="block-options">
                    @if(request('from_classroom'))
                        <a href="{{ route('classrooms.show', request('from_classroom')) }}" class="btn btn-sm btn-alt-secondary">
                            <i class="fa fa-arrow-left mr-1"></i> Kembali ke Kelas
                        </a>
                    @else
                        <a href="{{ route('question-packages.index', ['type' => request('type')]) }}" class="btn btn-sm btn-alt-secondary">
                            <i class="fa fa-arrow-left mr-1"></i> Kembali
                        </a>
                    @endif
                </div>
            </div>
            
            <div class="block-content block-content-full">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="block block-rounded bg-body-light p-3 text-center">
                            <div class="font-size-sm font-w700 text-uppercase text-muted">Total Attempt</div>
                            <div class="font-size-h3 font-w700">{{ $attempts->total() }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="block block-rounded bg-body-light p-3 text-center">
                            <div class="font-size-sm font-w700 text-uppercase text-muted">Rata-rata Skor</div>
                            <div class="font-size-h3 font-w700 text-primary">{{ round($attempts->avg('total_score'), 1) }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="block block-rounded bg-body-light p-3 text-center">
                            <div class="font-size-sm font-w700 text-uppercase text-muted">Lulus</div>
                            <div class="font-size-h3 font-w700 text-success">
                                {{ $attempts->filter(fn($a) => $a->isPassed() === true)->count() }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="block block-rounded bg-body-light p-3 text-center">
                            <div class="font-size-sm font-w700 text-uppercase text-muted">Gagal</div>
                            <div class="font-size-h3 font-w700 text-danger">
                                {{ $attempts->filter(fn($a) => $a->isPassed() === false)->count() }}
                            </div>
                        </div>
                    </div>
                </div>

                @if ($attempts->isEmpty())
                    <div class="text-center py-5">
                        <i class="fa fa-history fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Belum ada Siswa yang Mengerjakan</h4>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;">#</th>
                                    <th>Nama Siswa</th>
                                    <th class="text-center">Waktu Mulai</th>
                                    <th class="text-center">Durasi</th>
                                    <th class="text-center">Skor</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($attempts as $attempt)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration + ($attempts->currentPage() - 1) * $attempts->perPage() }}</td>
                                        <td class="font-w600">{{ $attempt->user->name }}</td>
                                        <td class="text-center">{{ $attempt->started_at->format('d M Y, H:i') }}</td>
                                        <td class="text-center">{{ $attempt->getFormattedDuration() }}</td>
                                        <td class="text-center font-w700 font-size-lg">
                                            @if($attempt->is_completed)
                                                <span class="{{ $attempt->isPassed() === false ? 'text-danger' : 'text-success' }}">
                                                    {{ $attempt->total_score }}
                                                </span>
                                            @else
                                                <span class="text-muted font-size-sm">InProgress</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($attempt->is_completed)
                                                @if($attempt->isPassed() === true)
                                                    <span class="badge bg-success">Lulus</span>
                                                @elseif($attempt->isPassed() === false)
                                                    <span class="badge bg-danger">Gagal</span>
                                                @else
                                                    <span class="badge bg-primary">Selesai</span>
                                                @endif
                                            @else
                                                <span class="badge bg-warning">Berjalan</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($attempt->is_completed)
                                                <a href="{{ route('exams.results', [$attempt->id, 'from' => 'package_results', 'from_classroom' => request('from_classroom'), 'type' => request('type')]) }}" class="btn btn-sm btn-alt-info" title="Lihat Detail">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        {{ $attempts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
