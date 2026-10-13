-- =====================================================================
-- SKEMA BASIS DATA: SiServis-PC
-- Sistem Informasi Manajemen & Tracking Servis Perangkat Komputer
-- Pengembang: Soffin Halim (NIM: 240104019)
-- Mata Kuliah: Rekayasa Perangkat Lunak - D3 Teknik Komputer 24A1
-- =====================================================================

CREATE DATABASE IF NOT EXISTS db_siservis_pc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_siservis_pc;

-- 1. TABEL PENGGUNA (users)
CREATE TABLE IF NOT EXISTS users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('admin', 'teknisi', 'pelanggan') NOT NULL DEFAULT 'teknisi',
    no_telp VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. TABEL PELANGGAN (pelanggan)
CREATE TABLE IF NOT EXISTS pelanggan (
    id_pelanggan INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20) NOT NULL,
    alamat TEXT,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. TABEL PERANGKAT (perangkat)
CREATE TABLE IF NOT EXISTS perangkat (
    id_perangkat INT AUTO_INCREMENT PRIMARY KEY,
    id_pelanggan INT NOT NULL,
    jenis_perangkat ENUM('PC Desktop', 'Laptop', 'All-in-One', 'Server Mini') NOT NULL,
    merk_model VARCHAR(100) NOT NULL,
    nomor_seri VARCHAR(100),
    keluhan_awal TEXT NOT NULL,
    kelengkapan VARCHAR(255) DEFAULT 'Unit + Charger/Kabel Power',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_perangkat_pelanggan FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. TABEL TIKET SERVIS (tiket_servis)
CREATE TABLE IF NOT EXISTS tiket_servis (
    id_tiket INT AUTO_INCREMENT PRIMARY KEY,
    nomor_tiket VARCHAR(30) NOT NULL UNIQUE,
    id_perangkat INT NOT NULL,
    id_teknisi INT NOT NULL,
    tanggal_masuk DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    tingkat_kerusakan ENUM('Ringan', 'Sedang', 'Berat') NOT NULL DEFAULT 'Ringan',
    status_servis ENUM('Antrean Masuk', 'Diagnosa', 'Menunggu Sparepart', 'Proses Servis', 'Selesai', 'Diambil') NOT NULL DEFAULT 'Antrean Masuk',
    estimasi_selesai DATE,
    catatan_diagnosa TEXT,
    biaya_jasa DECIMAL(12, 2) DEFAULT 0.00,
    biaya_sparepart DECIMAL(12, 2) DEFAULT 0.00,
    total_biaya DECIMAL(12, 2) DEFAULT 0.00,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_tiket_perangkat FOREIGN KEY (id_perangkat) REFERENCES perangkat(id_perangkat) ON DELETE CASCADE,
    CONSTRAINT fk_tiket_teknisi FOREIGN KEY (id_teknisi) REFERENCES users(id_user) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- 5. TABEL DETAIL SPAREPART (detail_sparepart)
CREATE TABLE IF NOT EXISTS detail_sparepart (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_tiket INT NOT NULL,
    nama_komponen VARCHAR(100) NOT NULL,
    jumlah INT NOT NULL DEFAULT 1,
    harga_satuan DECIMAL(12, 2) NOT NULL,
    subtotal DECIMAL(12, 2) NOT NULL,
    CONSTRAINT fk_detail_tiket FOREIGN KEY (id_tiket) REFERENCES tiket_servis(id_tiket) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. TABEL LOG RIWAYAT STATUS (log_status_servis)
CREATE TABLE IF NOT EXISTS log_status_servis (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_tiket INT NOT NULL,
    status_sebelumnya VARCHAR(50),
    status_baru VARCHAR(50) NOT NULL,
    waktu_perubahan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    keterangan VARCHAR(255),
    CONSTRAINT fk_log_tiket FOREIGN KEY (id_tiket) REFERENCES tiket_servis(id_tiket) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================================
-- DATA AWAL SIMULASI (DUMMY DATA)
-- =====================================================================

INSERT INTO users (id_user, username, password_hash, nama_lengkap, role, no_telp) VALUES
(1, 'admin', MD5('admin123'), 'Soffin Halim (Koordinator Lab)', 'admin', '081234567890'),
(2, 'teknisi1', MD5('tek123'), 'Budi Pratama (Teknisi Hardware)', 'teknisi', '081298765432');

INSERT INTO pelanggan (id_pelanggan, nama, no_hp, alamat, email) VALUES
(1, 'Ahmad Fauzi', '085711223344', 'Jl. Slamet Riyadi No. 45, Surakarta', 'ahmad.fauzi@gmail.com'),
(2, 'Siti Nurhaliza', '087899887766', 'Jl. Urip Sumoharjo No. 12, Solo', 'siti.nur@gmail.com');

INSERT INTO perangkat (id_perangkat, id_pelanggan, jenis_perangkat, merk_model, nomor_seri, keluhan_awal, kelengkapan) VALUES
(1, 1, 'Laptop', 'Lenovo ThinkPad T480', 'PF-1ABC890', 'Layar blank hitam setelah booting, kipas berputar kencang', 'Unit Laptop + Adaptor Original 65W'),
(2, 2, 'PC Desktop', 'Custom PC Intel Core i5 12400', 'SN-DESK-2024-99', 'Blue Screen (BSOD) terus menerus saat membuka aplikasi berat', 'Hanya CPU casing tanpa kabel');

INSERT INTO tiket_servis (id_tiket, nomor_tiket, id_perangkat, id_teknisi, tanggal_masuk, tingkat_kerusakan, status_servis, estimasi_selesai, catatan_diagnosa, biaya_jasa, biaya_sparepart, total_biaya) VALUES
(1, 'SRV-202610-001', 1, 2, '2026-10-11 09:30:00', 'Sedang', 'Proses Servis', '2026-10-14', 'Kerusakan pada IC Power dan RAM kotor. Perlu pembersihan slot RAM dan penggantian thermal paste baru.', 125000.00, 75000.00, 200000.00),
(2, 'SRV-202610-002', 2, 2, '2026-10-12 14:15:00', 'Berat', 'Menunggu Sparepart', '2026-10-16', 'SSD NVMe mengalami bad block parah. Perlu penggantian unit SSD NVMe 512GB dan re-install OS Windows 11.', 150000.00, 550000.00, 700000.00);

INSERT INTO detail_sparepart (id_detail, id_tiket, nama_komponen, jumlah, harga_satuan, subtotal) VALUES
(1, 1, 'Thermal Paste Arctic MX-4 Original', 1, 75000.00, 75000.00),
(2, 2, 'SSD NVMe M.2 512GB Gen3x4', 1, 550000.00, 550000.00);

INSERT INTO log_status_servis (id_log, id_tiket, status_sebelumnya, status_baru, keterangan) VALUES
(1, 1, 'Antrean Masuk', 'Diagnosa', 'Unit diterima dan didaftarkan ke sistem'),
(2, 1, 'Diagnosa', 'Proses Servis', 'Diagnosa selesai, teknisi melakukan pembersihan motherboard & ganti thermal'),
(3, 2, 'Antrean Masuk', 'Diagnosa', 'Pemeriksaan awal menemukan storage failing'),
(4, 2, 'Diagnosa', 'Menunggu Sparepart', 'Menunggu persetujuan pelanggan untuk pengadaan SSD baru');
