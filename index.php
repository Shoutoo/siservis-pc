<?php
/**
 * SiServis-PC: Sistem Informasi Manajemen & Tracking Servis Perangkat Komputer
 * Ujian Tengah Semester (UTS) Rekayasa Perangkat Lunak
 * Mahasiswa: Muhammad Soffin Halim Sampurna (NIM: 240104019)
 * Program Studi: D3 Teknik Komputer 24A1 - Universitas Duta Bangsa
 */

date_default_timezone_set('Asia/Jakarta');
$currentTime = date('d-m-Y H:i:s');

// Data Mockup Tiket Servis
$databaseTiket = [
    'SRV-202610-001' => [
        'nomor_tiket' => 'SRV-202610-001',
        'pelanggan' => 'Ahmad Fauzi',
        'no_hp' => '0857-1122-3344',
        'jenis_perangkat' => 'Laptop',
        'merk_model' => 'Lenovo ThinkPad T480',
        'keluhan' => 'Layar blank hitam setelah booting, kipas berputar kencang',
        'teknisi' => 'Budi Pratama (Teknisi Hardware)',
        'tingkat_kerusakan' => 'Sedang',
        'status' => 'Proses Servis',
        'step_progress' => 4, // 1: Masuk, 2: Diagnosa, 3: Sparepart, 4: Servis, 5: Selesai
        'tgl_masuk' => '11-10-2026 09:30',
        'est_selesai' => '14-10-2026',
        'catatan_diagnosa' => 'IC Power terindikasi korosi ringan, slot RAM berdebu. Dilakukan pembersihan sirkuit dan re-pasting thermal.',
        'biaya_jasa' => 125000,
        'biaya_sparepart' => 75000,
        'sparepart_item' => 'Thermal Paste Arctic MX-4'
    ],
    'SRV-202610-002' => [
        'nomor_tiket' => 'SRV-202610-002',
        'pelanggan' => 'Siti Nurhaliza',
        'no_hp' => '0878-9988-7766',
        'jenis_perangkat' => 'PC Desktop',
        'merk_model' => 'Custom PC Core i5 12400',
        'keluhan' => 'Blue Screen (BSOD) terus menerus saat membuka file berat',
        'teknisi' => 'Budi Pratama (Teknisi Hardware)',
        'tingkat_kerusakan' => 'Berat',
        'status' => 'Menunggu Sparepart',
        'step_progress' => 3,
        'tgl_masuk' => '12-10-2026 14:15',
        'est_selesai' => '16-10-2026',
        'catatan_diagnosa' => 'Health SSD NVMe terdeteksi 0% dengan ribuan bad sector. Diperlukan penggantian SSD baru dan clean install OS.',
        'biaya_jasa' => 150000,
        'biaya_sparepart' => 550000,
        'sparepart_item' => 'SSD NVMe M.2 512GB'
    ],
    'SRV-202610-003' => [
        'nomor_tiket' => 'SRV-202610-003',
        'pelanggan' => 'Rian Hidayat',
        'no_hp' => '0813-4455-6677',
        'jenis_perangkat' => 'All-in-One PC',
        'merk_model' => 'HP 24-df0012d AIO',
        'keluhan' => 'Kinerja sangat lambat dan sering freeze saat booting awal',
        'teknisi' => 'Muhammad Soffin Halim Sampurna (Koordinator Lab)',
        'tingkat_kerusakan' => 'Ringan',
        'status' => 'Selesai',
        'step_progress' => 5,
        'tgl_masuk' => '09-10-2026 10:00',
        'est_selesai' => '11-10-2026',
        'catatan_diagnosa' => 'Optimasi sistem startup, pembersihan temporary cache, dan upgrade RAM DDR4 8GB selesai dilakukan. Unit siap diambil.',
        'biaya_jasa' => 100000,
        'biaya_sparepart' => 250000,
        'sparepart_item' => 'RAM DDR4 8GB 3200MHz'
    ]
];

// 1. Logika Pencarian Tiket Servis
$searchResult = null;
$searchError = '';
$searchedTicket = '';

if (isset($_GET['cari_tiket'])) {
    $searchedTicket = strtoupper(trim($_GET['cari_tiket']));
    if (empty($searchedTicket)) {
        $searchError = 'Silakan masukkan nomor tiket servis yang valid!';
    } elseif (isset($databaseTiket[$searchedTicket])) {
        $searchResult = $databaseTiket[$searchedTicket];
    } else {
        $searchError = "Nomor tiket '{$searchedTicket}' tidak ditemukan dalam basis data sistem.";
    }
}

