@extends('layouts.guest')

@section('title', 'Daftar Akun')

@section('content')
<div class="bg-gd-emerald">
    <div class="hero-static content content-full bg-body-extra-light">
        <div class="py-4 px-1 text-center mb-4">
            <a class="link-fx fw-bold" href="/">
                <i class="fa fa-fire"></i>
                <span class="fs-4 text-dual-dark">Mulai</span><span class="fs-4 text-primary">Aja</span>
            </a>
            <h1 class="h3 fw-bold mt-4 mb-2">Buat Akun Baru</h1>
            <h2 class="h5 fw-medium text-muted mb-0">Bergabunglah dengan platform kami</h2>
        </div>

        <div class="row justify-content-center px-1">
            <div class="col-sm-8 col-md-6 col-lg-5">
                <form action="{{ route('register') }}" method="POST">
                    @csrf
                    <div class="block block-rounded block-shadow-2 mb-0">
                        <div class="block-content">
                            <div class="form-floating mb-4">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="register-name" name="name" placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required>
                                <label class="form-label" for="register-name">Nama Lengkap</label>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-floating mb-4">
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="register-email" name="email" placeholder="Masukkan email" value="{{ old('email') }}" required>
                                <label class="form-label" for="register-email">Email</label>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label" for="register-role">Daftar Sebagai</label>
                                <select class="form-select @error('role') is-invalid @enderror" id="register-role" name="role" required>
                                    <option value="" disabled selected>Pilih Role</option>
                                    <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student (Siswa)</option>
                                    <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>Teacher (Guru)</option>
                                    <option value="administrator" {{ old('role') == 'administrator' ? 'selected' : '' }}>Administrator (Admin Lembaga)</option>
                                </select>
                                <div class="form-text text-muted mt-2">
                                    <small>* Akun Administrator memerlukan persetujuan Superuser sebelum dapat digunakan.</small>
                                </div>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-floating mb-4">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="register-password" name="password" placeholder="Masukkan password" required>
                                <label class="form-label" for="register-password">Password</label>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-floating mb-4">
                                <input type="password" class="form-control" id="register-password-confirm" name="password_confirmation" placeholder="Konfirmasi password" required>
                                <label class="form-label" for="register-password-confirm">Konfirmasi Password</label>
                            </div>
                            <div class="row mb-4">
                                <div class="col-12 text-sm-end">
                                    <button type="submit" class="btn btn-lg btn-alt-success fw-semibold w-100">
                                        Daftar Sekarang
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="block-content bg-body-light p-4">
                            <div class="row g-sm text-center">
                                <div class="col-12 mb-2">
                                    <a class="link-fx fw-semibold fs-sm" href="{{ route('login') }}">
                                        Sudah punya akun? Masuk di sini
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
