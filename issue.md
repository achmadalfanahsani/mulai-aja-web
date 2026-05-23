# Planning: Implementasi Filter pada `QuestionPackage`

## Deskripsi
Implementasikan fitur pencarian dan filter pada halaman daftar `QuestionPackage` (route `/question-packages`). Saat ini, daftar paket ditampilkan tanpa kemampuan untuk melakukan pencarian atau pemfilteran, yang menyulitkan admin/guru ketika jumlah paket sudah banyak.

## Target
1.  Menambahkan formulir filter pada `resources/views/question_packages/index.blade.php`.
2.  Memperbarui `QuestionPackageController@index` untuk menangani parameter request pencarian dan filter.

## Spesifikasi Filter
- **Pencarian:** Cari berdasarkan nama paket.
- **Filter Berdasarkan Status:** (Jika ada field status, tambahkan opsi filter aktif/non-aktif).
- **Pagination:** Pastikan filter tetap terjaga saat pindah halaman (menggunakan `withQueryString()`).

## Langkah Pengerjaan
1.  **Backend (`QuestionPackageController`):**
    - Ambil data `Request $request`.
    - Buat query builder untuk model `QuestionPackage`.
    - Tambahkan klausa `where` jika `$request->filled('q')` (search).
    - Tambahkan klausa `where` untuk filter status jika diperlukan.
    - Gunakan `paginate(10)->withQueryString()`.
    - Kirim data ke view.

2.  **Frontend (`index.blade.php`):**
    - Tambahkan section `<form action="{{ route('question-packages.index') }}" method="GET">` di atas tabel.
    - Tambahkan `input` untuk pencarian (`name="q"`) dan `select` untuk filter status.
    - Pastikan nilai input diisi dengan `request('...')` untuk menjaga status setelah submit.
    - Pastikan tombol submit memiliki icon filter.

## Contoh Referensi
Lihat implementasi pada:
- `App\Http\Controllers\Superuser\UserController@index`
- `resources/views/superuser/users/index.blade.php`

---
*Catatan untuk Junior Developer:*
- Pastikan untuk selalu menggunakan `withQueryString()` pada hasil pagination agar filter tidak hilang saat navigasi halaman.
- Gunakan `Blade` directive untuk menjaga state form seperti `request('q')`.
- Ikuti konsistensi styling Bootstrap 5 yang digunakan pada template Codebase.
