# Masalah: Dropdown Profil Header Kurang Informatif dan Tidak Sinkron

## Deskripsi Masalah
Saat ini, dropdown profil di bar header masih menggunakan data statis/placeholder untuk beberapa menu (seperti Inbox, Invoices) yang tidak ada dalam aplikasi "MulaiAja". Selain itu, informasi pengguna yang ditampilkan sangat minim (hanya nama) dan aksi logout belum terhubung dengan rute yang benar.

## Analisis Teknis
1. File yang bertanggung jawab adalah `resources/views/partials/header/header-user-dropdown.blade.php`.
2. Menu "Inbox", "Invoices", dan "Settings" saat ini tidak fungsional dan tidak relevan dengan kebutuhan aplikasi CBT MulaiAja.
3. Informasi peran (*role*) pengguna (Superuser, Administrator, Teacher, Student) tidak ditampilkan, padahal ini penting untuk konteks navigasi.
4. Rute logout masih menggunakan placeholder `#` bukannya `route('logout')`.

## Rencana Penyelesaian
Kita akan merombak isi dropdown agar lebih bersih, informatif, dan fungsional sesuai dengan ekosistem MulaiAja.

### Langkah-langkah:
1. **Pembersihan Menu Tidak Relevan:**
   Hapus menu "Inbox", "Invoices", dan "Settings" (side overlay) karena aplikasi saat ini tidak menggunakan fitur tersebut.

2. **Penambahan Informasi Role:**
   Tampilkan label peran pengguna di bawah nama pengguna menggunakan Badge Bootstrap agar lebih jelas siapa yang sedang login.

3. **Sinkronisasi Rute Logout:**
   Pastikan link "Sign Out" dan form logout tersembunyi mengarah ke `route('logout')`.

4. **Penyesuaian Visual:**
   - Gunakan avatar default (icon user) yang lebih menarik atau inisial nama.
   - Tambahkan informasi email jika perlu untuk membedakan akun.

5. **Struktur Baru yang Diusulkan:**
   - **Header Info:** Nama (Bold) + Badge Role + Email (Kecil/Muted).
   - **Menu Aksi:**
     - Dashboard (Kembali ke halaman utama).
     - Ganti Password (Jika sudah ada rutenya, atau placeholder yang rapi).
     - Divider.
     - Sign Out (Logout).

## Catatan untuk Junior Programmer
- Gunakan `@if(auth()->check())` atau `@auth` untuk membungkus logika akses data user.
- Manfaatkan helper Laravel seperti `ucfirst(auth()->user()->role)` untuk memformat tampilan role.
- Pastikan form logout menyertakan `@csrf` agar tidak ditolak oleh Laravel.
- Gunakan class-class utility dari Bootstrap 5 (seperti `fw-bold`, `fs-sm`, `text-muted`) untuk memoles tampilan tanpa menulis CSS tambahan.
