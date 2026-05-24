# Issue: Perapihan Halaman Exams dan Sidebar

## Deskripsi
Halaman daftar ujian (`/exams`) memerlukan fitur filter untuk mempermudah siswa mencari paket soal. Selain itu, bagian riwayat ujian perlu dioptimalkan dengan pagination AJAX agar pengalaman pengguna lebih mulus. Sidebar juga perlu disesuaikan untuk memberikan jarak yang lebih baik antar menu.

## Planning Implementasi

### 1. Fitur Filter Paket Soal
Tambahkan form filter pada bagian "Ujian CBT yang Tersedia".
- **Backend:**
    - Update `ExamController@index` untuk menerima parameter `q` (nama) dan `type` (tipe paket).
    - Tambahkan logika query `where` pada variabel `$packages`.
- **Frontend:**
    - Tambahkan form di atas daftar paket soal pada `resources/views/exams/index.blade.php`.
    - Input: Text (Nama Paket) dan Select (Tipe: Pilihan Ganda, Isian Singkat, Campuran).

### 2. Pagination AJAX Riwayat Ujian
Optimalkan bagian "Riwayat Ujian Anda".
- **Backend:**
    - Ubah pagination `$attempts` di `ExamController@index` menjadi 5 baris per halaman.
    - Tambahkan pengecekan `if ($request->ajax())` untuk mengembalikan partial view khusus tabel riwayat jika dipanggil via AJAX.
- **Frontend:**
    - Bungkus tabel riwayat ujian dalam sebuah container ID (misal: `#history-container`).
    - Gunakan JavaScript (Vanilla JS atau jQuery) untuk menangani klik link pagination.
    - Lakukan fetch data ke URL pagination dan perbarui isi `#history-container` tanpa refresh halaman.

### 3. Penyesuaian Sidebar
Memberikan jarak pada tombol "Mulai Ujian".
- Buka `resources/views/partials/sidebar/sidebar-nav.blade.php`.
- Cari elemen `<li>` yang membungkus menu "Mulai Ujian".
- Tambahkan class utility CSS seperti `mt-3` atau `mt-4` untuk memberikan margin top.

---

### Panduan Teknis untuk Implementator

**Langkah 1: Sidebar**
- Tambahkan `mt-4` pada `li` menu Mulai Ujian.

**Langkah 2: Controller Filtering**
- Gunakan `$request->query('q')` dan `$request->query('type')`.
- Tipe data `package_type` di database: `multiple_choice`, `essay`, `mixed`.

**Langkah 3: AJAX Pagination**
- Buat file view baru `resources/views/exams/_history_table.blade.php` yang hanya berisi isi tabel dan pagination links.
- Di controller: `return view('exams._history_table', compact('attempts'))->render();` jika request adalah AJAX.
- Di script: `fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })`.
