# 🔐 Issue: Perbaikan Mekanisme Otorisasi untuk Production

**Prioritas:** 🔴 Tinggi (Harus diselesaikan sebelum publish ke public)
**Tanggal:** 26 Mei 2026

---

## 📋 Ringkasan Masalah

Sistem otorisasi (hak akses) pada platform MulaiAja CBT saat ini **belum cukup aman untuk dipublish ke publik**. Berikut rangkuman masalah utama yang ditemukan beserta rencana perbaikannya.

---

## 🔍 Daftar Masalah yang Ditemukan

### Masalah 1: Tidak Ada Laravel Policy — Otorisasi Ditulis Manual di Controller

**Lokasi:** `app/Http/Controllers/QuestionPackageController.php` (baris 170-175)

**Penjelasan Sederhana:**
Saat ini, pengecekan "apakah user boleh mengedit/menghapus paket soal ini?" ditulis langsung di dalam controller pakai fungsi `authorizeAccess()` buatan sendiri. Ini TIDAK menggunakan fitur bawaan Laravel bernama **Policy**.

**Kenapa Bermasalah?**
- Pengecekan otorisasi tersebar di banyak tempat (tidak terpusat).
- Mudah lupa menambahkan pengecekan di method baru.
- Sulit di-maintain dan di-test.
- `QuestionController` (`app/Http/Controllers/QuestionController.php`) **tidak punya pengecekan otorisasi sama sekali** — siapapun yang role-nya teacher/admin/superuser bisa mengedit/menghapus soal milik teacher lain!

**Contoh Kode Bermasalah (`QuestionController.php`):**
```php
// ❌ Tidak ada pengecekan apakah soal ini milik user yang login
public function edit(Question $question) {
    $questionPackage = $question->questionPackage;
    $options = $question->options->pluck('option_text', 'option_label')->toArray();
    return view('questions.edit', compact('questionPackage', 'question', 'options'));
}
```

---

### Masalah 2: Superuser Bisa Mengubah Role Dirinya Sendiri dan Membuat Superuser Lain

**Lokasi:** `app/Http/Controllers/Superuser/UserController.php` (baris 47-56)

**Penjelasan Sederhana:**
Saat ini, Superuser bisa mengubah role user manapun menjadi `superuser`. Tidak ada pengecekan:
- Apakah superuser mencoba mengubah role dirinya sendiri (baris 18 hanya filter di query `index`, bukan di `updateRole`).
- Apakah ada pembatasan siapa yang boleh dijadikan superuser.
- Apakah superuser sedang menghapus dirinya sendiri.

**Contoh Kode Bermasalah:**
```php
// ❌ Tidak ada proteksi: superuser bisa menghapus dirinya sendiri
public function destroy(User $user) {
    $userName = $user->name;
    $user->delete(); // Bagaimana jika $user adalah superuser yang sedang login?
    return back()->with('success', "User {$userName} berhasil dihapus.");
}
```

---

### Masalah 3: Mekanisme Approval Hanya Berlaku untuk Administrator

**Lokasi:** `app/Http/Controllers/Auth/RegisterController.php` (baris 34)

**Penjelasan Sederhana:**
Saat registrasi, hanya role `administrator` yang memerlukan approval. Role `student` dan `teacher` langsung aktif tanpa perlu persetujuan siapapun.

**Kenapa Bermasalah?**
- Siapapun bisa mendaftar sebagai `teacher` dan langsung bisa membuat paket soal.
- Tidak ada mekanisme untuk memverifikasi bahwa orang yang mendaftar sebagai teacher benar-benar seorang guru.
- Dalam konteks produksi, ini membuka celah penyalahgunaan.

**Kode Bermasalah:**
```php
// ❌ Teacher langsung ter-approve tanpa verifikasi
$isApproved = $request->role !== User::ROLE_ADMINISTRATOR;
// Artinya: student = true (langsung aktif), teacher = true (langsung aktif), administrator = false (butuh approval)
```

---

### Masalah 4: RoleMiddleware Hanya Cek Approval untuk Administrator

