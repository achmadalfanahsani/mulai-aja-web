@extends('layouts.app')

@section('title', 'Edit Kelas: ' . $classroom->name)
@section('page-heading', 'Edit Kelas: ' . $classroom->name)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">Form Edit Data Kelas</h3>
            </div>
            <div class="block-content">
                <form action="{{ route('classrooms.update', $classroom->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label class="form-label" for="name">Nama Kelas <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Misal: XII RPL 1" value="{{ old('name', $classroom->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label" for="description">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="Keterangan tambahan mengenai kelas ini...">{{ old('description', $classroom->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save me-1"></i> Perbarui Kelas
                        </button>
                        <a href="{{ route('classrooms.index') }}" class="btn btn-alt-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
