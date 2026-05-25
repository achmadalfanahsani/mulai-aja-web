# Masalah: Filter Paket Soal Mengarahkan ke "Semua Paket Soal"

## Deskripsi Masalah
Saat ini, ketika pengguna berada di halaman khusus tipe paket soal (misalnya "Paket Soal Isian Singkat") dan melakukan filter (pencarian nama atau status), hasilnya justru diarahkan ke halaman "Semua Paket Soal". Hal ini terjadi karena parameter `type` hilang saat formulir filter dikirimkan.

## Analisis Teknis
1. Di `sidebar-nav.blade.php`, menu navigasi menggunakan parameter `type` (misal: `?type=essay`) untuk membedakan kategori paket soal.
2. Di `resources/views/question_packages/index.blade.php`, formulir filter menggunakan metode `GET` dan mengarah ke `route('question-packages.index')` tanpa menyertakan parameter `type`.
3. Saat tombol filter ditekan, browser hanya mengirimkan parameter yang ada di dalam input form (`q` dan `status`), sehingga parameter `type` yang ada di URL sebelumnya menjadi hilang.

## Rencana Penyelesaian
Untuk memperbaiki ini, kita perlu memastikan parameter `type` tetap terjaga saat filter dilakukan.

### Langkah-langkah:
1. **Tambahkan Input Hidden di Form Filter:**
   Buka file `resources/views/question_packages/index.blade.php`. Di dalam `<form>`, tambahkan input tersembunyi (*hidden input*) yang mengambil nilai `type` dari request saat ini.
   ```html
   @if(request()->has('type'))
       <input type="hidden" name="type" value="{{ request('type') }}">
   @endif
   ```

2. **Verifikasi di Controller (Opsional):**
   Pastikan `QuestionPackageController@index` tetap memproses parameter `type` dengan benar meskipun ada filter `q` atau `status`. (Berdasarkan pengamatan awal, controller seharusnya sudah menangani ini jika menggunakan `request()->all()` atau pengecekan per parameter).

3. **Uji Coba:**
   - Masuk ke menu "Paket Soal Isian Singkat".
   - Lakukan pencarian nama paket.
   - Pastikan URL tetap mengandung `type=essay` dan halaman tidak berpindah ke "Semua Paket Soal".

## Catatan untuk Junior Programmer
- Ingat bahwa formulir dengan `method="GET"` akan menimpa seluruh *query string* di URL dengan data yang ada di dalam input form.
- Menggunakan `<input type="hidden">` adalah cara paling sederhana untuk "mengoper" data yang sudah ada di URL ke pengiriman form berikutnya tanpa menampilkannya ke pengguna.
