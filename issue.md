
## Issue: UI/UX Improvements pada Halaman Kelola Paket Soal

### Deskripsi
Dibutuhkan beberapa peningkatan visual dan fungsional pada halaman `/question-packages` (Kelola Paket Soal) untuk meningkatkan pengalaman pengguna (UX) dan estetika antarmuka.

Peningkatan yang diperlukan:
1.  **Styling Tombol Aksi:** Mengubah kumpulan tombol aksi menjadi satu grup tombol yang terlihat menyatu.
2.  **Responsivitas Label Status:** Memastikan label status (Draft/Published) tidak terlipat atau berubah ukuran saat layar mengecil.
3.  **Konfirmasi Hapus:** Mengganti konfirmasi browser standar (`onsubmit="return confirm(...)"`) dengan modal konfirmasi Bootstrap yang lebih modern, serupa dengan yang ada di manajemen user.

### Rencana Aksi (Panduan Junior Programmer)

#### 1. Memperbaiki Styling Tombol Aksi
- Buka `resources/views/question_packages/index.blade.php`.
- Temukan elemen `<div class="btn-group">` di dalam kolom aksi.
- Pastikan semua elemen di dalamnya (link dan form) berada di dalam satu container `btn-group` agar terlihat menyatu sebagai satu kesatuan.
- Gunakan class `btn-group` dari Bootstrap 5 untuk merapatkan tombol-tombol tersebut.

#### 2. Memperbaiki Label Status (Published/Draft)
- Temukan bagian kolom Status di tabel.
- Tambahkan inline CSS `white-space: nowrap;` atau gunakan class utilitas Bootstrap (jika tersedia) pada tombol status agar teks "Published" atau "Draft" tetap dalam satu baris dan tidak terpotong saat lebar kolom menyempit.
- Contoh: `<button ... style="white-space: nowrap;">`.

#### 3. Implementasi Modal Konfirmasi Hapus
- Hapus atribut `onsubmit="return confirm(...)"` pada tag `<form>` hapus yang ada saat ini.
- Ubah tombol hapus (`<button type="submit" ...>`) menjadi tombol pemicu modal:
  - Ganti `type="submit"` menjadi `type="button"`.
  - Tambahkan atribut `data-bs-toggle="modal"` dan `data-bs-target="#modal-delete-{{ $package->id }}"`.
- Tambahkan kode HTML Modal di dalam loop `@foreach`, diletakkan setelah tag `</tr>` atau di bagian bawah file.
- Kode modal harus berisi form hapus yang sebenarnya, serupa dengan pola di `resources/views/superuser/users/index.blade.php`.

### Status: ✅ SELESAI
*Peningkatan UI/UX telah diimplementasikan: tombol aksi disatukan, label status diperbaiki, dan modal konfirmasi hapus telah diterapkan.*
