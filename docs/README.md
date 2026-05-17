# MulaiAja — Project Documentation Hub

Selamat datang di pusat dokumentasi proyek **MulaiAja** (Laravel 13 + Codebase Bootstrap 5). Direktori ini berisi seluruh rekaman arsitektur, keputusan teknis, panduan instalasi, dan riwayat pembaruan kode proyek.

---

## 📁 Struktur Direktori Dokumentasi

Berikut adalah fungsi dan struktur direktori dokumentasi yang diterapkan:

```text
docs/
├── README.md               ← Hub Utama / Indeks Dokumentasi
├── architecture/           ← Diagram arsitektur, struktur database (ERD), dan alur data
├── api/                    ← Spesifikasi API Endpoint (OpenAPI/Swagger jika ada)
├── deployment/             ← Panduan deployment ke staging dan production
├── decisions/              ← Architecture Decision Records (ADR) untuk mencatat keputusan teknis
├── setup/                  ← Panduan instalasi dan setup lokal lebih detail
└── walkthrough/            ← Rekaman tinjauan kode, rencana aksi, dan penyelesaian isu
```

---

## 🎯 Navigasi Dokumentasi Walkthrough

Untuk tinjauan mendalam mengenai proses refactoring inisialisasi proyek, silakan ikuti dokumen berikut secara berurutan:

1. **[01-project-review.md](file:///Users/achmadalfanahsani/Documents/Coding%20Skill/Personal/mulai-aja-website/docs/walkthrough/01-project-review.md)**
   Tinjauan kode awal, temuan masalah (*anti-patterns*), penjelasan dampak keamanan, serta analisis konflik integrasi CSS Framework (Tailwind vs Bootstrap).

2. **[02-architecture-plan.md](file:///Users/achmadalfanahsani/Documents/Coding%20Skill/Personal/mulai-aja-website/docs/walkthrough/02-architecture-plan.md)**
   Detail rencana arsitektur baru, alasan peniadaan Tailwind CSS demi stabilitas Bootstrap, keputusan standardisasi routing, dan penyederhanaan komponen layout.

3. **[03-github-issues-plan.md](file:///Users/achmadalfanahsani/Documents/Coding%20Skill/Personal/mulai-aja-website/docs/walkthrough/03-github-issues-plan.md)**
   Penyelarasan rencana dengan GitHub Issues (Issue #1), langkah-langkah implementasi (*action plan*), status pengerjaan, serta pembuktian hasil verifikasi teknis.

---

## 💡 Standar Pembaruan Dokumentasi
* Setiap ada keputusan arsitektur besar yang disepakati oleh tim, buatlah satu dokumen ADR baru di folder `docs/decisions/` menggunakan format penomoran (misal: `0001-judul-keputusan.md`).
* Selalu perbarui berkas arsitektur atau panduan setup jika terjadi perubahan dependensi inti pada proyek.
