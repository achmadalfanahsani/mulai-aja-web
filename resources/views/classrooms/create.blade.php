@extends('layouts.app')

@section('title', 'Buat Kelas Baru')
@section('page-heading', 'Buat Kelas Baru')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">Form Data Kelas</h3>
            </div>
            <div class="block-content">
                <form action="{{ route('classrooms.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label" for="name">Nama Kelas <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Misal: XII RPL 1" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label" for="description">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="Keterangan tambahan mengenai kelas ini...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save me-1"></i> Simpan Kelas
                        </button>
                        <a href="{{ route('classrooms.index') }}" class="btn btn-alt-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