**Lokasi:** `app/Http/Middleware/RoleMiddleware.php` (baris 30-33)

**Penjelasan Sederhana:**
Middleware hanya mengecek `is_approved` untuk role `administrator`. Jika nanti logika approval diperluas ke role lain (misal teacher), middleware ini akan lolos begitu saja.

**Kode Bermasalah:**
```php
// ❌ Hanya cek approval untuk administrator, bukan semua role
if ($user->role === 'administrator' && !$user->is_approved) {
    Auth::logout();
    return redirect()->route('login')->with('error', '...');
}
```

---

### Masalah 5: ExamController — Potensi IDOR (Insecure Direct Object Reference)

**Lokasi:** `app/Http/Controllers/ExamController.php`

**Penjelasan Sederhana:**
Walaupun method `attempt()`, `saveResponse()`, `submit()`, dan `results()` sudah mengecek `$questionAttempt->user_id !== Auth::id()`, pengecekan ini dilakukan manual per-method. Jika ada developer baru menambahkan endpoint baru terkait attempt, bisa lupa menambahkan pengecekan ini.

**Risiko:** Ini adalah pola **IDOR (Insecure Direct Object Reference)** yang rentan jika tidak konsisten.

---

### Masalah 6: Tidak Ada Rate Limiting pada Login

**Lokasi:** `app/Http/Controllers/Auth/LoginController.php`

**Penjelasan Sederhana:**
Tidak ada pembatasan berapa kali seseorang boleh mencoba login. Tanpa rate limiting, penyerang bisa melakukan **brute force** (mencoba password berkali-kali sampai berhasil).

---

### Masalah 7: Seeder Menggunakan Password Default "password"

**Lokasi:** `database/seeders/RoleAndPermissionSeeder.php`

**Penjelasan Sederhana:**
Semua user default (superuser, admin, teacher, student) menggunakan password `password`. Jika seeder ini dijalankan di production, maka semua akun memiliki password yang sangat mudah ditebak.

---

### Masalah 8: Tidak Ada Middleware EnsureApproved yang Universal

**Penjelasan Sederhana:**
Pengecekan approval saat ini terjadi di dua tempat yang berbeda:
1. `LoginController` (baris 34) — mengecek saat login.
2. `RoleMiddleware` (baris 30) — mengecek saat akses route.

Tapi keduanya punya logika yang sedikit berbeda. Sebaiknya ada satu middleware khusus `EnsureApproved` agar konsisten.

---

## ✅ Rencana Perbaikan (Step-by-Step)

### Fase 1: Perbaikan Kritis (Keamanan Dasar)

#### Langkah 1.1 — Buat Middleware EnsureApproved

**File baru:** `app/Http/Middleware/EnsureApproved.php`

**Apa yang harus dilakukan:**
1. Buat file middleware baru.
2. Di dalam method `handle()`, cek apakah user yang login sudah `is_approved`.
3. Jika belum approved, logout user dan redirect ke halaman login dengan pesan error.
4. Superuser di-skip dari pengecekan ini (karena superuser selalu approved).

**Contoh Kode:**
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureApproved
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Superuser selalu diperbolehkan
        if ($user && $user->isSuperuser()) {
            return $next($request);
        }

        // Cek approval
        if ($user && !$user->is_approved) {
            Auth::logout();
            $request->session()->invalidate();
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum disetujui oleh administrator.');
        }

        return $next($request);
    }
}
```

**Daftarkan di:** `bootstrap/app.php`
```php
$middleware->alias([
    'role' => \App\Http\Middleware\RoleMiddleware::class,
    'approved' => \App\Http\Middleware\EnsureApproved::class,
]);
```

**Pasang di routes:** `routes/web.php` — tambahkan `approved` ke semua grup route yang dilindungi `auth`:
```php
Route::middleware(['auth', 'approved'])->group(function () {
    // ... semua route yang butuh user approved
});
```

---

#### Langkah 1.2 — Perbaiki RoleMiddleware (Hapus Logika Approval)

**File:** `app/Http/Middleware/RoleMiddleware.php`

**Apa yang harus dilakukan:**
1. Hapus blok `if ($user->role === 'administrator' && !$user->is_approved)` (baris 30-33).
2. Biarkan RoleMiddleware HANYA mengecek role, tidak mengecek approval.
3. Approval sudah ditangani oleh `EnsureApproved` middleware.

**Kode Sesudah Perbaikan:**
```php
public function handle(Request $request, Closure $next, ...$roles): Response
{
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();

    if (in_array($user->role, $roles)) {
        return $next($request);
    }

    abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
}
```

---

#### Langkah 1.3 — Perbaiki Registrasi: Teacher Juga Butuh Approval

**File:** `app/Http/Controllers/Auth/RegisterController.php`

**Apa yang harus dilakukan:**
1. Ubah logika `$isApproved` agar teacher juga butuh approval.
2. Hanya student yang langsung ter-approve.

**Kode Sesudah Perbaikan:**
```php
// Hanya student yang langsung aktif
$isApproved = $request->role === User::ROLE_STUDENT;

