@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="bg-gd-dusk">
    <div class="hero-static content content-full bg-body-extra-light">
        <div class="py-4 px-1 text-center mb-4">
            <a class="link-fx fw-bold" href="/">
                <i class="fa fa-fire"></i>
                <span class="fs-4 text-dual-dark">Mulai</span><span class="fs-4 text-primary">Aja</span>
            </a>
            <h1 class="h3 fw-bold mt-4 mb-2">Selamat Datang di Platform CBT</h1>
            <h2 class="h5 fw-medium text-muted mb-0">Silakan login untuk melanjutkan</h2>
        </div>

        <div class="row justify-content-center px-1">
            <div class="col-sm-8 col-md-6 col-lg-4">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="block block-rounded block-shadow-2 mb-0">
                        <div class="block-content">
                            <div class="form-floating mb-4">
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="login-email" name="email" placeholder="Masukkan email" value="{{ old('email') }}" required>
                                <label class="form-label" for="login-email">Email</label>
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
                            <div class="row mb-4">
                                <div class="col-sm-6 d-sm-flex align-items-center mb-3 mb-sm-0">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="login-remember-me" name="remember">
                                        <label class="form-check-label" for="login-remember-me">Ingat Saya</label>
                                    </div>
                                </div>
                                <div class="col-sm-6 text-sm-end">
                                    <button type="submit" class="btn btn-lg btn-alt-primary fw-semibold">
                                        Masuk
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="block-content bg-body-light p-4">
                            <div class="row g-sm text-center">
                                <div class="col-12 mb-2">
                                    <a class="link-fx fw-semibold fs-sm" href="{{ route('register') }}">
                                        Belum punya akun? Daftar sekarang
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
