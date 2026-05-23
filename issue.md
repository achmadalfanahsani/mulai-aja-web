# Project Issues & Action Plan Tracking

Dokumen ini melacak status pengerjaan berbagai isu yang ditemukan pada proyek **MulaiAja**.

---

## 1. Issue: Fix 'togglePublish' Undefined Method Error

### Deskripsi
Muncul *Internal Server Error* saat mengubah status `QuestionPackage` karena method `togglePublish()` belum didefinisikan di `QuestionPackageController`.

### Rencana Aksi
- [x] **Update Controller:** Tambahkan method `togglePublish` di `app/Http/Controllers/QuestionPackageController.php`.
- [x] **Otorisasi & Validasi:** Pastikan hanya pembuat atau admin yang bisa mengubah status, dan minimal ada 1 soal sebelum publish.
- [x] **Update Route:** Daftarkan rute POST di `routes/web.php`.
- [x] **Update View:** Pastikan tombol status di `resources/views/question_packages/index.blade.php` mengarah ke rute yang benar.
- [x] **Verifikasi:** Uji perubahan status draft <-> published di UI.

### Status: ✅ SELESAI
*Method telah diimplementasikan dengan Route Model Binding dan validasi minimal soal.*

---

## 2. Issue: Syntax Error di ExamController

### Deskripsi
Ditemukan `ParseError: syntax error, unexpected token "public"` pada `app/Http/Controllers/ExamController.php` baris 44. Hal ini disebabkan karena kurangnya tanda kurung kurawal penutup (`}`) untuk metode `index()`.

### Rencana Aksi
- [x] **Identifikasi Lokasi:** Cari metode `index()` yang tidak tertutup di `app/Http/Controllers/ExamController.php`.
- [x] **Perbaikan Syntax:** Tambahkan penutup `}` sebelum metode `history()` dimulai.
- [x] **Verifikasi:** Jalankan `php -l` untuk memastikan tidak ada syntax error.
- [x] **Uji Akses:** Pastikan halaman `/exams` dapat diakses tanpa error.

### Status: ✅ SELESAI
*Syntax error telah diperbaiki dan file telah divalidasi dengan `php -l`.*

---

## 3. Issue: Class "QuestionAttempt" Not Found di QuestionPackageController

### Deskripsi
Saat mengakses halaman hasil pengerjaan (`/question-packages/{id}/results`), muncul error *Internal Server Error* dengan pesan:
`Class "App\Http\Controllers\QuestionAttempt" not found`.

Ini terjadi karena model `QuestionAttempt` digunakan di dalam `QuestionPackageController` (pada method `results`), namun belum di-import menggunakan pernyataan `use` di bagian atas file.

### Rencana Aksi
- [x] **Identifikasi File:** Buka file `app/Http/Controllers/QuestionPackageController.php`.
- [x] **Tambah Import:** Tambahkan `use App\Models\QuestionAttempt;` di bagian atas file.
- [x] **Verifikasi Kode:** Pastikan baris tersebut diletakkan bersama dengan import model lainnya.
- [x] **Pengujian:** Akses rute `/question-packages/{id}/results` dan pastikan halaman dimuat.

### Status: ✅ SELESAI
*Model QuestionAttempt telah di-import di QuestionPackageController dan verifikasi sukses.*
