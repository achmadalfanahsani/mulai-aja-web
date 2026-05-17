# MulaiAja — Laravel 13 + Codebase Bootstrap 5

Panduan setup dan struktur proyek Laravel 13 menggunakan Codebase Bootstrap 5.

---

## Struktur File

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── LoginController.php       ← Login & logout
│   │   │   └── RegisterController.php    ← Registrasi akun baru
│   │   ├── DashboardController.php
│   │   └── ErrorPageController.php
│   └── Middleware/
│       └── RedirectIfAuthenticated.php   ← Redirect user login dari halaman guest
├── Models/
│   └── User.php
│
database/
├── migrations/
│   └── ..._create_users_table.php
└── seeders/
    └── DatabaseSeeder.php                ← Seed admin default
│
resources/
├── js/
│   └── app.js                            ← JS custom (Vite entry)
├── scss/
│   ├── app.scss                          ← SCSS entry point
│   ├── _variables.scss                   ← Override variabel Bootstrap/Codebase
│   ├── components/                       ← Komponen UI (alerts, buttons, dll)
│   ├── layouts/
│   │   ├── _sidebar.scss
│   │   └── _header.scss
│   └── pages/
│       ├── _auth.scss
│       └── _dashboard.scss
└── views/
    ├── layouts/
    │   └── app.blade.php                 ← Layout induk semua halaman
    ├── partials/
    │   ├── head.blade.php                ← Meta, CSS, favicon
    │   ├── sidebar/
    │   │   ├── sidebar.blade.php
    │   │   ├── sidebar-header.blade.php
    │   │   └── sidebar-nav.blade.php
    │   ├── header/
    │   │   ├── header.blade.php
    │   │   ├── header-color-themes.blade.php
    │   │   ├── header-user-dropdown.blade.php
    │   │   ├── header-search.blade.php
    │   │   └── header-loader.blade.php
    │   └── footer/
    │       └── footer.blade.php
    ├── auth/
    │   ├── login.blade.php
    │   └── register.blade.php
    ├── errors/
    │   ├── 404.blade.php
    │   └── 500.blade.php
    └── pages/
        ├── dashboard.blade.php
        ├── profile.blade.php
        ├── inbox.blade.php
        ├── invoices.blade.php
        └── search.blade.php
│
routes/
└── web.php
│
vite.config.js
package.json
```

---

## Setup Awal

### 1. Install PHP dependencies
```bash
composer install
```

### 2. Salin & isi .env
```bash
cp .env.example .env
php artisan key:generate
```

Isi koneksi database di `.env`:
```
DB_DATABASE=nama_database
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Jalankan migrasi & seeder
```bash
php artisan migrate
php artisan db:seed
```

Akun admin default:
- **Email:** `admin@example.com`
- **Password:** `password`

### 4. Install Node & compile aset
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

---

## Membuat Halaman Baru

1. Buat controller:
```bash
php artisan make:controller NamaController
```

2. Tambahkan route di `routes/web.php` dalam group `auth`.

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

- Controller method menggunakan bahasa Inggris (`index`, `show`, `store`, dll.)
- Komentar view Blade menggunakan `{{-- ... --}}`
- Flash message menggunakan `session('success')` atau `session('error')`
- Validasi selalu dilakukan di Controller menggunakan `$request->validate()`