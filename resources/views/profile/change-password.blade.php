@extends('layouts.app')

@section('title', 'Ganti Password')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        @if (session('success'))
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <i class="fa fa-check-circle me-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">Ganti Password</h3>
            </div>
            <div class="block-content">
                <form action="{{ route('profile.password.update') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-4">
                        <label class="form-label" for="current_password">Password Saat Ini</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                               id="current_password" name="current_password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label" for="password">Password Baru</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" 
                               id="password_confirmation" name="password_confirmation" required>
                    </div>

                    <div class="mb-4 d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" onclick="history.back()">Kembali</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save me-1"></i> Perbarui Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
