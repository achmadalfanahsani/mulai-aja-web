@extends('layouts.app')

@section('title', 'Manajemen Kelas')
@section('page-heading', 'Manajemen Kelas')

@section('content')
<div class="block block-rounded">
    <div class="block-header block-header-default">
        <h3 class="block-title">Daftar Kelas</h3>
        <div class="block-options">
            @can('create', App\Models\Classroom::class)
            <a href="{{ route('classrooms.create') }}" class="btn btn-sm btn-primary">
                <i class="fa fa-plus me-1"></i> Buat Kelas Baru
            </a>
            @endcan
        </div>
    </div>
    <div class="block-content">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-vcenter">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th>Nama Kelas</th>
                        <th>Deskripsi</th>
                        <th class="text-center" style="width: 15%;">Jumlah Siswa</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classrooms as $classroom)
                    <tr>
                        <td class="text-center">{{ ($classrooms->currentPage() - 1) * $classrooms->perPage() + $loop->iteration }}</td>
                        <td class="fw-semibold">
                            <a href="{{ route('classrooms.show', $classroom->id) }}">{{ $classroom->name }}</a>
                        </td>
                        <td>
                            {{ Str::limit($classroom->description, 100) ?: '-' }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $classroom->students_count }} Siswa</span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('classrooms.show', $classroom->id) }}" class="btn btn-sm btn-alt-secondary" title="Detail & Kelola">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ route('classrooms.edit', $classroom->id) }}" class="btn btn-sm btn-alt-info" title="Edit">
                                    <i class="fa fa-pencil-alt"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-alt-danger" title="Hapus" 
                                    data-bs-toggle="modal" data-bs-target="#modal-delete-{{ $classroom->id }}">
                                    <i class="fa fa-trash"></i>
                                </button>

                                <!-- Modal: Delete Classroom -->
                                <div class="modal fade" id="modal-delete-{{ $classroom->id }}" tabindex="-1" role="dialog" aria-labelledby="modal-delete-{{ $classroom->id }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="{{ route('classrooms.destroy', $classroom->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <div class="block block-rounded block-transparent mb-0">
                                                    <div class="block-header block-header-default">
                                                        <h3 class="block-title">Hapus Kelas</h3>
                                                        <div class="block-options">
                                                            <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                                                                <i class="fa fa-fw fa-times"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="block-content fs-sm text-start">
                                                        <p>Apakah Anda yakin ingin menghapus kelas <strong>{{ $classroom->name }}</strong>?</p>
                                                    </div>
                                                    <div class="block-content block-content-full text-end bg-body">
                                                        <button type="button" class="btn btn-sm btn-alt-secondary me-1" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-sm btn-danger">Ya, Hapus Kelas</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">Belum ada data kelas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $classrooms->links() }}
        </div>
    </div>
</div>
@endsection