$user = User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => Hash::make($request->password),
    'role' => $request->role,
    'is_approved' => $isApproved,
]);

if (!$isApproved) {
    return redirect()->route('login')
        ->with('success', 'Registrasi berhasil! Akun Anda sedang menunggu persetujuan.');
}
```

---

#### Langkah 1.4 — Perbaiki Scope pendingApproval di Model User

**File:** `app/Models/User.php`

**Apa yang harus dilakukan:**
Ubah scope `pendingApproval` agar mencakup teacher (bukan hanya administrator).

**Kode Sesudah Perbaikan:**
```php
public function scopePendingApproval(Builder $query): void
{
    $query->whereIn('role', [self::ROLE_ADMINISTRATOR, self::ROLE_TEACHER])
          ->where('is_approved', false);
}
```

---

#### Langkah 1.5 — Tambahkan Rate Limiting pada Login

**File:** `routes/web.php`

**Apa yang harus dilakukan:**
Tambahkan middleware `throttle` pada route login POST.

**Cara Termudah — Tambahkan middleware di route:**
```php
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
// Artinya: maksimal 5 request per 1 menit
```

---

### Fase 2: Implementasi Laravel Policy (Otorisasi Terpusat)

#### Langkah 2.1 — Buat Policy untuk QuestionPackage

**Jalankan perintah artisan:**
```bash
php artisan make:policy QuestionPackagePolicy --model=QuestionPackage
```

**File baru:** `app/Policies/QuestionPackagePolicy.php`

**Isi file:**
```php
<?php

namespace App\Policies;

use App\Models\QuestionPackage;
use App\Models\User;

class QuestionPackagePolicy
{
    /**
     * Superuser dan Administrator boleh melakukan semua hal.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperuser() || $user->isAdministrator()) {
            return true;
        }
        return null; // Lanjut ke method spesifik
    }

    /**
     * Boleh melihat daftar paket soal?
     */
    public function viewAny(User $user): bool
    {
        return $user->isTeacher();
    }

    /**
     * Boleh melihat detail paket soal tertentu?
     */
    public function view(User $user, QuestionPackage $package): bool
    {
        return $user->id === $package->user_id;
    }

    /**
     * Boleh membuat paket soal?
     */
    public function create(User $user): bool
    {
        return $user->isTeacher();
    }

    /**
     * Boleh mengedit paket soal tertentu?
     */
    public function update(User $user, QuestionPackage $package): bool
    {
        return $user->id === $package->user_id;
    }

    /**
     * Boleh menghapus paket soal tertentu?
     */
    public function delete(User $user, QuestionPackage $package): bool
    {
        return $user->id === $package->user_id;
    }

    /**
     * Boleh toggle publish?
     */
    public function togglePublish(User $user, QuestionPackage $package): bool
    {
        return $user->id === $package->user_id;
    }

    /**
     * Boleh melihat hasil pengerjaan siswa?
     */
    public function viewResults(User $user, QuestionPackage $package): bool
    {
        return $user->id === $package->user_id;
    }
}
```

---

#### Langkah 2.2 — Buat Policy untuk Question

**Jalankan perintah artisan:**
```bash
php artisan make:policy QuestionPolicy --model=Question
```

**File baru:** `app/Policies/QuestionPolicy.php`

**Isi file:**
```php
<?php