// 2. Logika Perhitungan Estimasi Biaya & Durasi Servis
$kalkulasiResult = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hitung_estimasi'])) {
    $jenisPerangkat = $_POST['jenis_perangkat'] ?? 'PC Desktop';
    $tingkatKerusakan = $_POST['tingkat_kerusakan'] ?? 'Ringan';
    $pilihanSparepart = $_POST['pilihan_sparepart'] ?? 'none';
    $isCivitas = isset($_POST['diskon_civitas']);

    // Tarif Jasa Pokok
    $biayaJasaPokok = 75000;
    if ($jenisPerangkat === 'Laptop') $biayaJasaPokok = 100000;
    if ($jenisPerangkat === 'All-in-One') $biayaJasaPokok = 120000;

    // Pengali & Durasi Hari
    $pengali = 1.0;
    $durasiHari = 1;
    if ($tingkatKerusakan === 'Sedang') {
        $pengali = 1.5;
        $durasiHari = 3;
    } elseif ($tingkatKerusakan === 'Berat') {
        $pengali = 2.2;
        $durasiHari = 5;
    }

    $totalJasa = $biayaJasaPokok * $pengali;

    // Biaya Komponen Sparepart
    $biayaSparepart = 0;
    $namaKomponen = 'Tidak ada penggantian sparepart';
    switch ($pilihanSparepart) {
        case 'ram8':
            $biayaSparepart = 250000;
            $namaKomponen = 'RAM DDR4 8GB 3200MHz';
            break;
        case 'ssd512':
            $biayaSparepart = 550000;
            $namaKomponen = 'SSD NVMe M.2 512GB';
            break;
        case 'psu550':
            $biayaSparepart = 450000;
            $namaKomponen = 'Power Supply 550W Bronze';
            break;
        case 'pasta':
            $biayaSparepart = 75000;
            $namaKomponen = 'Thermal Paste High-Performance';
            break;
    }

    $subtotal = $totalJasa + $biayaSparepart;
    $potongan = $isCivitas ? ($subtotal * 0.10) : 0;
    $totalAkhir = $subtotal - $potongan;

    $estTanggalSelesai = date('d-m-Y', strtotime("+{$durasiHari} weekdays"));

    $kalkulasiResult = [
        'perangkat' => $jenisPerangkat,
        'kerusakan' => $tingkatKerusakan,
        'komponen' => $namaKomponen,
        'biaya_jasa' => $totalJasa,
        'biaya_sparepart' => $biayaSparepart,
        'potongan' => $potongan,
        'total' => $totalAkhir,
        'durasi' => $durasiHari,
        'est_tanggal' => $estTanggalSelesai
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiServis-PC | Sistem Informasi Tracking & Servis Komputer</title>
    <style>
        :root {
            --primary: #1e3a8a;
            --primary-light: #3b82f6;
            --accent: #0ea5e9;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #0f172a;
            --light-bg: #f8fafc;
            --card-bg: #ffffff;
            --border: #e2e8f0;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        body {
            background-color: var(--light-bg);
            color: var(--text-main);
            line-height: 1.6;
        }
        header {
            background: linear-gradient(135deg, var(--primary) 0%, #1e40af 50%, var(--accent) 100%);
            color: white;
            padding: 2.2rem 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .header-container {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .brand h1 {
            font-size: 1.85rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .brand p {
            font-size: 0.95rem;
            opacity: 0.9;
            margin-top: 0.2rem;
        }
        .badge-academic {
            background: rgba(255,255,255,0.18);
            backdrop-filter: blur(8px);
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.25);
            font-size: 0.85rem;
            text-align: right;
        }
        .container {
            max-width: 1100px;
            margin: -1.5rem auto 3rem;
            padding: 0 1rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: var(--card-bg);
            padding: 1.25rem;
            border-radius: 12px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .stat-card .val {
            font-size: 1.9rem;
            font-weight: 700;
            color: var(--primary);
        }
        .stat-card .lbl {
            font-size: 0.85rem;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 600;
        }
        .main-grid {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 1.75rem;
        }
        @media (max-width: 860px) {
            .main-grid { grid-template-columns: 1fr; }
            .header-container { flex-direction: column; align-items: flex-start; }
            .badge-academic { text-align: left; }
        }
        .card {
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            padding: 1.75rem;
            margin-bottom: 1.75rem;
        }
        .card-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 0.75rem;
        }
        .form-group {
            margin-bottom: 1.1rem;
        }
        label {
            display: block;
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 0.35rem;
        }
        input[type="text"], select {
            width: 100%;
            padding: 0.7rem 0.9rem;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-size: 0.95rem;
            transition: border-color 0.2s;
            outline: none;
        }
        input[type="text"]:focus, select:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        }
        .btn {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 0.75rem 1.4rem;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            width: 100%;
        }
        .btn:hover {
            background-color: #1d4ed8;
            transform: translateY(-1px);
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0.8rem 0;
            font-size: 0.88rem;
        }
        .quick-badges {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
            flex-wrap: wrap;
        }
        .quick-badge {
            background: #e0f2fe;
            color: #0369a1;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-size: 0.8rem;
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
        }
        .quick-badge:hover { background: #bae6fd; }
        .result-box {
            background: #f8fafc;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            padding: 1.25rem;
            margin-top: 1.25rem;
        }
        .progress-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 1.5rem 0 1.2rem;
        }
        .progress-steps::before {
            content: '';
            position: absolute;
            top: 14px;
            left: 5%;
            right: 5%;
            height: 3px;
            background: #cbd5e1;
            z-index: 1;
        }
        .step-node {
            position: relative;
            z-index: 2;
            text-align: center;
            flex: 1;
        }
        .step-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #94a3b8;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
            margin: 0 auto 0.35rem;
        }
        .step-node.active .step-circle {
            background: var(--primary-light);
            box-shadow: 0 0 0 4px rgba(59,130,246,0.25);
        }
        .step-node.completed .step-circle {
            background: var(--success);
        }
        .step-label {
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--text-muted);
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.88rem;
            margin-top: 0.8rem;
        }
        .info-table td {
            padding: 0.45rem 0.2rem;
            border-bottom: 1px dashed var(--border);
        }
        .info-table td:first-child {
            color: var(--text-muted);
            font-weight: 500;
            width: 35%;
        }
        .info-table td:last-child {
            font-weight: 600;
            color: var(--text-main);
        }
        .status-pill {
            display: inline-block;
            padding: 0.3rem 0.75rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 700;
        }
        .status-pill.selesai { background: #dcfce7; color: #15803d; }
        .status-pill.proses { background: #dbeafe; color: #1d4ed8; }
        .status-pill.sparepart { background: #fef3c7; color: #b45309; }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 0.8rem;
            border-radius: 8px;
            font-size: 0.88rem;
            margin-top: 1rem;
            border-left: 4px solid var(--danger);
        }
        footer {
            text-align: center;
            padding: 2rem 1rem;
            font-size: 0.85rem;
            color: var(--text-muted);
            border-top: 1px solid var(--border);
            background: white;
        }
    </style>
</head>
<body>

<header>
    <div class="header-container">
        <div class="brand">
            <h1>🛠️ SiServis-PC</h1>
            <p>Sistem Informasi Manajemen & Live Tracking Servis Perangkat Komputer</p>
        </div>
        <div class="badge-academic">
            <strong>Muhammad Soffin Halim Sampurna</strong> (NIM: 240104019)<br>
            Kelas: D3 Teknik Komputer 24A1 | Semester V<br>
            Server Time: <?= htmlspecialchars($currentTime) ?> WIB
        </div>
    </div>
</header>

<div class="container">
    <!-- Stat Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div>
                <div class="lbl">Unit Masuk</div>
                <div class="val">12</div>
            </div>
            <span style="font-size:2rem;">💻</span>
        </div>
        <div class="stat-card">
            <div>
                <div class="lbl">Dalam Servis</div>
                <div class="val" style="color:var(--accent);">4</div>
            </div>
            <span style="font-size:2rem;">⚙️</span>
        </div>
        <div class="stat-card">
            <div>
                <div class="lbl">Menunggu Part</div>
                <div class="val" style="color:var(--warning);">3</div>
            </div>
            <span style="font-size:2rem;">📦</span>
        </div>
        <div class="stat-card">
            <div>
                <div class="lbl">Selesai Siap Ambil</div>
                <div class="val" style="color:var(--success);">5</div>
            </div>
            <span style="font-size:2rem;">✅</span>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="main-grid">
        <!-- Kolom Kiri: Lacak Status Tiket -->
        <div>
            <div class="card">
                <div class="card-title">🔍 Lacak Tiket Servis (Customer Self-Tracking)</div>
                <p style="font-size:0.88rem; color:var(--text-muted); margin-bottom:1rem;">
                    Masukkan nomor tiket servis unik yang tertera pada bukti penerimaan unit untuk melihat tahapan pengerjaan.
                </p>
                <form method="GET" action="">
                    <div class="form-group">
                        <label for="cari_tiket">Nomor Tiket Servis:</label>
                        <input type="text" id="cari_tiket" name="cari_tiket" placeholder="Contoh: SRV-202610-001" value="<?= htmlspecialchars($searchedTicket) ?>" required>
                    </div>
                    <button type="submit" class="btn">Periksa Status Pengerjaan</button>
                    <div class="quick-badges">
                        <span style="font-size:0.78rem; color:var(--text-muted); align-self:center;">Contoh Tiket:</span>
                        <a href="?cari_tiket=SRV-202610-001" class="quick-badge">SRV-202610-001</a>
                        <a href="?cari_tiket=SRV-202610-002" class="quick-badge">SRV-202610-002</a>
                        <a href="?cari_tiket=SRV-202610-003" class="quick-badge">SRV-202610-003</a>
                    </div>
                </form>

                <?php if ($searchError): ?>
                    <div class="alert-error">
                        ⚠️ <?= htmlspecialchars($searchError) ?>
                    </div>
                <?php endif; ?>

                <?php if ($searchResult): ?>
                    <div class="result-box">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
                            <strong style="font-size:1.1rem; color:var(--primary);"><?= htmlspecialchars($searchResult['nomor_tiket']) ?></strong>
                            <?php 
                                $pillClass = 'proses';
                                if ($searchResult['status'] === 'Selesai') $pillClass = 'selesai';
                                if ($searchResult['status'] === 'Menunggu Sparepart') $pillClass = 'sparepart';
                            ?>
                            <span class="status-pill <?= $pillClass ?>"><?= htmlspecialchars($searchResult['status']) ?></span>
                        </div>

                        <!-- Step Timeline -->
                        <div class="progress-steps">
                            <?php
                            $steps = ['1. Masuk', '2. Diagnosa', '3. Part', '4. Servis', '5. Selesai'];
                            foreach ($steps as $idx => $label):
                                $num = $idx + 1;
                                $nodeClass = '';
                                if ($num < $searchResult['step_progress']) $nodeClass = 'completed';
                                elseif ($num === $searchResult['step_progress']) $nodeClass = 'active';
                            ?>
                            <div class="step-node <?= $nodeClass ?>">
                                <div class="step-circle"><?= $num < $searchResult['step_progress'] ? '✓' : $num ?></div>
                                <div class="step-label"><?= $label ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <table class="info-table">
                            <tr>
                                <td>Nama Pelanggan</td>
                                <td><?= htmlspecialchars($searchResult['pelanggan']) ?> (<?= htmlspecialchars($searchResult['no_hp']) ?>)</td>
                            </tr>
                            <tr>
                                <td>Unit Perangkat</td>
                                <td><?= htmlspecialchars($searchResult['jenis_perangkat']) ?> - <?= htmlspecialchars($searchResult['merk_model']) ?></td>
                            </tr>
                            <tr>
                                <td>Keluhan Awal</td>
                                <td><?= htmlspecialchars($searchResult['keluhan']) ?></td>
                            </tr>
                            <tr>
                                <td>Diagnosa Teknisi</td>
                                <td><?= htmlspecialchars($searchResult['catatan_diagnosa']) ?></td>
                            </tr>
                            <tr>
                                <td>Teknisi Penanggung Jawab</td>
                                <td><?= htmlspecialchars($searchResult['teknisi']) ?></td>
                            </tr>
                            <tr>
                                <td>Estimasi Selesai</td>
                                <td><?= htmlspecialchars($searchResult['est_selesai']) ?></td>
                            </tr>
                            <tr>
                                <td>Total Biaya</td>
                                <td style="color:var(--primary); font-size:1rem;">Rp <?= number_format($searchResult['biaya_jasa'] + $searchResult['biaya_sparepart'], 0, ',', '.') ?> (Jasa + Komponen)</td>
                            </tr>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Kolom Kanan: Kalkulator Estimasi Biaya & Durasi -->
        <div>
            <div class="card">
                <div class="card-title">🧮 Kalkulator Estimasi Biaya & Durasi</div>
                <p style="font-size:0.88rem; color:var(--text-muted); margin-bottom:1rem;">
                    Implementasi logika bisnis otomatis untuk menghitung estimasi biaya pengerjaan dan waktu perbaikan hardware/software.
                </p>
                <form method="POST" action="">
                    <input type="hidden" name="hitung_estimasi" value="1">
                    <div class="form-group">
                        <label for="jenis_perangkat">Kategori Perangkat:</label>
                        <select id="jenis_perangkat" name="jenis_perangkat" required>
                            <option value="PC Desktop">PC Desktop / Tower (Jasa Dasar: Rp 75.000)</option>
                            <option value="Laptop">Laptop / Notebook (Jasa Dasar: Rp 100.000)</option>
                            <option value="All-in-One">All-in-One PC (Jasa Dasar: Rp 120.000)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tingkat_kerusakan">Tingkat Kerusakan / Kompleksitas:</label>
                        <select id="tingkat_kerusakan" name="tingkat_kerusakan" required>
                            <option value="Ringan">Ringan (Software / Pembersihan) - Estimasi: 1 Hari</option>
                            <option value="Sedang">Sedang (Re-pasting / Perbaikan Jalur) - Estimasi: 3 Hari</option>
                            <option value="Berat">Berat (Kerusakan IC / Storage Bad Sector) - Estimasi: 5 Hari</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="pilihan_sparepart">Kebutuhan Suku Cadang (Sparepart):</label>
                        <select id="pilihan_sparepart" name="pilihan_sparepart">
                            <option value="none">-- Tanpa Penggantian Sparepart (Rp 0) --</option>
                            <option value="pasta">Thermal Paste High-Performance (+ Rp 75.000)</option>
                            <option value="ram8">RAM DDR4 8GB 3200MHz (+ Rp 250.000)</option>
                            <option value="psu550">Power Supply 550W 80+ Bronze (+ Rp 450.000)</option>
                            <option value="ssd512">SSD NVMe M.2 512GB (+ Rp 550.000)</option>
                        </select>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="diskon_civitas" name="diskon_civitas" value="1">
                        <label for="diskon_civitas" style="margin-bottom:0; font-weight:normal;">Terapkan Diskon Civitas Akademika (10%)</label>
                    </div>

                    <button type="submit" class="btn" style="background-color:#0284c7;">Hitung Estimasi Biaya & Waktu</button>
                </form>

                <?php if ($kalkulasiResult): ?>
                    <div class="result-box" style="border-color:#38bdf8; background:#f0f9ff;">
                        <h4 style="color:#0369a1; margin-bottom:0.6rem; font-size:1rem;">📊 Rincian Hasil Kalkulasi:</h4>
                        <table class="info-table">
                            <tr>
                                <td>Perangkat</td>
                                <td><?= htmlspecialchars($kalkulasiResult['perangkat']) ?></td>
                            </tr>
                            <tr>
                                <td>Tingkat Kerusakan</td>
                                <td><?= htmlspecialchars($kalkulasiResult['kerusakan']) ?></td>
                            </tr>
                            <tr>
                                <td>Biaya Jasa Teknisi</td>
                                <td>Rp <?= number_format($kalkulasiResult['biaya_jasa'], 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td>Biaya Sparepart</td>
                                <td>Rp <?= number_format($kalkulasiResult['biaya_sparepart'], 0, ',', '.') ?> (<?= htmlspecialchars($kalkulasiResult['komponen']) ?>)</td>
                            </tr>
                            <?php if ($kalkulasiResult['potongan'] > 0): ?>
                            <tr>
                                <td>Diskon Civitas (10%)</td>
                                <td style="color:var(--success);">- Rp <?= number_format($kalkulasiResult['potongan'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr style="border-top: 2px solid #bae6fd;">
                                <td style="font-weight:bold; color:var(--dark);">Total Estimasi Biaya</td>
                                <td style="font-weight:bold; color:var(--primary); font-size:1.1rem;">Rp <?= number_format($kalkulasiResult['total'], 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td>Estimasi Pengerjaan</td>
                                <td style="color:#0369a1; font-weight:bold;"><?= $kalkulasiResult['durasi'] ?> Hari Kerja (Target: <?= $kalkulasiResult['est_tanggal'] ?>)</td>
                            </tr>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<footer>
    <div style="max-width:1100px; margin:0 auto;">
        <p><strong>SiServis-PC</strong> &copy; 2026 | Proyek Ujian Tengah Semester Rekayasa Perangkat Lunak</p>
        <p style="margin-top:0.3rem;">D3 Teknik Komputer - Universitas Duta Bangsa Surakarta</p>
    </div>
</footer>

</body>
</html>
