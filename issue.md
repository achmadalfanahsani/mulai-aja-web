# 🎯 Feature Request: Manajemen Kumpulan Soal Pilihan Ganda (CBT)

## 📌 Deskripsi Fitur
Proyek membutuhkan penambahan fitur **Manajemen Soal (Computer Based Test)** yang memungkinkan pembuatan paket soal pilihan ganda. Fitur ini dirancang khusus untuk soal pilihan ganda dengan 5 opsi jawaban (A, B, C, D, E) dan dilengkapi dengan sistem pengacakan, timer, serta evaluasi otomatis.

## ⚙️ Mekanisme & Kebutuhan Sistem
1. **Paket Soal (Question Packages):**
   * Pengguna (Guru/Admin) dapat membuat paket soal baru.
   * Parameter paket mencakup: Nama Paket, Deskripsi, Durasi Pengerjaan (menit), dan Nilai Kelulusan.
   * Mekanisme otomatis mengacak urutan soal dan urutan opsi jawaban setiap kali paket dikerjakan.
2. **Soal & Opsi (Questions & Options):**
   * Setiap soal dapat berupa teks biasa dan dapat dilampirkan **gambar penjelas** (bersifat opsional/nullable).
   * Terdapat 5 opsi jawaban pasti (A, B, C, D, E) dengan 1 kunci jawaban yang benar.
3. **Ujian & Mengerjakan (Exam Attempts):**
   * Siswa dapat melihat daftar paket soal di *dashboard* mereka.
   * Saat mengerjakan, siswa dibatasi oleh durasi waktu pengerjaan.
   * **Auto-Submit:** Jawaban akan ter-*submit* otomatis saat waktu habis tanpa harus menekan tombol simpan.
4. **Hasil & Evaluasi (Results & Reporting):**
   * Setelah ujian selesai, sistem menampilkan halaman hasil yang berisi:
     - Skor akhir yang diperoleh.
     - Total waktu yang dihabiskan.
     - Rincian jumlah jawaban benar, salah, dan tidak dijawab.
     - Review soal yang dijawab salah lengkap dengan indikasi kunci jawaban yang benar.

---

## 🛠️ Rencana Implementasi (Tahapan Pengerjaan)
Untuk menjaga struktur *best practices* Laravel, pengerjaan dibagi menjadi 4 tahap (*Phases*):

### Phase 1: Database, Models & Seeders
* Membuat *migrations* untuk tabel `users` (tambah *role*), `question_packages`, `questions`, `question_options`, `question_attempts`, dan `question_responses`.
* Membuat Model Eloquent dengan relasi lengkap.
* Menyiapkan *Factories* dan *Seeders* untuk men-generate data *dummy* sehingga mempercepat proses pengujian UI.

### Phase 2: Back-end Logic & Routing
* Membuat `QuestionPackageController` untuk manajemen (CRUD) paket soal oleh admin/guru.
* Membuat `QuestionController` untuk mengatur rincian soal beserta kunci jawaban.
* Membuat `ExamController` untuk memproses logika saat siswa mulai ujian, mencatat *timestamp*, dan melakukan perhitungan skor akhir (grading).
* Mendaftarkan rute (routes) yang sesuai di `routes/web.php`.

### Phase 3: Antarmuka Pengguna (Views)
Menggunakan referensi komponen dari template **Codebase Bootstrap** (`_codebase-source-html`):
* Menambahkan menu **Manajemen Soal** di sidebar kiri (sidebar utama).
* Membangun tampilan daftar paket (menggunakan desain *card/grid*).
* Membangun form pembuatan soal dengan elemen input *radio button* untuk memilih kunci jawaban.
* Membangun antarmuka pengerjaan ujian yang bersih, fokus, dan responsif.
* Membangun halaman laporan skor.

### Phase 4: JavaScript Timer & Auto-Submit
* Mengimplementasikan *countdown timer* menggunakan Vanilla JavaScript.
* Menyinkronkan perhitungan waktu dengan data *timestamp* server untuk mencegah manipulasi.
* Menjalankan *trigger* `form.submit()` secara otomatis ketika hitung mundur mencapai angka nol.

---

> **Note untuk Junior Programmer:** 
> Pastikan setiap kode yang dibuat menjaga *clean code*, memisahkan logika *controller* dengan tampilan *view*, dan memanfaatkan fitur bawaan Laravel (seperti Form Requests untuk validasi) agar *codebase* tetap mudah dikelola di masa mendatang!
