# Walkthrough 01: Project Review & Anti-Patterns Identification

Dokumen ini mendokumentasikan analisis awal dari inisialisasi kode proyek **MulaiAja** yang dikombinasikan dengan template Codebase Bootstrap 5. Tinjauan ini berfokus pada efisiensi aset, performa routing, kerapian tata letak, dan kepatuhan terhadap praktik terbaik (*best practices*) Laravel modern.

---

## 🔍 Temuan Utama & Analisis Masalah

Berikut adalah 4 poin utama yang ditemukan selama proses tinjauan kode awal:

### 1. File Source Mentah (`_js` & `_scss`) Terbuka Publik
* **Deskripsi:** Folder `public/assets/` berisi folder `_js` (berisi script JavaScript ES6 mentah) dan `_scss` (berisi style SCSS mentah).
* **Dampak Negatif:**
  * **Keamanan (Security):** Semua berkas di folder `public/` dapat diunduh langsung oleh siapa saja melalui browser. Mempublikasikan kode SCSS/JS mentah dapat memaparkan struktur orisinal template, variabel konfigurasi, dan logika internal program.
  * **Kerapian:** Folder `public` seharusnya hanya berisi berkas hasil kompilasi siap produksi (*compiled production assets*) seperti CSS yang sudah diminifikasi, JS terkompilasi, font, dan gambar.

### 2. Duplikasi dan Konflik Framework CSS (Tailwind vs Bootstrap 5)
* **Deskripsi:** Dependensi `@tailwindcss/vite` terpasang di `package.json`, dan `@import 'tailwindcss';` dimuat di `resources/css/app.css`. Di sisi lain, proyek menggunakan template Codebase yang dibangun 100% di atas **Bootstrap 5** (menggunakan kelas Bootstrap seperti `row`, `col-md-6`, `d-flex`, dll.).
* **Dampak Negatif:**
  * **Clash Utilitas:** Tailwind dan Bootstrap memiliki kelas utilitas dengan nama yang sama (misal: `p-3`, `m-2`, `d-flex`). Memuat keduanya sekaligus akan memicu konflik prioritas CSS (*specificity conflict*) yang membuat komponen visual rusak atau tidak konsisten.
  * **Overhead Ukuran File:** Menggabungkan dua framework besar secara mentah akan menambah beban ukuran halaman web secara signifikan, menurunkan performa PageSpeed.
  * **Browser Reset Bentrok:** Reset style dasar (Preflight Tailwind vs Reboot Bootstrap) akan saling menimpa.

### 3. Statik Rute Closure Menghambat Route Caching
* **Deskripsi:** Pada berkas `routes/web.php`, halaman dashboard dan semua halaman error dimuat menggunakan *closure* (fungsi anonim):
  ```php
  Route::get('/dashboard', function () {
      return view('pages.dashboard');
  })->name('dashboard');
  ```
* **Dampak Negatif:**
  * **Route Caching Gagal:** Laravel menyediakan fitur Route Cache (`php artisan route:cache`) untuk mempercepat pemrosesan routing hingga ratusan kali lipat di server produksi. **Adanya rute closure akan membuat perintah ini error** secara mutlak.
  * **Kerapian Kode:** File rute menjadi penuh dengan fungsi pembungkus yang sebenarnya tidak diperlukan untuk halaman statis.

### 4. Kedalaman Nesting Redundan pada Partials Footer
* **Deskripsi:** Berkas partial footer diletakkan di `resources/views/partials/footer/footer.blade.php`.
* **Dampak Negatif:**
  * Folder `footer/` hanya memiliki satu berkas tunggal (`footer.blade.php`). Hal ini membuat struktur direktori menjadi terlalu dalam (*nested*) tanpa alasan yang kuat.
  * Pemanggilan di layout menjadi lebih panjang: `@include('partials.footer.footer')`.

---

## 💡 Kesimpulan Review
Struktur dekomposisi Blade (pemisahan layout dan partials) yang Anda buat sebenarnya sudah **sangat baik dan modular**. Namun, beberapa kendala pada manajemen aset dan routing di atas merupakan *anti-pattern* yang perlu segera dibenahi sebelum proyek masuk ke tahap pengembangan fitur bisnis agar tidak mempersulit proses deployment ke produksi.
