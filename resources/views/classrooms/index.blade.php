@extends('layouts.app')

@section('title', 'Manajemen Kelas')
@section('page-heading', 'Manajemen Kelas')

@section('content')
<div class="block block-rounded">
    <div class="block-header block-header-default">
        <h3 class="block-title">Daftar Kelas</h3>
        <div class="block-options">
            <a href="{{ route('classrooms.create') }}" class="btn btn-sm btn-primary">
                <i class="fa fa-plus me-1"></i> Buat Kelas Baru
            </a>
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
                        <th>Pengajar</th>
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
                        <td>{{ $classroom->teacher->name }}</td>
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
                                    onclick="if(confirm('Apakah Anda yakin ingin menghapus kelas ini?')) { document.getElementById('delete-form-{{ $classroom->id }}').submit(); }">
                                    <i class="fa fa-trash"></i>
                                </button>
                                <form id="delete-form-{{ $classroom->id }}" action="{{ route('classrooms.destroy', $classroom->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
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
