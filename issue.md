# Analisis & Solusi Bug: Reset Tema Otomatis Setelah Mengakses Halaman Error

Halo! Jika kamu sedang mempelajari bagaimana sistem tema di proyek kita bekerja, dokumen ini akan menjelaskan secara detail mengapa tema yang sudah dipilih oleh pengguna tiba-tiba ter-reset ke default ketika mengakses halaman error (seperti `error/400`), serta bagaimana cara memperbaikinya dengan sangat mudah.

---

## 🕵️‍♂️ Penjelasan Masalah (The Root Cause)

Untuk memahami masalah ini, mari kita bedah bagaimana sistem tema di template **Codebase Bootstrap 5** kita bekerja:

### 1. Bagaimana Tema Disimpan?
Ketika seorang pengguna memilih salah satu tema warna di dashboard (misalkan tema *Elegance*, *Pulse*, *Flat*, dll.), sistem akan menyimpan pilihan tersebut di dalam penyimpanan lokal browser yang disebut **`localStorage`** dengan kunci (`key`) bernama **`codebaseColorTheme`**. 
Isi dari penyimpanan ini adalah path file CSS tema yang aktif, contohnya: `assets/css/themes/flat.min.css`.

### 2. Apa Peran Class `remember-theme`?
Agar sistem tahu bahwa kita ingin mempertahankan tema di setiap perpindahan halaman, kita menggunakan class khusus bernama `remember-theme` pada tag paling luar HTML (`<html>`).
* **`setTheme.js`** (berjalan saat halaman pertama kali dimuat): Memeriksa apakah `<html>` memiliki class `remember-theme`. Jika **ada**, ia akan mengambil path CSS dari `localStorage` dan menerapkannya sebelum halaman selesai dimuat agar tidak terjadi efek kedipan (*flash*).
* **`codebase.app.min.js`** (script utama template): Memeriksa class `remember-theme`. Jika **ada**, ia akan mendengarkan klik pada tombol pemilih tema dan memperbarui `localStorage` setiap kali pengguna mengubah tema.

### 3. Mengapa Reset Terjadi Saat Mengakses Halaman Error?
Halaman error di proyek kita (seperti `error/400`, `error/404`, dll.) menggunakan layout terpisah yang terletak di:
`resources/views/layouts/error.blade.php`

Jika kita buka file tersebut, struktur tag HTML-nya didefinisikan seperti ini:
```html
<!doctype html>
<html>
```
Perhatikan baik-baik: **Tag `<html>` di atas tidak memiliki class `remember-theme`!**

Ketika pengguna mengakses halaman error:
1. Browser memuat layout `error.blade.php`.
2. Script pemilih tema `setTheme.js` berjalan, tetapi karena tag `<html>` tidak memiliki class `remember-theme`, ia **tidak memuat** tema yang disimpan di `localStorage`. Hasilnya, halaman error tampil dengan tema bawaan (default).
3. Script utama `codebase.app.min.js` kemudian berjalan. Ketika script ini mendeteksi bahwa tag `<html>` **tidak memiliki** class `remember-theme`, ia menyimpulkan: *"Oh, halaman/aplikasi ini tidak ingin mengingat tema pilihan pengguna!"*.
4. Akibat kesimpulan tersebut, `codebase.app.min.js` melakukan pembersihan (*cleanup*) dengan **menghapus** data tema yang tersimpan di `localStorage` menggunakan perintah:
   `localStorage.removeItem("codebaseColorTheme")`
5. Ketika pengguna menekan tombol kembali ke `/dashboard`, browser kembali memuat layout dashboard yang *sebenarnya* memiliki class `remember-theme`. Namun, karena data tema di `localStorage` telah dihapus secara permanen saat berada di halaman error tadi, dashboard terpaksa dimuat menggunakan tema default!

---

## 🛠️ Solusi Penyelesaian (The Solution)

Solusinya sangat sederhana! Kita hanya perlu memberi tahu halaman error agar ikut mendukung fitur penyimpanan tema. Caranya adalah dengan menambahkan atribut bahasa (`lang`) dan class `remember-theme` ke tag `<html>` di file layout error.

### File yang Harus Diubah:
[layouts/error.blade.php](file:///Users/achmadalfanahsani/Documents/Coding%20Skill/Personal/mulai-aja-website/resources/views/layouts/error.blade.php)

### 🔴 Sebelum Perbaikan (Baris 2):
```html
<!doctype html>
<html>
```

### 🟢 Setelah Perbaikan:
```html
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="remember-theme">
```

---

## ✨ Manfaat Tambahan dari Solusi Ini

Dengan melakukan perbaikan kecil di atas, kita mendapatkan dua keuntungan sekaligus:
1. **Bug Teratasi:** Tema pilihan pengguna tidak akan pernah ter-reset lagi karena `codebase.app.min.js` tidak akan menghapus data tema dari `localStorage` saat berada di halaman error.
2. **Desain Konsisten (User Experience Lebih Baik):** Halaman error kita sekarang akan tampil cantik dan serasi dengan menggunakan tema warna yang sedang aktif dipilih oleh pengguna, alih-alih mendadak berubah ke tema default yang kaku!

---

## 🚀 Langkah Uji Coba (Verification)
Untuk memastikan perbaikan ini sukses, ikuti langkah berikut:
1. Jalankan aplikasi di lokal (`php artisan serve` dan `npm run dev`).
2. Masuk ke halaman `/dashboard`.
3. Buka dropdown pemilih tema (ikon kuas lukis di pojok kanan atas) dan pilih salah satu tema warna (misalnya merah/hijau/biru).
4. Akses halaman error secara langsung melalui URL: `http://localhost:8000/error/400`.
5. Perhatikan: Halaman error sekarang tampil dengan tema warna yang kamu pilih!
6. Klik tombol **"Back to App"** atau kembali ke `/dashboard`.
7. **Berhasil!** Tema pilihanmu tetap aktif dan tidak kembali ke tema default.
