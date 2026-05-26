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
                    <a href="{{ route('question-packages.create', ['type' => request('type')]) }}" class="btn btn-sm btn-alt-secondary">
                        <i class="fa fa-plus me-1"></i> Buat Paket Baru
                    </a>
                </div>
            </div>
            
            <div class="block-content block-content-full">
                <!-- Filter Form -->
                <form action="{{ route('question-packages.index') }}" method="GET" class="mb-4">
                    @if(request()->has('type'))
                        <input type="hidden" name="type" value="{{ request('type') }}">
                    @endif
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="q" class="form-control" placeholder="Cari Nama Paket..." value="{{ request('q') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="package_type" class="form-select">
                                <option value="">Semua Tipe</option>
                                <option value="multiple_choice" {{ request('package_type') == 'multiple_choice' ? 'selected' : '' }}>Pilihan Ganda</option>
                                <option value="essay" {{ request('package_type') == 'essay' ? 'selected' : '' }}>Isian</option>
                                <option value="mixed" {{ request('package_type') == 'mixed' ? 'selected' : '' }}>Campuran</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa fa-filter me-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>

                @if ($packages->isEmpty())
                    <div class="text-center py-5">
                        <i class="fa fa-folder-open fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Belum ada Paket Soal yang Dibuat</h4>
                        <p class="text-muted">Buat paket soal pertama Anda untuk mendistribusikan ujian ke siswa.</p>
                        <a href="{{ route('question-packages.create', ['type' => request('type')]) }}" class="btn btn-primary">
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
                                            <div class="text-muted font-size-sm mt-1">
                                                <i class="fa fa-user-edit text-primary-light me-1"></i> {{ $package->user->name ?? 'Sistem' }}
                                                <span class="ms-2 badge {{ $package->type_badge_class }}">{{ $package->type_label }}</span>
                                            </div>
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
                                                    <button type="submit" class="btn btn-sm btn-success bg-success-light text-success font-w600 border-0 rounded-pill px-3" style="white-space: nowrap;">
                                                        <i class="fa fa-check me-1"></i> Published
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn btn-sm btn-warning bg-warning-light text-warning font-w600 border-0 rounded-pill px-3" style="white-space: nowrap;">
                                                        <i class="fa fa-lock me-1"></i> Draft
                                                    </button>
                                                @endif
                                            </form>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="{{ route('question-packages.results', [$package->id, 'type' => request('type')]) }}" class="btn btn-sm btn-alt-success" data-toggle="tooltip" title="Lihat Hasil Pengerjaan">
                                                    <i class="fa fa-chart-line"></i>
                                                </a>
                                                <a href="{{ route('question-packages.questions.index', [$package->id, 'type' => request('type')]) }}" class="btn btn-sm btn-alt-info" data-toggle="tooltip" title="Kelola Soal">
                                                    <i class="fa fa-list"></i>
                                                </a>
                                                <a href="{{ route('question-packages.edit', [$package->id, 'type' => request('type')]) }}" class="btn btn-sm btn-alt-warning" data-toggle="tooltip" title="Edit Paket">
                                                    <i class="fa fa-pencil-alt"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-alt-danger" data-bs-toggle="modal" data-bs-target="#modal-delete-{{ $package->id }}" title="Hapus Paket">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="modal-delete-{{ $package->id }}" tabindex="-1" role="dialog" aria-labelledby="modal-delete-{{ $package->id }}" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form action="{{ route('question-packages.destroy', $package->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <div class="block block-rounded shadow-none mb-0">
                                                                <div class="block-header block-header-default">
                                                                    <h3 class="block-title">Hapus Paket Soal: {{ $package->name }}</h3>
                                                                    <div class="block-options">
                                                                        <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                                                                            <i class="fa fa-times"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <div class="block-content fs-sm py-4">
                                                                    <p class="mb-0">Apakah Anda yakin ingin menghapus paket soal ini? Seluruh data soal dan jawaban siswa akan hilang!</p>
                                                                </div>
                                                                <div class="block-content block-content-full block-content-sm text-end border-top">
                                                                    <button type="button" class="btn btn-alt-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-alt-danger">Ya, Hapus</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
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
