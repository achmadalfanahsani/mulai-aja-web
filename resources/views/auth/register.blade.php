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
                                                <input type="text"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    id="register-name" name="name" placeholder="Masukkan nama lengkap"
                                                    value="{{ old('name') }}" required>
                                                <label class="form-label" for="register-name">Nama Lengkap</label>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-4">
                                                <input type="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    id="register-email" name="email" placeholder="Masukkan email"
                                                    value="{{ old('email') }}" required>
                                                <label class="form-label" for="register-email">Email</label>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="form-label fs-sm fw-semibold mb-2">Daftar Sebagai</label>
                                        <input type="hidden" name="role" value="administrator">
                                        <div
                                            class="form-control form-control-lg bg-body-light border-0 fs-6 fw-medium d-flex align-items-center">
                                            <i class="fa fa-user-tie text-primary me-2"></i> Administrator (Admin Lembaga)
                                        </div>
                                        @error('role')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text fs-xs text-muted mt-2 d-flex align-items-center">
                                            <i class="fa fa-info-circle me-2"></i>
                                            <span>
                                                Pendaftaran mandiri hanya tersedia untuk <strong>Administrator</strong>.
                                                Akun memerlukan persetujuan manual oleh Superuser.
                                            </span>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-floating mb-4">
                                                <input type="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    id="register-password" name="password" placeholder="Masukkan password"
                                                    required>
                                                <label class="form-label" for="register-password">Password</label>
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-4">
                                                <input type="password" class="form-control" id="register-password-confirm"
                                                    name="password_confirmation" placeholder="Konfirmasi password" required>
                                                <label class="form-label" for="register-password-confirm">Konfirmasi</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox"
                                                id="register-terms" name="terms" required {{ old('terms') ? 'checked' : '' }}>
                                            <label class="form-check-label fs-sm" for="register-terms">
                                                Saya setuju dengan <a href="{{ route('terms') }}">Syarat & Ketentuan</a>
                                            </label>
                                            @error('terms')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
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
</div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            const storageKey = 'mulai_aja_reg_data';

            // Function to save form data to sessionStorage
            function saveFormData() {
                const data = {
                    name: $('#register-name').val(),
                    email: $('#register-email').val(),
                    terms: $('#register-terms').is(':checked')
                };
                sessionStorage.setItem(storageKey, JSON.stringify(data));
            }

            // Function to load form data from sessionStorage
            function loadFormData() {
                const savedData = JSON.parse(sessionStorage.getItem(storageKey) || '{}');

                // Only fill if current value is empty (prioritize Laravel's old() data)
                if (savedData.name && !$('#register-name').val()) {
                    $('#register-name').val(savedData.name);
                }
                if (savedData.email && !$('#register-email').val()) {
                    $('#register-email').val(savedData.email);
                }
                if (savedData.terms && !$('#register-terms').is(':checked')) {
                    $('#register-terms').prop('checked', true);
                }
            }

            // Load data on page load
            loadFormData();

            // Save data on any input change
            $('form input:not([type="password"])').on('input change', function() {
                saveFormData();
            });

            // Clear storage when navigating to login
            $('.block-content.bg-body-light a').on('click', function() {
                sessionStorage.removeItem(storageKey);
            });

            // Clear storage on form submission
            $('form').on('submit', function() {
                sessionStorage.removeItem(storageKey);
            });
        });
    </script>
@endpush