namespace App\Policies;

use App\Models\Question;
use App\Models\QuestionPackage;
use App\Models\User;

class QuestionPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperuser() || $user->isAdministrator()) {
            return true;
        }
        return null;
    }

    /**
     * Boleh melihat daftar soal dalam paket?
     */
    public function viewAny(User $user, QuestionPackage $package): bool
    {
        return $user->id === $package->user_id;
    }

    /**
     * Boleh membuat soal baru dalam paket?
     */
    public function create(User $user, QuestionPackage $package): bool
    {
        return $user->id === $package->user_id;
    }

    /**
     * Boleh mengedit soal?
     */
    public function update(User $user, Question $question): bool
    {
        return $user->id === $question->questionPackage->user_id;
    }

    /**
     * Boleh menghapus soal?
     */
    public function delete(User $user, Question $question): bool
    {
        return $user->id === $question->questionPackage->user_id;
    }
}
```

---

#### Langkah 2.3 — Buat Policy untuk QuestionAttempt

**Jalankan perintah artisan:**
```bash
php artisan make:policy QuestionAttemptPolicy --model=QuestionAttempt
```

**File baru:** `app/Policies/QuestionAttemptPolicy.php`

**Isi file:**
```php
<?php

namespace App\Policies;

use App\Models\QuestionAttempt;
use App\Models\User;

class QuestionAttemptPolicy
{
    /**
     * Hanya pemilik attempt yang boleh mengakses.
     */
    public function view(User $user, QuestionAttempt $attempt): bool
    {
        return $user->id === $attempt->user_id;
    }

    public function submit(User $user, QuestionAttempt $attempt): bool
    {
        return $user->id === $attempt->user_id;
    }

    public function saveResponse(User $user, QuestionAttempt $attempt): bool
    {
        return $user->id === $attempt->user_id;
    }
}
```

---

#### Langkah 2.4 — Daftarkan Semua Policy

**File:** `app/Providers/AppServiceProvider.php` (atau `AuthServiceProvider` jika ada)

**Tambahkan di method `boot()`:**
```php
use Illuminate\Support\Facades\Gate;
use App\Models\QuestionPackage;
use App\Models\Question;
use App\Models\QuestionAttempt;
use App\Policies\QuestionPackagePolicy;
use App\Policies\QuestionPolicy;
use App\Policies\QuestionAttemptPolicy;

public function boot(): void
{
    Gate::policy(QuestionPackage::class, QuestionPackagePolicy::class);
    Gate::policy(Question::class, QuestionPolicy::class);
    Gate::policy(QuestionAttempt::class, QuestionAttemptPolicy::class);
}
```

---

#### Langkah 2.5 — Ganti Logika Manual di Controller dengan Policy

**File:** `app/Http/Controllers/QuestionPackageController.php`

**Apa yang harus dilakukan:**
1. Hapus method `authorizeAccess()` (baris 170-175).
2. Ganti semua pemanggilan `$this->authorizeAccess(...)` dengan `$this->authorize(...)`.

**Contoh perubahan:**
```php
// ❌ SEBELUM (manual)
private function authorizeAccess(QuestionPackage $package) {
    $user = Auth::user();
    if (!$user->isAdministrator() && !$user->isSuperuser() && $package->user_id !== $user->id) {
        abort(403);
    }
}

public function edit(QuestionPackage $questionPackage) {
    $this->authorizeAccess($questionPackage);
    // ...
}

