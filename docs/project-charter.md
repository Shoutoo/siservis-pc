# Project Charter: SiServis-PC

## 1. Identitas Proyek
- **Nama Proyek**: SiServis-PC (Sistem Informasi Manajemen & Tracking Servis Perangkat Komputer)
- **Pengembang**: Soffin Halim (NIM: 240104019)
- **Program Studi**: D3 Teknik Komputer 24A1
- **Mata Kuliah**: Rekayasa Perangkat Lunak

---

## 2. Latar Belakang & Permasalahan
Di lingkungan laboratorium komputer kampus dan unit reparasi komputer mandiri, pencatatan masuknya unit komputer yang rusak masih sering dilakukan secara manual menggunakan buku catatan fisik atau nota kertas. Praktik konvensional ini menimbulkan sejumlah kendala:
1. **Risiko Kehilangan Data**: Nota fisik rentan tercecer, sobek, atau rusak sehingga riwayat kerusakan perangkat sulit dilacak kembali.
2. **Ketiadaan Transparansi Status**: Pelanggan atau civitas akademika tidak dapat mengetahui progres perbaikan perangkat secara mandiri, sehingga harus menanyakan berulang kali kepada teknisi.
3. **Ketidakpastian Biaya & Estimasi Waktu**: Tidak adanya standardisasi kalkulasi biaya jasa dan penggantian suku cadang (spare part) sering memicu kesalahpahaman antara teknisi dan pemilik perangkat.

---

## 3. Tujuan Pengembangan
1. Membangun sistem informasi berbasis web yang memusatkan pencatatan tiket servis, diagnosa kerusakan, dan alur perbaikan perangkat keras/lunak.
2. Menyediakan fitur *live tracking* berbasis kode tiket unik agar pelanggan dapat mengecek status servis (Menunggu Antrean, Diagnosa, Menunggu Sparepart, Proses Servis, Selesai) secara transparan.
3. Mengotomatisasi kalkulasi estimasi biaya perbaikan dan estimasi durasi waktu pengerjaan berdasarkan tingkat keparahan kerusakan dan suku cadang yang diperlukan.
4. Mendukung operasional teknisi dan koordinator laboratorium dalam mengelola penugasan serta riwayat reparasi secara sistematis.

---

## 4. Pengguna dan Pihak Terkait (Stakeholder)
1. **Pelanggan / Pemilik Perangkat (Mahasiswa / Dosen / Umum)**: Mengajukan servis, menerima nota digital/kode tiket, dan memantau status pengerjaan secara mandiri.
2. **Teknisi Hardware/Software**: Menginput hasil diagnosa kerusakan, mencatat suku cadang yang digunakan, mengupdate status pekerjaan, dan mencatat rekomendasi perawatan.
3. **Koordinator / Admin Servis**: Memvalidasi penerimaan unit masuk, mengalokasikan teknisi, mencetak invoice/nota akhir, dan melihat rekapitulasi data servis.

---

## 5. Ruang Lingkup Sistem

### A. Fitur dalam Ruang Lingkup (In-Scope)
- Modul Manajemen Tiket Servis (Pencatatan data pemilik, spesifikasi unit PC/Laptop, dan keluhan awal).
- Modul Tracking Status Real-Time melalui pencarian Nomor Tiket unik.
- Modul Kalkulasi Estimasi Biaya & Durasi Servis berbasis logika algoritma tingkat kerusakan dan komponen pengganti.
- Modul Diagnosa & Update Status Alur Servis oleh teknisi.
- Antarmuka berbasis web responsif yang dapat diakses melalui peramban desktop maupun ponsel pintar.

### B. Di Luar Ruang Lingkup (Out-of-Scope)
- Integrasi payment gateway perbankan otomatis (pembayaran saat ini masih tunai / transfer manual).
- Fitur pelacakan pengiriman kurir fisik antar-jemput perangkat.
- Integrasi otomatisasi pengujian hardware berbasis sensor IoT (dijadwalkan pada pengembangan lanjutan).

---

## 6. Analisis Risiko Awal & Mitigasi
| No | Potensi Risiko | Tingkat Dampak | Rencana Mitigasi |
|---|---|---|---|
| 1 | Duplikasi nomor tiket servis | Tinggi | Menggunakan generator kode unik berbasis format waktu dan *auto-increment* (`SRV-YYYYMM-XXX`). |
| 2 | Kesalahan estimasi biaya sparepart akibat fluktuasi pasar | Sedang | Menyediakan harga acuan standar dalam sistem yang dapat disesuaikan manual oleh teknisi setelah konfirmasi pelanggan. |
| 3 | Kendala aksesibilitas jaringan lokal | Sedang | Mendesain sistem berbasis web ringan dan mendukung deployment cloud (Vercel/Hosting) dengan caching aset statis. |

---

## 7. Pemilihan Model Pengembangan (SDLC)
- **Model yang Dipilih**: **Agile Development (Scrum Framework)**
- **Alasan Pemilihan**:
  1. Kebutuhan sistem penanganan servis bersifat dinamis dan membutuhkan umpan balik cepat dari teknisi di lapangan terkait alur diagnosa.
  2. Pendekatan berbasis sprint memungkinkan perilisan modul fungsional inti (seperti tracking tiket dan kalkulasi biaya) lebih awal untuk diuji pengguna (*Minimum Viable Product*).
  3. Memudahkan adaptasi perubahan spesifikasi basis data maupun penyesuaian aturan estimasi biaya tanpa merombak total tahapan proyek.

---

## 8. Hasil yang Diharapkan
1. Repositori kode sumber yang terkelola dengan baik di GitHub dilengkapi riwayat *commit* bermakna dan manajemen *branch*.
2. Prototipe aplikasi web PHP yang berjalan lancar di lingkungan lokal dan terdeploy pada platform cloud (Vercel).
3. Dokumen analisis kebutuhan perangkat lunak (FR, NFR, Acceptance Criteria).
4. Dokumen pemodelan sistem (Context Diagram, Alur Proses Bisnis, ERD, dan Kamus Data).
