# Masalah: Sidebar "Paket Soal Pilihan Ganda" Tetap Aktif Saat Mengakses "Semua Paket Soal"

## Deskripsi Masalah
Saat ini, ketika pengguna mengakses menu "Semua Paket Soal", item sidebar "Paket Soal Pilihan Ganda" juga terlihat aktif. Hal ini terjadi karena logika penentuan status `active` di sidebar kurang spesifik dalam membedakan antara halaman daftar umum (tanpa filter tipe) dan halaman daftar kategori tertentu.

## Analisis Teknis
1. Di `sidebar-nav.blade.php`, logika penentuan status aktif menggunakan variabel `$isMultipleChoiceActive`.
2. Definisi `$isMultipleChoiceActive` adalah:
   ```php
   $isMultipleChoiceActive = ($currentType === 'multiple_choice') || ($packageType === 'multiple_choice');
   ```
3. Variabel `$packageType` mengambil nilai dari `$questionPackage->package_type` atau `$package->package_type`.
4. Masalahnya, ketika di halaman "Semua Paket Soal", variabel `$package` kemungkinan besar terdefinisi (misalnya dari perulangan daftar paket atau variabel sisa dari view), dan jika item pertama atau variabel tersebut memiliki tipe `multiple_choice`, maka sidebar tersebut akan ikut aktif.
5. Selain itu, pada halaman edit atau detail paket soal, kita ingin sidebar kategori yang sesuai tetap aktif, namun pada halaman indeks umum ("Semua Paket Soal"), hanya menu "Semua Paket Soal" yang boleh aktif.

## Rencana Penyelesaian
Kita perlu memperketat logika penentuan status aktif agar hanya aktif jika memang sedang berada di rute yang relevan dan memiliki parameter `type` yang sesuai, atau sedang melihat paket soal dengan tipe tersebut.

### Langkah-langkah:
1. **Perbaiki Logika Penentuan Status Aktif di Sidebar:**
   Buka file `resources/views/partials/sidebar/sidebar-nav.blade.php`.
   Ubah logika `@php` di dalam navigasi manajemen soal.
   
   Kita harus memastikan bahwa jika kita berada di rute `question-packages.index` namun tidak ada parameter `type`, maka semua kategori spesifik harus tidak aktif.

   ```php
   $isPackageIndex = request()->routeIs('question-packages.index');
   $hasType = request()->has('type');
   
   $isMultipleChoiceActive = ($currentType === 'multiple_choice') || (!$hasType && $isPackageIndex ? false : ($packageType === 'multiple_choice'));
   ```
   Atau lebih sederhana: pastikan kategori hanya aktif jika rutenya berkaitan dengan `question-packages` DAN (ada parameter `type` yang cocok ATAU sedang membuka spesifik paket dengan tipe tersebut).

2. **Uji Coba:**
   - Akses "Semua Paket Soal". Pastikan hanya menu tersebut yang aktif.
   - Akses "Paket Soal Pilihan Ganda". Pastikan hanya menu tersebut yang aktif.
   - Buka detail/edit sebuah paket Pilihan Ganda. Pastikan menu "Paket Soal Pilihan Ganda" tetap aktif.

## Catatan untuk Junior Programmer
- Gunakan `request()->routeIs()` untuk mengecek rute saat ini.
- Gunakan `request()->has('type')` untuk mengecek keberadaan parameter di URL.
- Berhati-hatilah saat menggunakan variabel global seperti `$package` di dalam template sidebar, karena isinya bisa bervariasi tergantung halaman yang sedang dibuka.
