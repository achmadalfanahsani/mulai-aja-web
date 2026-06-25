@extends('layouts.guest')

@section('title', 'Lupa Password')

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
                                <h1 class="h3 fw-bold mb-1">Lupa Password?</h1>
                                <p class="text-muted mb-0">Masukkan email Anda untuk menerima link reset password.</p>
                            </div>

                            @if (session('status'))
                                <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                                    <div class="flex-shrink-0 me-2">
                                        <i class="fa fa-check-circle"></i>
                                    </div>
                                    <div class="flex-grow-1 fs-sm">
                                        {{ session('status') }}
                                    </div>
                                </div>
                            @endif

                            <form autocomplete="off" action="{{ route('password.email') }}" method="POST">
                                @csrf
                                <div class="form-floating mb-4">
                                    <input autocomplete="off" type="email" class="form-control @error('email') is-invalid @enderror" id="forgot-email" name="email" placeholder="Masukkan email Anda" value="{{ old('email') }}" required>
                                    <label class="form-label" for="forgot-email">Alamat Email</label>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold">
                                    Kirim Link Reset
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
