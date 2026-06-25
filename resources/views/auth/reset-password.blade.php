@extends('layouts.guest')

@section('title', 'Reset Password')

@section('content')
<div class="bg-body-light">
    <div class="hero-static d-flex align-items-center justify-content-center">
        <div class="content">
            <div class="row justify-content-center push">
                <div class="col-md-8 col-lg-6 col-xl-5">
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
                                <h1 class="h3 fw-bold mb-1">Atur Ulang Password</h1>
                                <p class="text-muted mb-0">Masukkan password baru Anda di bawah ini.</p>
                            </div>

                            <form autocomplete="off" action="{{ route('password.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="token" value="{{ $token }}">

                                <div class="form-floating mb-4">
                                    <input autocomplete="off" type="email" class="form-control @error('email') is-invalid @enderror" id="reset-email" name="email" placeholder="Masukkan email Anda" value="{{ $email ?? old('email') }}" required readonly>
                                    <label class="form-label" for="reset-email">Alamat Email</label>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating mb-4">
                                    <input autocomplete="off" type="password" class="form-control @error('password') is-invalid @enderror" id="reset-password" name="password" placeholder="Password Baru" required>
                                    <label class="form-label" for="reset-password">Password Baru</label>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating mb-4">
                                    <input autocomplete="off" type="password" class="form-control" id="reset-password-confirm" name="password_confirmation" placeholder="Konfirmasi Password Baru" required>
                                    <label class="form-label" for="reset-password-confirm">Konfirmasi Password Baru</label>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold">
                                    Atur Ulang Password
                                </button>
                            </form>
                        </div>
                        <div class="block-content bg-body-light p-4 text-center">
                            <p class="mb-0 fs-sm">
                                Ingat password Anda? <a class="fw-semibold" href="{{ route('login') }}">Masuk di sini</a>
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
