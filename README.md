# MulaiAja — Platform CBT (Computer Based Test)

Platform ujian berbasis komputer (CBT) yang dibangun menggunakan **Laravel 13** dan template **Codebase Bootstrap 5**. Mendukung manajemen paket soal, kelas, dan pelaksanaan ujian online dengan sistem peran (role) yang lengkap.

---

## Tech Stack

| Komponen       | Teknologi                          |
|----------------|------------------------------------|
| Framework      | Laravel 13 (PHP 8.3+)              |
| Frontend       | Codebase Bootstrap 5 + Vite        |
| Database       | MySQL / SQLite                     |
| Autentikasi    | Custom Auth (Session-based)        |
| Build Tool     | Vite                               |

---

## Fitur Utama

- **Manajemen Paket Soal** — Buat, edit, dan publikasi paket ujian dengan pengaturan durasi, passing score, dan mode acak soal.
- **Manajemen Soal** — Soal pilihan ganda (dengan gambar & penjelasan), serta soal esai. Mendukung opsi jawaban (A–E).
- **Manajemen Kelas** — Guru dan administrator dapat membuat kelas, menambahkan siswa/guru, dan menetapkan paket soal ke kelas.
- **Mesin Ujian (CBT)** — Siswa dapat memulai, mengerjakan, dan menyelesaikan ujian dengan auto-save jawaban secara asinkron.
- **Hasil & Rekap** — Lihat detail jawaban benar/salah beserta skor setelah ujian selesai.
- **Manajemen Pengguna** — Superuser dan Administrator dapat mengelola akun, menetapkan peran, dan menyetujui/menolak pendaftaran.
- **Sistem Peran (Role-Based Access)** — 4 peran: `superuser`, `administrator`, `teacher`, `student`.
- **Tema Antarmuka** — Pengguna dapat mengganti tema warna tampilan (color themes).

---

## Sistem Peran (Roles)

| Peran           | Akses                                                                                                   |
|-----------------|---------------------------------------------------------------------------------------------------------|
| `superuser`     | Akses penuh ke semua fitur, termasuk manajemen pengguna, ujian, dan kelas.                              |
| `administrator` | Manajemen pengguna (approve/reject), paket soal, kelas, dan melihat hasil ujian.                        |
| `teacher`       | Membuat dan mengelola paket soal, kelas, serta melihat hasil ujian.                                     |
| `student`       | Mengikuti ujian dan melihat hasil/riwayat ujian miliknya.                                               |

---

## Struktur Direktori

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── LoginController.php           ← Login & logout
│   │   │   └── RegisterController.php        ← Registrasi akun baru
│   │   ├── Superuser/
│   │   │   └── UserController.php            ← Manajemen user (superuser)
│   │   ├── ClassroomController.php           ← CRUD kelas & member
│   │   ├── DashboardController.php           ← Statistik dashboard
│   │   ├── ExamController.php                ← Mesin CBT (mulai, jawab, submit)
│   │   ├── ProfileController.php             ← Ganti password & tema
│   │   ├── QuestionController.php            ← CRUD soal
│   │   └── QuestionPackageController.php     ← CRUD paket soal & publikasi
│   └── Middleware/
│       ├── EnsureApproved.php                ← Blokir user belum disetujui
│       └── RoleMiddleware.php                ← Guard akses berdasarkan role
├── Models/
│   ├── Classroom.php
│   ├── Question.php
│   ├── QuestionAttempt.php
│   ├── QuestionOption.php
│   ├── QuestionPackage.php
│   ├── QuestionResponse.php
│   └── User.php
│
database/
├── migrations/                               ← 22 file migrasi
├── seeders/
│   ├── DatabaseSeeder.php                    ← Entry point seeder
│   ├── QuestionPackageSeeder.php             ← Seed paket soal & guru
│   └── RoleAndPermissionSeeder.php           ← Seed akun default per role
│
resources/
├── js/
│   └── app.js                                ← JS entry point (Vite)
├── scss/
│   ├── app.scss                              ← SCSS entry point
│   ├── _variables.scss
│   ├── components/
│   ├── layouts/
│   │   ├── _sidebar.scss
│   │   └── _header.scss
│   └── pages/
│       ├── _auth.scss
│       └── _dashboard.scss
└── views/
    ├── layouts/
    │   └── app.blade.php                     ← Layout induk semua halaman
    ├── partials/
    │   ├── head.blade.php
    │   ├── footer.blade.php
    │   ├── error-hero.blade.php
    │   ├── sidebar/
    │   │   ├── sidebar.blade.php
    │   │   ├── sidebar-header.blade.php
    │   │   └── sidebar-nav.blade.php
    │   └── header/
    │       ├── header.blade.php
    │       ├── header-color-themes.blade.php
    │       ├── header-user-dropdown.blade.php
    │       ├── header-search.blade.php
    │       └── header-loader.blade.php
    ├── auth/
    │   ├── login.blade.php
    │   └── register.blade.php
    ├── pages/
    │   ├── dashboard.blade.php
    │   └── terms.blade.php
    ├── profile/
    │   └── change-password.blade.php
    ├── question_packages/                    ← index, create, edit, results
    ├── questions/                            ← index, create, edit
    ├── classrooms/                           ← index, create, edit, show
    ├── exams/                                ← index, attempt, history, results
    ├── admin/users/                          ← index, create (administrator)
    └── errors/                              ← 400, 401, 403, 404, 500, 503
