@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="bg-body-light">
    <div class="hero-static d-flex align-items-center justify-content-center">
        <div class="content">
            <div class="row justify-content-center push">
                <div class="col-md-8 col-lg-6 col-xl-4">
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
                                <h1 class="h3 fw-bold mb-1">Selamat Datang</h1>
                                <p class="text-muted mb-0">Silakan masuk ke akun Anda</p>
                            </div>

                            @if (session('success'))
                                <div class="alert alert-success d-flex align-items-center" role="alert">
                                    <i class="fa fa-check-circle me-2"></i>
                                    <p class="mb-0">{{ session('success') }}</p>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger d-flex align-items-center" role="alert">
                                    <i class="fa fa-exclamation-circle me-2"></i>
                                    <p class="mb-0">{{ session('error') }}</p>
                                </div>
                            @endif

                            <form action="{{ route('login') }}" method="POST">
                                @csrf
                                <div class="form-floating mb-4">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="login-email" name="email" placeholder="Masukkan email" value="{{ old('email') }}" required>
                                    <label class="form-label" for="login-email">Alamat Email</label>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-floating mb-4">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="login-password" name="password" placeholder="Masukkan password" required>
                                    <label class="form-label" for="login-password">Password</label>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="login-remember-me" name="remember">
                                        <label class="form-check-label fs-sm" for="login-remember-me">Ingat Saya</label>
                                    </div>
                                    <a class="fs-sm fw-medium link-fx" href="#">Lupa Password?</a>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold">
                                    Masuk Ke Dashboard
                                </button>
                            </form>
                        </div>
                        <div class="block-content bg-body-light p-4 text-center">
                            <p class="mb-0 fs-sm">
                                Belum punya akun? <a class="fw-semibold" href="{{ route('register') }}">Daftar sekarang</a>
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
