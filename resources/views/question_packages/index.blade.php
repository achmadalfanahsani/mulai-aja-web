@extends('layouts.app')

@section('title', 'Kelola Paket Soal | MulaiAja')

@section('content')
<div class="row">
    <div class="col-md-12">
        @if (session('success'))
            <div class="alert alert-success d-flex align-items-center justify-content-between" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fa fa-check-circle me-2"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger d-flex align-items-center justify-content-between" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fa fa-exclamation-circle me-2"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="block block-rounded block-themed">
            <div class="block-header block-header-default bg-primary-dark">
                <h3 class="block-title">Kelola Paket Soal</h3>
                <div class="block-options">
                    <a href="{{ route('question-packages.create') }}" class="btn btn-sm btn-alt-secondary">
                        <i class="fa fa-plus me-1"></i> Buat Paket Baru
                    </a>
                </div>
            </div>
            
            <div class="block-content block-content-full">
                @if ($packages->isEmpty())
                    <div class="text-center py-5">
                        <i class="fa fa-folder-open fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Belum ada Paket Soal yang Dibuat</h4>
                        <p class="text-muted">Buat paket soal pertama Anda untuk mendistribusikan ujian ke siswa.</p>
                        <a href="{{ route('question-packages.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Buat Paket Soal
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover table-vcenter">
                            <thead>
                                <tr>
                                    <th>Nama Paket</th>
                                    <th class="d-none d-sm-table-cell">Durasi</th>
                                    <th class="d-none d-sm-table-cell text-center">Passing Score</th>
                                    <th class="text-center">Jumlah Soal</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($packages as $package)
                                    <tr>
                                        <td>
                                            <a class="font-w600" href="{{ route('question-packages.questions.index', $package->id) }}">
                                                {{ $package->name }}
                                            </a>
                                            @if ($package->description)
                                                <div class="text-muted font-size-sm mt-1 text-truncate" style="max-width: 300px;">
                                                    {{ $package->description }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="d-none d-sm-table-cell">
                                            <span class="badge bg-secondary-light text-secondary font-size-sm">
                                                <i class="fa fa-clock me-1"></i> {{ $package->getFormattedDuration() }}
                                            </span>
                                        </td>
                                        <td class="d-none d-sm-table-cell text-center">
                                            @if($package->passing_score)
                                                <span class="font-w600 text-warning">{{ $package->passing_score }}%</span>
                                            @else
                                                <span class="text-muted font-size-sm">Tidak ada</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info-light text-info font-w700 font-size-sm">
                                                {{ $package->questions_count }} Soal
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('question-packages.toggle-publish', $package->id) }}" method="POST">
                                                @csrf
                                                @if ($package->is_published)
                                                    <button type="submit" class="btn btn-sm btn-success bg-success-light text-success font-w600 border-0 rounded-pill px-3">
                                                        <i class="fa fa-check me-1"></i> Published
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn btn-sm btn-warning bg-warning-light text-warning font-w600 border-0 rounded-pill px-3">
                                                        <i class="fa fa-lock me-1"></i> Draft
                                                    </button>
                                                @endif
                                            </form>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="{{ route('question-packages.results', $package->id) }}" 
                                                   class="btn btn-sm btn-alt-success" 
                                                   data-toggle="tooltip" 
                                                   title="Lihat Hasil Pengerjaan">
                                                    <i class="fa fa-chart-line"></i>
                                                </a>
                                                <a href="{{ route('question-packages.questions.index', $package->id) }}" 
                                                   class="btn btn-sm btn-alt-info" 
                                                   data-toggle="tooltip" 
                                                   title="Kelola Soal">
                                                    <i class="fa fa-list"></i>
                                                </a>
                                                <a href="{{ route('question-packages.edit', $package->id) }}" 
                                                   class="btn btn-sm btn-alt-warning" 
                                                   data-toggle="tooltip" 
                                                   title="Edit Paket">
                                                    <i class="fa fa-pencil-alt"></i>
                                                </a>
                                                <form action="{{ route('question-packages.destroy', $package->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus paket soal ini? Seluruh data soal dan jawaban siswa akan hilang!')" style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-alt-danger" data-toggle="tooltip" title="Hapus Paket">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-end mt-3">
                        {{ $packages->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
