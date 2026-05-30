@extends('layouts.app')

@section('title', 'Edit Profil | MulaiAja')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="block block-rounded block-themed">
            <div class="block-header block-header-default">
                <h3 class="block-title">Edit Profil</h3>
            </div>
            <div class="block-content block-content-full">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" value="{{ $user->email }}" disabled>
                        <div class="form-text">Email tidak dapat diubah.</div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
