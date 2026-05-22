# Issue: Syntax Error di ExamController

## Deskripsi
Ditemukan `ParseError: syntax error, unexpected token "public"` pada `app/Http/Controllers/ExamController.php` baris 44.

Hal ini disebabkan karena kurangnya tanda kurung kurawal penutup (`}`) untuk metode `index()`, sehingga kode di bawahnya (yaitu metode `history()`) tidak terbaca dengan benar oleh PHP.

## Rencana Pengerjaan (Untuk Junior Programmer)
1. Buka file `app/Http/Controllers/ExamController.php`.
2. Temukan metode `index()` yang berada di sekitar baris 18.
3. Pastikan metode `index()` tersebut ditutup dengan benar oleh tanda kurung kurawal `}` sebelum metode `history()` dimulai.
4. Simpan perubahan dan lakukan pengujian dengan mengakses kembali route `/exams`.

## Verifikasi
- Pastikan tidak ada lagi error `ParseError` saat mengakses halaman `/exams`.
- Pastikan daftar ujian dan riwayat pengerjaan ujian dapat ditampilkan dengan normal.
