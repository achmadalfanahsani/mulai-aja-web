@extends('layouts.guest')

@section('title', 'Syarat & Ketentuan')

@section('content')
<div class="bg-body-light">
    <div class="hero-static d-flex align-items-center justify-content-center">
        <div class="content">
            <div class="row justify-content-center push">
                <div class="col-md-10 col-lg-8 col-xl-7">
                    <!-- Logo and Header -->
                    <div class="block block-rounded block-transparent bg-transparent mb-0">
                        <div class="block-content block-content-full text-center">
                            <a class="link-fx fw-bold" href="/">
                                <i class="fa fa-fire text-primary me-1"></i>
                                <span class="fs-4 text-dual-dark">Mulai</span><span class="fs-4 text-primary">Aja</span>
                            </a>
                            <h1 class="h3 fw-bold mt-4 mb-2">Syarat & Ketentuan Layanan</h1>
                            <p class="text-muted fw-medium mb-0">
                                Harap baca dengan seksama sebelum menggunakan platform kami.
                            </p>
                        </div>
                    </div>

                    <!-- Terms Content -->
                    <div class="block block-rounded block-shadow-2 mb-0 overflow-hidden">
                        <div class="block-content block-content-full p-4 p-md-5">
                            <div class="fs-sm text-muted">
                                <h2 class="h5 fw-bold text-dark mb-3">1. Penerimaan Ketentuan</h2>
                                <p>
                                    Dengan mengakses dan menggunakan platform MulaiAja, Anda dianggap telah membaca, memahami, dan menyetujui untuk terikat oleh Syarat dan Ketentuan ini. Jika Anda tidak setuju, mohon untuk tidak menggunakan layanan kami.
                                </p>

                                <h2 class="h5 fw-bold text-dark mt-4 mb-3">2. Pendaftaran dan Akun</h2>
                                <ul>
                                    <li>Pendaftaran akun saat ini hanya terbuka untuk peran <strong>Administrator</strong> Lembaga.</li>
                                    <li>Setiap pendaftaran memerlukan persetujuan manual oleh Superuser sebelum akun dapat diaktifkan.</li>
                                    <li>Anda bertanggung jawab untuk menjaga kerahasiaan informasi akun dan password Anda.</li>
                                    <li>Anda wajib memberikan informasi yang akurat, lengkap, dan terbaru saat mendaftar.</li>
                                </ul>

                                <h2 class="h5 fw-bold text-dark mt-4 mb-3">3. Penggunaan Layanan</h2>
                                <p>
                                    Platform MulaiAja disediakan untuk keperluan Computer Based Test (CBT). Pengguna dilarang:
                                </p>
                                <ul>
                                    <li>Menggunakan platform untuk tujuan ilegal atau melanggar hukum.</li>
                                    <li>Mencoba merusak, mengganggu, atau memodifikasi sistem keamanan platform.</li>
                                    <li>Mengunggah konten yang melanggar hak kekayaan intelektual orang lain.</li>
                                    <li>Menyebarluaskan soal ujian atau materi rahasia tanpa izin resmi.</li>
                                </ul>

                                <h2 class="h5 fw-bold text-dark mt-4 mb-3">4. Hak Kekayaan Intelektual</h2>
                                <p>
                                    Seluruh desain, kode sumber, logo, dan konten yang disediakan oleh MulaiAja adalah milik eksklusif kami atau pemberi lisensi kami. Materi ujian yang diunggah oleh Administrator adalah tanggung jawab penuh Administrator yang bersangkutan.
                                </p>

                                <h2 class="h5 fw-bold text-dark mt-4 mb-3">5. Privasi dan Data</h2>
                                <p>
                                    Kami menghargai privasi Anda. Data yang dikumpulkan akan digunakan semata-mata untuk keperluan operasional platform, penilaian ujian, dan pengembangan layanan. Kami tidak akan membagikan data pribadi Anda kepada pihak ketiga tanpa persetujuan Anda, kecuali diwajibkan oleh hukum.
                                </p>

                                <h2 class="h5 fw-bold text-dark mt-4 mb-3">6. Perubahan Ketentuan</h2>
                                <p>
                                    Kami berhak untuk mengubah atau memperbarui Syarat dan Ketentuan ini kapan saja tanpa pemberitahuan sebelumnya. Penggunaan berkelanjutan Anda atas platform setelah perubahan tersebut akan dianggap sebagai persetujuan terhadap ketentuan baru.
                                </p>
                            </div>

                            <div class="mt-5 pt-3 border-top">
                                <a class="btn btn-primary w-100 py-3 fw-bold" href="javascript:void(0)" onclick="window.history.back()">
                                    Kembali ke Pendaftaran
                                </a>
                            </div>
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
