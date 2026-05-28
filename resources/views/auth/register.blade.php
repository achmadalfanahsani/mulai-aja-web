@extends('layouts.guest')

@section('title', 'Daftar Akun')

@section('content')
<div class="bg-body-light">
    <div class="hero-static d-flex align-items-center justify-content-center">
        <div class="content">
            <div class="row justify-content-center push">
                <div class="col-md-10 col-lg-8 col-xl-6">
                    <div class="block block-rounded block-transparent bg-transparent mb-0">
                        <div class="block-content block-content-full text-center">
                            <a class="link-fx fw-bold" href="/">
                                <i class="fa fa-fire text-primary me-1"></i>
                                <span class="fs-4 text-dual-dark">Mulai</span><span class="fs-4 text-primary">Aja</span>
                            </a>
                            <p class="text-muted fw-medium mt-2 mb-0">
                                Platform Computer Based Test (CBT)
                            </p>
                        </div>
                    </div>

                    <div class="block block-rounded block-shadow-2 mb-0 overflow-hidden">
                        <div class="block-content block-content-full p-4 p-md-5">
                            <div class="text-center mb-4">
                                <h1 class="h3 fw-bold mb-1">Buat Akun Baru</h1>
                                <p class="text-muted mb-0">Lengkapi data di bawah ini untuk mendaftar</p>
                            </div>

                            <form action="{{ route('register') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-floating mb-4">
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="register-name" name="name" placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required>
                                            <label class="form-label" for="register-name">Nama Lengkap</label>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-4">
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="register-email" name="email" placeholder="Masukkan email" value="{{ old('email') }}" required>
                                            <label class="form-label" for="register-email">Email</label>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="form-label fs-sm fw-semibold mb-2" for="register-role">Daftar Sebagai</label>
                                    <select class="form-select form-select-lg @error('role') is-invalid @enderror" id="register-role" name="role" required style="font-size: 1rem;">
                                        <option value="" disabled selected>Pilih Peran Anda</option>
                                        <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student (Siswa)</option>
                                        <option value="administrator" {{ old('role') == 'administrator' ? 'selected' : '' }}>Administrator (Admin Lembaga)</option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text fs-xs text-muted mt-2">
                                        <i class="fa fa-info-circle me-1"></i> Akun Administrator memerlukan persetujuan manual.
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-floating mb-4">
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="register-password" name="password" placeholder="Masukkan password" required>
                                            <label class="form-label" for="register-password">Password</label>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-4">
                                            <input type="password" class="form-control" id="register-password-confirm" name="password_confirmation" placeholder="Konfirmasi password" required>
                                            <label class="form-label" for="register-password-confirm">Konfirmasi</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="register-terms" name="terms" required>
                                        <label class="form-check-label fs-sm" for="register-terms">
                                            Saya setuju dengan <a href="#">Syarat & Ketentuan</a>
                                        </label>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold">
                                    Daftar Sekarang
                                </button>
                            </form>
                        </div>
                        <div class="block-content bg-body-light p-4 text-center">
                            <p class="mb-0 fs-sm">
                                Sudah punya akun? <a class="fw-semibold" href="{{ route('login') }}">Masuk di sini</a>
                            </p>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <p class="fs-xs text-muted">
                            &copy; {{ date('Y') }} MulaiAja. All rights reserved.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