// ✅ SESUDAH (pakai Policy)
public function edit(QuestionPackage $questionPackage) {
    $this->authorize('update', $questionPackage);
    // ...
}
```

**Daftar perubahan di QuestionPackageController:**

| Method            | Sebelum                              | Sesudah                                       |
|-------------------|--------------------------------------|-----------------------------------------------|
| `edit()`          | `$this->authorizeAccess($package)`   | `$this->authorize('update', $package)`        |
| `update()`        | `$this->authorizeAccess($package)`   | `$this->authorize('update', $package)`        |
| `destroy()`       | `$this->authorizeAccess($package)`   | `$this->authorize('delete', $package)`        |
| `togglePublish()` | `$this->authorizeAccess($package)`   | `$this->authorize('togglePublish', $package)` |
| `results()`       | `$this->authorizeAccess($package)`   | `$this->authorize('viewResults', $package)`   |

---

**File:** `app/Http/Controllers/QuestionController.php`

**Apa yang harus dilakukan:**
Tambahkan pengecekan otorisasi di SEMUA method:

```php
public function index(QuestionPackage $questionPackage) {
    $this->authorize('viewAny', [Question::class, $questionPackage]);
    // ... kode lama tetap
}

public function create(QuestionPackage $questionPackage) {
    $this->authorize('create', [Question::class, $questionPackage]);
    // ... kode lama tetap
}

public function store(Request $request, QuestionPackage $questionPackage) {
    $this->authorize('create', [Question::class, $questionPackage]);
    // ... kode lama tetap
}

public function edit(Question $question) {
    $this->authorize('update', $question);
    // ... kode lama tetap
}

public function update(Request $request, Question $question) {
    $this->authorize('update', $question);
    // ... kode lama tetap
}

public function destroy(Question $question) {
    $this->authorize('delete', $question);
    // ... kode lama tetap
}
```

---

**File:** `app/Http/Controllers/ExamController.php`

**Apa yang harus dilakukan:**
Ganti pengecekan manual `if ($questionAttempt->user_id !== Auth::id())` dengan Policy:

```php
// ❌ SEBELUM
if ($questionAttempt->user_id !== Auth::id()) {
    abort(403, 'Akses ditolak.');
}

// ✅ SESUDAH
$this->authorize('view', $questionAttempt);
```

**Terapkan di method:** `attempt()`, `submit()`, `results()`

**Untuk `saveResponse()`**, karena mengembalikan JSON:
```php
// ❌ SEBELUM
if ($questionAttempt->user_id !== Auth::id() || $questionAttempt->is_completed) {
    return response()->json(['error' => 'Akses ditolak.'], 403);
}

// ✅ SESUDAH
if (Auth::user()->cannot('saveResponse', $questionAttempt) || $questionAttempt->is_completed) {
    return response()->json(['error' => 'Akses ditolak.'], 403);
}
```

---

### Fase 3: Proteksi Superuser

#### Langkah 3.1 — Lindungi Superuser dari Operasi Berbahaya

**File:** `app/Http/Controllers/Superuser/UserController.php`

**Apa yang harus dilakukan:**

**a) Cegah superuser mengubah role dirinya sendiri:**
```php
public function updateRole(Request $request, User $user)
{
    // Proteksi: tidak boleh mengubah role diri sendiri
    if ($user->id === auth()->id()) {
        return back()->with('error', 'Anda tidak dapat mengubah role diri sendiri.');
    }

    // Proteksi: tidak boleh mengubah role superuser lain
    if ($user->isSuperuser()) {
        return back()->with('error', 'Tidak dapat mengubah role superuser lain.');
    }

    $request->validate([
        'role' => 'required|in:student,teacher,administrator', // Hapus 'superuser' dari pilihan
    ]);

    $user->update(['role' => $request->role]);
    return back()->with('success', "Role user {$user->name} berhasil diubah.");
}
```

**b) Cegah superuser menghapus dirinya sendiri:**
```php
public function destroy(User $user)
{
    // Proteksi: tidak boleh menghapus diri sendiri
    if ($user->id === auth()->id()) {
        return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
    }

    // Proteksi: tidak boleh menghapus superuser lain
    if ($user->isSuperuser()) {
        return back()->with('error', 'Tidak dapat menghapus akun superuser.');
    }

    $userName = $user->name;
    $user->delete();
    return back()->with('success', "User {$userName} berhasil dihapus.");
}
```

**c) Cegah superuser mengubah password superuser lain:**
```php
public function updatePassword(Request $request, User $user)
{
    if ($user->isSuperuser() && $user->id !== auth()->id()) {
        return back()->with('error', 'Tidak dapat mengubah password superuser lain.');
    }

    // ... validasi dan update seperti biasa
}
```

---

### Fase 4: Perbaikan Tambahan

#### Langkah 4.1 — Perbarui Seeder dengan Password yang Lebih Aman

**File:** `database/seeders/RoleAndPermissionSeeder.php`

**Apa yang harus dilakukan:**
Gunakan password yang lebih kuat, atau lebih baik, baca dari environment variable:

```php
use Illuminate\Support\Str;

