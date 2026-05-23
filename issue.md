# Issue: Perapihan Sidebar dan Pemisahan Paket Soal

## Deskripsi
Sidebar saat ini kurang rapi dan menu manajemen soal perlu dipisah berdasarkan tipe soal agar pengguna lebih mudah mengelola paket ujian. Selain itu, jarak tombol logout perlu disesuaikan agar tampilan lebih nyaman.

## Planning Implementasi

### 1. Perubahan Struktur Menu (Sidebar)
Ubah menu di `resources/views/partials/sidebar/sidebar-nav.blade.php`.
- Hapus menu "Manajemen Soal" yang lama.
- Tambahkan sub-menu baru di bawah CBT & Ujian:
    1. **Paket Soal Pilihan Ganda** (Link ke `question-packages.index` dengan filter `type=multiple_choice`)
    2. **Paket Soal Isian Singkat** (Link ke `question-packages.index` dengan filter `type=essay`)
    3. **Paket Soal Campuran** (Link ke `question-packages.index` dengan filter `type=mixed`)
    4. **Mulai Ujian** (Link ke `exams.index`)

### 2. Penyesuaian Backend (Filter)
- Update `QuestionPackageController@index` untuk menangani parameter `type` baru.
- Logika filter:
    - `multiple_choice`: Filter paket yang *hanya* berisi soal `multiple_choice`.
    - `essay`: Filter paket yang *hanya* berisi soal `essay`.
    - `mixed`: Filter paket yang berisi *kombinasi* keduanya.

### 3. Perubahan Styling
- Cari class CSS untuk tombol logout di sidebar.
- Tambahkan `margin-top` atau `padding-top` agar tombol logout terpisah (ada jarak) dari daftar menu di atasnya.

---

### Panduan Teknis untuk Implementator

**Langkah 1: Update Route/Controller**
- Pastikan route `question-packages.index` bisa menerima parameter query `type`.
- Di `QuestionPackageController@index`, tambahkan logika `switch` atau `if` untuk memfilter koleksi paket soal berdasarkan relasi soalnya.

**Langkah 2: Update View Sidebar**
- Buka `resources/views/partials/sidebar/sidebar-nav.blade.php`.
- Gunakan struktur `<ul>` dan `<li>` yang konsisten dengan template Codebase.
- Gunakan `request()->query('type')` untuk memberikan class `active` pada menu yang sedang dipilih.

**Langkah 3: Styling**
- Jika perlu, tambahkan style khusus di file CSS atau gunakan class utility Bootstrap seperti `mt-4` pada elemen tombol logout.