│
routes/
└── web.php
│
vite.config.js
package.json
composer.json
```

---

## Setup Awal

### 1. Pasang dependency PHP
```bash
composer install
```

### 2. Salin & konfigurasi `.env`
```bash
cp .env.example .env
php artisan key:generate
```

Isi koneksi database di `.env`:
```env
DB_CONNECTION=mysql
DB_DATABASE=nama_database
DB_USERNAME=root
DB_PASSWORD=
```

> **Tips:** Proyek juga mendukung SQLite. Ganti `DB_CONNECTION=sqlite` dan pastikan file `database/database.sqlite` sudah ada.

### 3. Jalankan migrasi & seeder
```bash
php artisan migrate --seed
```

### 4. Pasang dependency Node & compile aset
```bash
npm install
npm run dev      # development (hot-reload)
npm run build    # production
```

### 5. Salin folder aset Codebase ke public
```bash
cp -r path/to/codebase/assets public/assets
```

### 6. Jalankan server
```bash
php artisan serve
```

Buka: http://localhost:8000

> **Shortcut:** Gunakan `composer dev` untuk menjalankan server, queue, log watcher, dan Vite secara bersamaan.
> ```bash
> composer dev
> ```

---

## Akun Default (Hasil Seeder)

> ⚠️ **Peringatan:** Segera ganti password akun-akun ini setelah deployment ke production!

| Role            | Email                     | Password   |
|-----------------|---------------------------|------------|
| Superuser       | `superuser@example.com`   | `password` |
| Administrator   | `admin@example.com`       | `password` |
| Teacher         | `teacher@example.com`     | `password` |
| Student         | `student@example.com`     | `password` |

Password default dapat dikustomisasi via variabel `DEFAULT_USER_PASSWORD` di file `.env`.

---

## Route Utama

| Method     | URI                                              | Akses                                      |
|------------|--------------------------------------------------|--------------------------------------------|
| `GET`      | `/dashboard`                                     | Semua role (auth + approved)               |
| `GET`      | `/question-packages`                             | Teacher, Administrator, Superuser          |
| `GET`      | `/question-packages/{id}/results`                | Teacher, Administrator, Superuser          |
| `GET`      | `/questions`                                     | Teacher, Administrator, Superuser          |
| `GET`      | `/classrooms`                                    | Teacher, Administrator, Superuser          |
| `GET`      | `/exams`                                         | Student, Superuser                         |
| `POST`     | `/exams/packages/{id}/start`                     | Student, Superuser                         |
| `GET`      | `/exams/attempt/{id}`                            | Student, Superuser                         |
| `POST`     | `/exams/attempt/{id}/save`                       | Student, Superuser (auto-save)             |
| `POST`     | `/exams/attempt/{id}/submit`                     | Student, Superuser                         |
| `GET`      | `/exams/results/{id}`                            | Student, Teacher, Administrator, Superuser |
| `GET`      | `/superuser/users`                               | Superuser                                  |
| `GET`      | `/admin/users`                                   | Administrator                              |
| `GET`      | `/profile/password`                              | Semua role (auth + approved)               |

---

## Membuat Halaman Baru

1. Buat controller:
```bash
php artisan make:controller NamaController
```

2. Tambahkan route di `routes/web.php` dalam group middleware yang sesuai (`auth`, `approved`, `role:...`).

3. Buat view dengan meng-extend layout:
```blade
@extends('layouts.app')

@section('title', 'Judul Halaman')
@section('page-heading', 'Heading Konten')

@section('content')
    {{-- Konten halaman di sini --}}
@endsection

@push('scripts')
    {{-- Script khusus halaman ini --}}
@endpush
```

4. Tambahkan item menu di `resources/views/partials/sidebar/sidebar-nav.blade.php`.

---

## Konvensi Kode

- **Controller methods:** Gunakan nama standar bahasa Inggris (`index`, `show`, `store`, `update`, `destroy`).
- **Validasi:** Selalu gunakan `$request->validate()` di dalam Controller.
- **Komentar Blade:** Gunakan `{{-- ... --}}`.
- **Flash messages:** Gunakan `session('success')` atau `session('error')`.
- **Middleware route:** Terapkan `auth`, `approved`, dan `role:{nama_role}` pada setiap grup route yang membutuhkan akses terbatas.
- **Formatting PHP:** Ikuti standar PSR-12 dan konvensi Laravel.