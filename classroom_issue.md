# 🏫 Rencana Pengembangan: Fitur Kelas (Classroom)

**Prioritas:** 🔵 Sedang
**Tujuan:** Mengatur visibilitas paket soal agar hanya dapat diakses oleh siswa di kelas tertentu, bukan ke seluruh siswa secara global.

---

## 📋 Gambaran Fitur

Sistem saat ini mempublikasikan paket soal ke **semua siswa** jika statusnya `is_published`. Kita akan mengubah ini menjadi sistem berbasis kelas:
1. **Teacher** membuat kelas.
2. **Teacher** memasukkan siswa ke kelas tersebut.
3. **Teacher** mengarahkan paket soal ke kelas tertentu.
4. **Student** hanya bisa melihat soal yang ditugaskan ke kelasnya.

---

## 🛠️ Langkah-Langkah Teknis (Step-by-Step)

### Fase 1: Perubahan Database (Migration)

Kita butuh 3 tabel baru untuk mendukung fitur ini:

1.  **Tabel `classrooms`**: Menyimpan data kelas.
    - `id` (Primary Key)
    - `name` (Nama Kelas, misal: "XII RPL 1")
    - `teacher_id` (Foreign Key ke `users` - Siapa pemilik kelas ini)
    - `timestamps`

2.  **Tabel `classroom_user` (Pivot Siswa)**: Menghubungkan Siswa dengan Kelas.
    - `classroom_id` (Foreign Key ke `classrooms`)
    - `user_id` (Foreign Key ke `users`)

3.  **Tabel `classroom_question_package` (Pivot Tugas)**: Menghubungkan Paket Soal dengan Kelas.
    - `classroom_id` (Foreign Key ke `classrooms`)
    - `question_package_id` (Foreign Key ke `question_packages`)

---

### Fase 2: Pengembangan Model (Eloquent)

1.  **Model `Classroom`**:
    - Relasi `teacher()` -> `belongsTo(User::class)`
    - Relasi `students()` -> `belongsToMany(User::class, 'classroom_user')`
    - Relasi `questionPackages()` -> `belongsToMany(QuestionPackage::class, 'classroom_question_package')`

2.  **Model `User`**:
    - Relasi `classrooms()` -> `belongsToMany(Classroom::class, 'classroom_user')` (Untuk Student)
    - Relasi `managedClassrooms()` -> `hasMany(Classroom::class, 'teacher_id')` (Untuk Teacher)

3.  **Model `QuestionPackage`**:
    - Relasi `classrooms()` -> `belongsToMany(Classroom::class, 'classroom_question_package')`

---

### Fase 3: Controller & Logika Bisnis

1.  **`ClassroomController`**:
    - `index()`: Daftar kelas yang dibuat oleh teacher.
    - `create()` / `store()`: Membuat kelas baru.
    - `show()`: Detail kelas (lihat daftar siswa dan daftar soal yang ditugaskan).
    - `addStudent()`: Form/Aksi menambah siswa ke kelas (bisa pakai search email/nama).
    - `assignPackage()`: Aksi memilih paket soal mana yang masuk ke kelas ini.

2.  **Update `ExamController@index` (PENTING)**:
    - Ubah query pengambilan soal untuk Student.
    - **Sebelumnya:** `QuestionPackage::published()->get()`
    - **Sekarang:** Ambil paket soal yang terhubung dengan kelas-kelas di mana Student tersebut menjadi anggotanya.

---

### Fase 4: Antarmuka (UI)

1.  **Menu Sidebar**: Tambahkan menu "Manajemen Kelas" untuk role Teacher, Admin, dan Superuser.
2.  **Halaman Daftar Kelas**: Tabel sederhana berisi Nama Kelas dan Jumlah Siswa.
3.  **Halaman Detail Kelas**:
    - Tab 1: Daftar Siswa (ada tombol hapus siswa dari kelas).
    - Tab 2: Daftar Soal (ada tombol tambah/lepas paket soal).
4.  **Halaman Student**: Di dashboard student, tampilkan nama kelas mereka.

---

## 🔐 Keamanan & Otorisasi (Policy)

1.  **`ClassroomPolicy`**:
    - Teacher hanya boleh mengedit/menghapus kelas yang ia buat sendiri.
    - Admin/Superuser boleh mengelola semua kelas.
2.  **Validasi**:
    - Pastikan saat menambah siswa, user tersebut memang benar memiliki role `student`.

---

## 📝 Catatan untuk Developer Junior

- Gunakan perintah `php artisan make:model Classroom -m` untuk membuat model sekaligus file migration.
- Untuk relasi Many-to-Many, gunakan method `$classroom->students()->attach($userId)` untuk menambah siswa dan `detach($userId)` untuk menghapus.
- Jangan lupa daftarkan route baru di `routes/web.php` di dalam grup middleware `auth`.
- Gunakan `@can` atau `Gate::authorize` untuk menjaga keamanan akses kelas.

---

**Link Terkait:**
- [Laravel Many-to-Many Relationships](https://laravel.com/docs/11.x/eloquent-relationships#many-to-many)
- [Laravel Policies](https://laravel.com/docs/11.x/authorization#creating-policies)