// ✅ Baca dari .env, fallback ke password acak
'password' => Hash::make(env('SUPERUSER_DEFAULT_PASSWORD', Str::random(16))),
```

Atau minimal tambahkan komentar peringatan:
```php
// ⚠️ PERINGATAN: Segera ganti password ini setelah deployment ke production!
'password' => Hash::make('password'),
```

---

#### Langkah 4.2 — Tambahkan Proteksi CSRF Eksplisit

Pastikan semua form sudah menggunakan `@csrf` directive. Ini biasanya sudah ada di Laravel, tapi pastikan tidak ada endpoint POST/PATCH/DELETE yang melewatkan pengecekan CSRF.

---

## 📊 Matriks Hak Akses Optimal per Role

Berikut tabel hak akses yang **direkomendasikan** setelah perbaikan:

| Fitur                                    | Student | Teacher | Administrator | Superuser |
|------------------------------------------|---------|---------|---------------|-----------|
| Login                                    | ✅      | ✅ *    | ✅ *          | ✅        |
| Registrasi (langsung aktif)              | ✅      | ❌      | ❌            | —         |
| Registrasi (butuh approval)              | —       | ✅      | ✅            | —         |
| Dashboard                                | ✅      | ✅      | ✅            | ✅        |
| Ganti Password Sendiri                   | ✅      | ✅      | ✅            | ✅        |
| Lihat Daftar Ujian                       | ✅      | ❌      | ✅            | ✅        |
| Mengerjakan Ujian                        | ✅      | ❌      | ✅            | ✅        |
| Lihat Hasil Ujian Sendiri                | ✅      | ❌      | ✅            | ✅        |
| Buat Paket Soal                          | ❌      | ✅      | ✅            | ✅        |
| Edit Paket Soal (milik sendiri)          | ❌      | ✅      | ✅            | ✅        |
| Edit Paket Soal (milik orang lain)       | ❌      | ❌      | ✅            | ✅        |
| Hapus Paket Soal (milik sendiri)         | ❌      | ✅      | ✅            | ✅        |
| Hapus Paket Soal (milik orang lain)      | ❌      | ❌      | ✅            | ✅        |
| Publish/Unpublish Paket Soal             | ❌      | ✅      | ✅            | ✅        |
| Lihat Hasil Siswa                        | ❌      | ✅ **   | ✅            | ✅        |
| Kelola User                              | ❌      | ❌      | ❌            | ✅        |
| Approve/Reject User                      | ❌      | ❌      | ❌            | ✅        |
| Ubah Role User                           | ❌      | ❌      | ❌            | ✅        |
| Hapus User                               | ❌      | ❌      | ❌            | ✅        |

> `*` = Harus sudah di-approve terlebih dahulu
> `**` = Hanya untuk paket soal miliknya sendiri

---

## 📁 Ringkasan File yang Perlu Diubah/Dibuat

| No | File                                                  | Aksi       | Fase   |
|----|-------------------------------------------------------|------------|--------|
| 1  | `app/Http/Middleware/EnsureApproved.php`               | BUAT BARU  | Fase 1 |
| 2  | `bootstrap/app.php`                                   | EDIT       | Fase 1 |
| 3  | `app/Http/Middleware/RoleMiddleware.php`               | EDIT       | Fase 1 |
| 4  | `routes/web.php`                                      | EDIT       | Fase 1 |
| 5  | `app/Http/Controllers/Auth/RegisterController.php`    | EDIT       | Fase 1 |
| 6  | `app/Models/User.php`                                 | EDIT       | Fase 1 |
| 7  | `app/Policies/QuestionPackagePolicy.php`              | BUAT BARU  | Fase 2 |
| 8  | `app/Policies/QuestionPolicy.php`                     | BUAT BARU  | Fase 2 |
| 9  | `app/Policies/QuestionAttemptPolicy.php`              | BUAT BARU  | Fase 2 |
| 10 | `app/Providers/AppServiceProvider.php`                | EDIT       | Fase 2 |
| 11 | `app/Http/Controllers/QuestionPackageController.php`  | EDIT       | Fase 2 |
| 12 | `app/Http/Controllers/QuestionController.php`         | EDIT       | Fase 2 |
| 13 | `app/Http/Controllers/ExamController.php`             | EDIT       | Fase 2 |
| 14 | `app/Http/Controllers/Superuser/UserController.php`   | EDIT       | Fase 3 |
| 15 | `database/seeders/RoleAndPermissionSeeder.php`        | EDIT       | Fase 4 |

---

## 🧪 Cara Verifikasi (Testing)

Setelah semua perubahan selesai, lakukan pengujian manual berikut:

### Test 1: Registrasi dan Approval
- [ ] Registrasi sebagai student → langsung bisa login
- [ ] Registrasi sebagai teacher → harus menunggu approval
- [ ] Registrasi sebagai administrator → harus menunggu approval
- [ ] Login dengan akun yang belum di-approve → ditolak dengan pesan error

### Test 2: Akses Route Sesuai Role
- [ ] Student mencoba akses `/question-packages` → ditolak (403)
- [ ] Teacher mencoba akses `/superuser/users` → ditolak (403)
- [ ] Administrator mencoba akses `/superuser/users` → ditolak (403)

### Test 3: Kepemilikan Resource
- [ ] Teacher A mencoba edit paket soal milik Teacher B → ditolak (403)
- [ ] Teacher A mencoba hapus soal milik Teacher B → ditolak (403)
- [ ] Administrator bisa edit paket soal milik siapapun → berhasil
- [ ] Superuser bisa edit paket soal milik siapapun → berhasil

### Test 4: Proteksi Superuser
- [ ] Superuser mencoba hapus dirinya sendiri → ditolak
- [ ] Superuser mencoba ubah role dirinya sendiri → ditolak
- [ ] Superuser mencoba menjadikan user lain sebagai superuser → ditolak (opsi tidak tersedia)

### Test 5: Rate Limiting
- [ ] Coba login salah 6 kali berturut-turut → request ke-6 ditolak (429 Too Many Requests)

### Test 6: IDOR pada Exam
- [ ] Student A mencoba akses URL hasil ujian Student B → ditolak (403)
- [ ] Student A mencoba submit jawaban untuk attempt milik Student B → ditolak (403)

---

## ⏱️ Estimasi Waktu Pengerjaan

| Fase                         | Estimasi  | Kesulitan  |
|------------------------------|-----------|------------|
| Fase 1: Keamanan Dasar      | 2-3 jam   | 🟢 Mudah   |
| Fase 2: Laravel Policy       | 3-4 jam   | 🟡 Sedang  |
| Fase 3: Proteksi Superuser   | 1-2 jam   | 🟢 Mudah   |
| Fase 4: Perbaikan Tambahan   | 30 menit  | 🟢 Mudah   |
| Testing                      | 1-2 jam   | 🟢 Mudah   |
| **Total**                    | **8-12 jam** |         |

---

## 📝 Catatan Penting

1. **Urutan pengerjaan penting!** Kerjakan Fase 1 terlebih dahulu karena ini menyangkut keamanan dasar.
2. **Jangan lupa jalankan `php artisan migrate`** jika ada perubahan database.
3. **Backup database** sebelum melakukan perubahan.
4. **Test di environment lokal** sebelum deploy ke production.
5. Policy di Laravel secara otomatis akan teregister jika mengikuti konvensi penamaan (`ModelPolicy` di `app/Policies/`), tapi lebih aman didaftarkan secara eksplisit.
