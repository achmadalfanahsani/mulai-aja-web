# Rencana Implementasi: Fitur Soal Tipe Uraian (Essay)

## Deskripsi Masalah
Saat ini sistem hanya mendukung soal pilihan ganda. Diperlukan penambahan tipe soal baru yaitu **Uraian (Essay)**. Mekanisme pengelolaan paket soal tetap sama, namun pada saat pengisian jawaban, siswa akan mengetikkan teks uraian alih-alih memilih opsi A-E.

## Tujuan
1. Mendukung pembuatan soal tipe uraian.
2. Menampilkan kolom input teks pada halaman ujian untuk soal tipe uraian.
3. Menambahkan menu navigasi baru di sidebar untuk akses cepat pengelolaan tipe soal ini.

---

## Rencana Pengerjaan (Panduan Junior Developer/AI)

### 1. Perubahan Database (Migration)
Kita perlu membedakan mana soal pilihan ganda dan mana soal uraian.
- **File:** `database/migrations/xxxx_add_type_to_questions_table.php`
- **Tugas:** Tambahkan kolom `question_type` pada tabel `questions`.
- **Nilai:** `multiple_choice` (default) dan `essay`.
- **Perubahan tabel responses:** Pastikan tabel `question_responses` memiliki kolom `essay_answer` (text/longText) untuk menyimpan jawaban uraian siswa.

### 2. Pembaruan Model (`app/Models/Question.php`)
- Tambahkan konstanta untuk tipe soal:
  ```php
  const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
  const TYPE_ESSAY = 'essay';
  ```
- Tambahkan logic di model untuk mengecek tipe: `isEssay()` dan `isMultipleChoice()`.

### 3. Antarmuka Pengguna (UI/UX)

#### A. Sidebar Menu
- **File:** `resources/views/partials/sidebar/sidebar.blade.php` (atau file sidebar terkait).
- **Tugas:** Tambahkan item menu baru "Soal Uraian" yang mengarah ke daftar paket soal yang difilter atau halaman manajemen terkait.

#### B. Pembuatan Soal
- **File:** `resources/views/questions/create.blade.php`
- **Tugas:** Tambahkan pilihan (dropdown/radio) untuk memilih tipe soal. Jika memilih `essay`, sembunyikan input 5 opsi jawaban menggunakan JavaScript sederhana.

#### C. Halaman Ujian (Exam Attempt)
- **File:** `resources/views/exams/attempt.blade.php`
- **Tugas:** 
    - Cek `$question->question_type`.
    - Jika `multiple_choice`: Tampilkan radio button A-E (seperti yang sudah ada).
    - Jika `essay`: Tampilkan `<textarea>` agar siswa bisa mengetik jawaban.
    - Pastikan fungsi `autoSaveAnswer` juga mengirim data teks uraian ke server.

### 4. Logic Penyimpanan (Controller)
- **File:** `app/Http/Controllers/QuestionController.php` & `ExamController.php`
- **Tugas:** 
    - Sesuaikan fungsi `store` dan `update` soal untuk menangani field `question_type`.
    - Sesuaikan fungsi `saveResponse` di `ExamController` untuk menyimpan ke kolom `essay_answer` jika tipe soal adalah essay.

---

## Langkah Verifikasi
1. Buat soal baru dengan tipe "Uraian" di salah satu paket.
2. Pastikan input pilihan ganda tidak muncul saat membuat soal uraian.
3. Masuk ke halaman ujian sebagai siswa.
4. Pastikan soal tipe uraian menampilkan `textarea` dan jawaban tersimpan secara otomatis saat mengetik (debounce/onblur).
5. Cek di database apakah kolom `essay_answer` terisi dengan benar.

---
*Catatan:* Gunakan class Bootstrap yang konsisten dengan tema **Codebase** agar UI tetap rapi. Untuk referensi styling codebase terdapat pada folder _codebase-source-html
