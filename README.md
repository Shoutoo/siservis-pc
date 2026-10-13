# SiServis-PC | Sistem Informasi Manajemen & Live Tracking Servis Komputer

Repositori Proyek Ujian Tengah Semester (UTS) Mata Kuliah **Rekayasa Perangkat Lunak**  
Program Studi **D3 Teknik Komputer** (Kelas 24A1 / Semester V)  
Universitas Duta Bangsa Surakarta

- **Nama**: Soffin Halim
- **NIM**: 240104019
- **Tautan Repositori GitHub**: [https://github.com/soffinhalim/siservis-pc](https://github.com/soffinhalim/siservis-pc)
- **Deployment Vercel**: [https://siservis-pc.vercel.app](https://siservis-pc.vercel.app)

---

## 📌 Deskripsi Singkat Proyek
**SiServis-PC** adalah aplikasi berbasis web yang dirancang untuk mendigitalkan proses pencatatan penerimaan unit komputer rusak, memfasilitasi diagnosa oleh teknisi hardware/software, menghadirkan fitur *self-tracking* status pengerjaan secara transparan bagi pelanggan menggunakan kode tiket unik, serta mengotomatisasi kalkulasi estimasi biaya dan durasi perbaikan.

---

## 📁 Struktur Direktori
```text
siservis-pc/
├── api/
│   └── index.php             # Entrypoint serverless Vercel
├── database/
│   └── schema.sql            # Skema DDL dan DML basis data MySQL
├── docs/
│   └── project-charter.md    # Dokumen perencanaan proyek dan SDLC
├── .gitignore                # Berkas filter pelacakan Git
├── index.php                 # Halaman utama aplikasi dan logika bisnis PHP
├── vercel.json               # Konfigurasi runtime PHP untuk Vercel
└── README.md                 # Dokumentasi proyek
```

---

## 🚀 Panduan Menjalankan Sistem Lokal

1. **Pastikan PHP Terpasang**
   ```bash
   php -v
   ```
2. **Jalankan PHP Built-in Server**
   ```bash
   cd siservis-pc
   php -S localhost:8000
   ```
3. **Buka Browser**
   Kunjungi [http://localhost:8000](http://localhost:8000)

## ??? Kebijakan Garansi Servis
<<<<<<< HEAD
Setiap perbaikan hardware disertai garansi pengerjaan 30 hari kalender (Garansi Standar Lab).
=======
Setiap perbaikan hardware disertai garansi pengerjaan 60 hari kalender (Garansi Plus Teknisi).
>>>>>>> conflict-b
