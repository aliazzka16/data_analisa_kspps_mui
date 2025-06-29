<?php
// admin/detail.php
$id = $_GET['id'] ?? -1;
$file_path = '../data/data.txt';
$calculation_type = $_GET['type'] ?? 'reguler'; // 'reguler' atau 'musiman'

if (!file_exists($file_path)) {
    header("Location: admin_panel.php?error=file_not_found");
    exit;
}

$data_lines = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$row = isset($data_lines[$id]) ? explode('|', $data_lines[$id]) : [];

// Fungsi pembantu untuk membersihkan dan mengubah ke float
function cleanAndFloat($value) {
    // Hapus 'Rp. ', semua titik (.), dan ganti koma (,) dengan kosong, lalu konversi ke float
    $cleaned = str_replace(['Rp. ', '.', ','], '', $value);
    return floatval($cleaned);
}

// Pastikan jumlah kolom yang diharapkan adalah 112 (indeks 0 sampai 111)
// Ini penting untuk kompatibilitas mundur jika ada data lama yang kurang dari 112 field
$expected_fields = 112;
if (count($row) < $expected_fields) {
    $missing_fields = $expected_fields - count($row);
    for ($i = 0; $i < $missing_fields; $i++) {
        $row[] = '0'; // Tambahkan '0' atau string kosong untuk field yang tidak ada
    }
}

// ---- Mapping Data Berdasarkan Indeks simpan.php (Total 112 field) ----

// Data Umum (Indeks 0-6)
$cabang = $row[0];
$marketing = $row[1];
$anggota = $row[2];
$alamat = $row[3];
$nominal_pengajuan = cleanAndFloat($row[4]);
$jenis_usaha = $row[5];
$jenis_pembiayaan = $row[6]; // NEW FIELD: Reguler/Musiman

// Data Pendapatan Pegawai (Indeks 7-9)
$gaji = cleanAndFloat($row[7]);
$total_tunjangan = cleanAndFloat($row[8]);
$biaya_pokok = cleanAndFloat($row[9]);

// Data Pendapatan Usaha - Uraian dan Omset (Indeks 10-32)
$jenis_usaha_uraian = $row[10];
$omset_text1 = []; $omset_text2 = []; $omset_nominal = [];
for ($i = 0; $i < 7; $i++) {
    $omset_text1[] = $row[11 + ($i * 3)];
    $omset_text2[] = $row[12 + ($i * 3)];
    $omset_nominal[] = cleanAndFloat($row[13 + ($i * 3)]);
}
$omset_total = cleanAndFloat($row[32]);

// Data Pendapatan Usaha - HPP (Indeks 33-54)
$hpp_text1 = []; $hpp_text2 = []; $hpp_nominal = [];
for ($i = 0; $i < 7; $i++) {
    $hpp_text1[] = $row[33 + ($i * 3)];
    $hpp_text2[] = $row[34 + ($i * 3)];
    $hpp_nominal[] = cleanAndFloat($row[35 + ($i * 3)]);
}
$hpp_total = cleanAndFloat($row[54]);

// Data Pendapatan Usaha - Biaya Operasional (Indeks 55-76)
$operasional_text1 = []; $operasional_text2 = []; $operasional_nominal = [];
for ($i = 0; $i < 7; $i++) {
    $operasional_text1[] = $row[55 + ($i * 3)];
    $operasional_text2[] = $row[56 + ($i * 3)];
    $operasional_nominal[] = cleanAndFloat($row[57 + ($i * 3)]);
}
$operasional_total = cleanAndFloat($row[76]);

// Pengeluaran Rumah Tangga (Indeks 77-83)
$makan_minum = cleanAndFloat($row[77]);
$anak = cleanAndFloat($row[78]);
$pendidikan = cleanAndFloat($row[79]);
$listrik = cleanAndFloat($row[80]);
$air = cleanAndFloat($row[81]);
$transport = cleanAndFloat($row[82]);
$pengeluaran_lain_rt = cleanAndFloat($row[83]);

// Pengeluaran Angsuran/Arisan/Iuran (Indeks 84-88)
$angsuran1 = cleanAndFloat($row[84]);
$bpjs = cleanAndFloat($row[85]);
$arisan = cleanAndFloat($row[86]);
$iuran = cleanAndFloat($row[87]);
$angsuran_lain = cleanAndFloat($row[88]);

// Aset Lancar (Indeks 89-94)
$kas_tunai = cleanAndFloat($row[89]);
$kas_bank = cleanAndFloat($row[90]);
$piutang = cleanAndFloat($row[91]);
$persediaan = cleanAndFloat($row[92]);
$emas = cleanAndFloat($row[93]);
$surat_berharga = cleanAndFloat($row[94]);

// Aset Tetap (Indeks 95-100)
$mobil = cleanAndFloat($row[95]);
$motor = cleanAndFloat($row[96]);
$rumah = cleanAndFloat($row[97]);
$tanah = cleanAndFloat($row[98]);
$gudang = cleanAndFloat($row[99]);
$kantor = cleanAndFloat($row[100]);

// Kewajiban Lancar (Indeks 101-104)
$dp_diterima = cleanAndFloat($row[101]);
$biaya_harus_bayar = cleanAndFloat($row[102]);
$dll_kewajiban_lancar = cleanAndFloat($row[103]);
$total_kewajiban_lancar = cleanAndFloat($row[104]);

// Kewajiban Tetap (Indeks 105-107)
$modal_pinjaman = cleanAndFloat($row[105]);
$modal_bank = cleanAndFloat($row[106]);
$total_kewajiban_tetap = cleanAndFloat($row[107]);

// Total Keseluruhan Kewajiban (Indeks 108)
$total_keseluruhan_kewajiban = cleanAndFloat($row[108]);

// Angsuran KSPPS MUI (Indeks 109)
$pengeluaran_kspps_original = cleanAndFloat($row[109]); // Simpan nilai asli
$pengeluaran_kspps = $pengeluaran_kspps_original; // Default ke nilai asli

// Dokumen filename dan Waktu Simpan (Indeks 110-111)
$dokumen_filename = $row[110];
$timestamp = $row[111];

// ---- Perhitungan Ulang Disposable Income dan Laba Usaha (sesuai logika index.php) ----
$total_gaji_tunjangan_pegawai = $gaji + $total_tunjangan;
$total_pendapatan_usaha = $omset_total; // Ini omset, bukan laba

$laba_usaha = $omset_total - $hpp_total - $operasional_total;

$pengeluaran_rumah_tangga_total = $makan_minum + $anak + $pendidikan + $listrik + $air + $transport + $pengeluaran_lain_rt;
$pengeluaran_angsuran_arisan_iuran_total = $angsuran1 + $bpjs + $arisan + $iuran + $angsuran_lain;

// Hitung ulang total_pengeluaran dengan mempertimbangkan $pengeluaran_kspps yang mungkin berubah
$total_pengeluaran = $pengeluaran_rumah_tangga_total + $pengeluaran_angsuran_arisan_iuran_total + $pengeluaran_kspps;

$disposable_income_pegawai = $total_gaji_tunjangan_pegawai - $total_pengeluaran;
$disposable_income_usaha = $laba_usaha - $total_pengeluaran;

$disposable_income_final = 0;
if ($jenis_usaha == 'pegawai') {
    $disposable_income_final = $disposable_income_pegawai;
} elseif ($jenis_usaha == 'usaha') {
    $disposable_income_final = $disposable_income_usaha;
} elseif ($jenis_usaha == 'usaha_dan_pegawai') {
    // Jika keduanya, gabungkan pendapatan pegawai dan laba usaha
    $total_pendapatan_gabungan_untuk_di = $total_gaji_tunjangan_pegawai + $laba_usaha;
    $disposable_income_final = $total_pendapatan_gabungan_untuk_di - $total_pengeluaran;
}

// Fungsi untuk menghitung Status Disposable Income (sesuai kriteria Diterima/Dipertimbangkan/Ditolak)
function hitungDisposableIncomeStatus(float $disposableIncomeFinal): string
{
    // Kriteria status Diterima, Dipertimbangkan, Ditolak berdasarkan nilai Disposable Income final
    // (Misal: 3 juta, 1 juta - sesuaikan dengan kebijakan KSPPS MUI)
    if ($disposableIncomeFinal >= 3000000) {
        return "Diterima";
    } elseif ($disposableIncomeFinal >= 1000000) {
        return "Dipertimbangkan";
    } else {
        return "Ditolak";
    }
}
$status_disposable_income = hitungDisposableIncomeStatus($disposable_income_final);


// --- New Analytical Calculations ---

// Total Pendapatan Keseluruhan (untuk section Analisa Keuangan)
$total_pendapatan_keseluruhan = 0;
if ($jenis_usaha == 'pegawai') {
    $total_pendapatan_keseluruhan = $total_gaji_tunjangan_pegawai;
} elseif ($jenis_usaha == 'usaha') {
    $total_pendapatan_keseluruhan = $laba_usaha;
} elseif ($jenis_usaha == 'usaha_dan_pegawai') {
    $total_pendapatan_keseluruhan = $total_gaji_tunjangan_pegawai + $laba_usaha;
}

// Total Seluruh Pengeluaran (Diluar Angsuran KSPPS)
$total_pengeluaran_diluar_kspps = $pengeluaran_rumah_tangga_total + $pengeluaran_angsuran_arisan_iuran_total;

// Sisa Dana Aktual
$sisa_dana_aktual = $total_pendapatan_keseluruhan - $total_pengeluaran_diluar_kspps;

// Total Aset (untuk Net Worth, RPC, DER)
$total_aset_lancar = $kas_tunai + $kas_bank + $piutang + $persediaan + $emas + $surat_berharga;
$total_aset_tetap = $mobil + $motor + $rumah + $tanah + $gudang + $kantor;
$total_aset = $total_aset_lancar + $total_aset_tetap; // NEW CALCULATION

// Net Worth
$net_worth = $total_aset - $total_keseluruhan_kewajiban;

// Modal (jumlah dari modal_pinjaman dan modal_bank)
$modal_total = $modal_pinjaman + $modal_bank;


// --- KONDISIONAL BERDASARKAN TIPE PERHITUNGAN (REGULER / MUSIMAN) ---
$rpc_value_raw = 0;
$rpc_status = "N/A";
$der_value = 0;
$der_status = "N/A";
$profitability_value_raw = 0;
$status_profitability = "N/A";

if ($calculation_type === 'musiman') {
    // Angsuran Musiman
    // rumus angsuran musiman : (plafon pinjaman x 47,25%) + plafon pinjaman di bagi 6
    $pengeluaran_kspps = ($nominal_pengajuan * 0.4725) + ($nominal_pengajuan / 6);

    // REPAYMENT CAPACITIY/RPC musiman
    // rumusnya : DISPOSABLE INCOME di bagi angsuran
    // (Standar minimal Repayment Capacity = 4 Diterima, 3 Dipertimbangkan, <3 Ditolak)
    if ($pengeluaran_kspps != 0) {
        $rpc_value_raw = $disposable_income_final / $pengeluaran_kspps;

        if ($rpc_value_raw >= 4) {
            $rpc_status = "Diterima";
        } elseif ($rpc_value_raw >= 3) {
            $rpc_status = "Dipertimbangkan";
        } else {
            $rpc_status = "Ditolak";
        }
    } else {
        $rpc_status = "Tidak Terdefinisi (Angsuran KSPPS 0)";
    }

    // Untuk DER dan Profitability, gunakan logika Reguler jika tidak ada spesifikasi lain
    // Debt to Equity Ratio (DER)
    if ($modal_total != 0) { // Avoid division by zero
        $der_value = ($total_keseluruhan_kewajiban / $modal_total);
        if ($der_value <= 1.5) { // Contoh kriteria: Max 1.5x
            $der_status = "Baik";
        } else {
            $der_status = "Kurang Baik";
        }
    } else {
        $der_status = "Tidak Terdefinisi (Modal 0)";
    }

    // Profitability Ratio (Laba Usaha / Omset)
    if ($omset_total != 0) {
        $profitability_value_raw = ($laba_usaha / $omset_total) * 100; // Dalam persen
        if ($profitability_value_raw >= 15) { // Contoh kriteria: Minimal 15%
            $status_profitability = "Baik";
        } else {
            $status_profitability = "Kurang Baik";
        }
    } else {
        $status_profitability = "Tidak Terdefinisi (Omset 0)";
    }

} else { // Regulur (default)
    // Status RPC (Rentability per Capital) - Logika Asli
    if ($net_worth != 0) { // Avoid division by zero
        $rpc_value_raw = ($disposable_income_final / $net_worth) * 100;
        if ($rpc_value_raw >= 10) { // Contoh kriteria: Minimal 10% dari Net Worth
            $rpc_status = "Baik";
        } else {
            $rpc_status = "Kurang Baik";
        }
    } else {
        $rpc_status = "Tidak Terdefinisi (Net Worth 0)";
    }

    // Debt to Equity Ratio (DER) - Logika Asli
    if ($modal_total != 0) { // Avoid division by zero
        $der_value = ($total_keseluruhan_kewajiban / $modal_total);
        if ($der_value <= 1.5) { // Contoh kriteria: Max 1.5x
            $der_status = "Baik";
        } else {
            $der_status = "Kurang Baik";
        }
    } else {
        $der_status = "Tidak Terdefinisi (Modal 0)";
    }

    // Profitability Ratio (Laba Usaha / Omset) - Logika Asli
    if ($omset_total != 0) {
        $profitability_value_raw = ($laba_usaha / $omset_total) * 100; // Dalam persen
        if ($profitability_value_raw >= 15) { // Contoh kriteria: Minimal 15%
            $status_profitability = "Baik";
        } else {
            $status_profitability = "Kurang Baik";
        }
    } else {
        $status_profitability = "Tidak Terdefinisi (Omset 0)";
    }
}


// Fungsi untuk memformat angka menjadi Rupiah
function formatRupiah($number) {
    return 'Rp ' . number_format($number, 0, ',', '.');
}

// Konfigurasi umum untuk Chart.js
$commonChartOptions = [
    'responsive' => true,
    'maintainAspectRatio' => false,
    'plugins' => [
        'legend' => [
            'display' => true,
            'position' => 'top',
        ],
        'tooltip' => [
            'callbacks' => [
                'label' => 'function(context) {
                    let label = context.dataset.label || "";
                    if (label) {
                        label += ": ";
                    }
                    if (context.parsed.y !== null) {
                        label += new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(context.parsed.y);
                    }
                    return label;
                }'
            ]
        ],
        'datalabels' => [
            'anchor' => 'end', // Posisi label (di akhir bar)
            'align' => 'top', // Penyelarasan label relatif terhadap jangkar
            'formatter' => 'function(value, context) { // Format angka sebagai mata uang Rupiah tanpa desimal
                return "Rp " + new Intl.NumberFormat("id-ID", { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(value);
            }',
            'color' => '#333', // Warna teks label
            'font' => [
                'weight' => 'bold', // Tebal
                'size' => 10
            ]
        ]
    ],
    'scales' => [
        'y' => [
            'beginAtZero' => true,
            'ticks' => [
                'callback' => 'function(value, index, values) {
                    return new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(value);
                }'
            ]
        ]
    ]
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Data Analisa Pembiayaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        .detail-item {
            margin-bottom: 10px;
        }
        .detail-item strong {
            display: inline-block;
            width: 180px;
        }
        .section-header {
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
            margin-bottom: 15px;
            color: #007bff;
        }
        .table-custom {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            border-collapse: collapse;
        }
        .table-custom th, .table-custom td {
            padding: 0.5rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }
        .table-custom thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }
        .chart-container {
            position: relative;
            height: 400px; /* Atur tinggi container grafik */
            width: 100%;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3 class="mb-4">Detail Analisa Pembiayaan</h3>

        <div class="alert alert-info">
            Mode Perhitungan: <strong><?= ucfirst($calculation_type) ?></strong>
        </div>

        <h5 class="section-header">Data Umum</h5>
        <div class="row">
            <div class="col-md-6 detail-item"><strong>Cabang:</strong> <?= htmlspecialchars($cabang) ?></div>
            <div class="col-md-6 detail-item"><strong>Marketing:</strong> <?= htmlspecialchars($marketing) ?></div>
            <div class="col-md-6 detail-item"><strong>Nama Anggota:</strong> <?= htmlspecialchars($anggota) ?></div>
            <div class="col-md-6 detail-item"><strong>Alamat:</strong> <?= htmlspecialchars($alamat) ?></div>
            <div class="col-md-6 detail-item"><strong>Nominal Pengajuan:</strong> <?= formatRupiah($nominal_pengajuan) ?></div>
            <div class="col-md-6 detail-item"><strong>Jenis Usaha:</strong> <?= htmlspecialchars($jenis_usaha) ?></div>
            <div class="col-md-6 detail-item"><strong>Jenis Pembiayaan:</strong> <?= htmlspecialchars($jenis_pembiayaan) ?></div>
            <div class="col-md-6 detail-item"><strong>Waktu Simpan:</strong> <?= htmlspecialchars($timestamp) ?></div>
        </div>

        <?php if ($jenis_usaha == 'pegawai' || $jenis_usaha == 'usaha_dan_pegawai'): ?>
        <h5 class="section-header mt-4">Pendapatan Pegawai</h5>
        <div class="row">
            <div class="col-md-6 detail-item"><strong>Gaji Pokok:</strong> <?= formatRupiah($gaji) ?></div>
            <div class="col-md-6 detail-item"><strong>Total Tunjangan:</strong> <?= formatRupiah($total_tunjangan) ?></div>
            <div class="col-md-6 detail-item"><strong>Biaya Pokok (Transport & Makan):</strong> <?= formatRupiah($biaya_pokok) ?></div>
            <div class="col-md-6 detail-item"><strong>Total Gaji & Tunjangan:</strong> <?= formatRupiah($total_gaji_tunjangan_pegawai) ?></div>
        </div>
        <?php endif; ?>

        <?php if ($jenis_usaha == 'usaha' || $jenis_usaha == 'usaha_dan_pegawai'): ?>
        <h5 class="section-header mt-4">Pendapatan Usaha - <?= htmlspecialchars($jenis_usaha_uraian) ?></h5>
        <h6>Omset Penjualan</h6>
        <table class="table table-bordered table-sm table-custom">
            <thead>
                <tr><th>Uraian 1</th><th>Uraian 2</th><th>Nominal</th></tr>
            </thead>
            <tbody>
                <?php for ($i = 0; $i < 7; $i++): ?>
                    <?php if (!empty($omset_text1[$i]) || !empty($omset_text2[$i]) || $omset_nominal[$i] > 0): ?>
                    <tr>
                        <td><?= htmlspecialchars($omset_text1[$i]) ?></td>
                        <td><?= htmlspecialchars($omset_text2[$i]) ?></td>
                        <td><?= formatRupiah($omset_nominal[$i]) ?></td>
                    </tr>
                    <?php endif; ?>
                <?php endfor; ?>
                <tr>
                    <td colspan="2"><strong>Total Omset:</strong></td>
                    <td><strong><?= formatRupiah($omset_total) ?></strong></td>
                </tr>
            </tbody>
        </table>

        <h6>Harga Pokok Penjualan (HPP)</h6>
        <table class="table table-bordered table-sm table-custom">
            <thead>
                <tr><th>Uraian 1</th><th>Uraian 2</th><th>Nominal</th></tr>
            </thead>
            <tbody>
                <?php for ($i = 0; $i < 7; $i++): ?>
                    <?php if (!empty($hpp_text1[$i]) || !empty($hpp_text2[$i]) || $hpp_nominal[$i] > 0): ?>
                    <tr>
                        <td><?= htmlspecialchars($hpp_text1[$i]) ?></td>
                        <td><?= htmlspecialchars($hpp_text2[$i]) ?></td>
                        <td><?= formatRupiah($hpp_nominal[$i]) ?></td>
                    </tr>
                    <?php endif; ?>
                <?php endfor; ?>
                <tr>
                    <td colspan="2"><strong>Total HPP:</strong></td>
                    <td><strong><?= formatRupiah($hpp_total) ?></strong></td>
                </tr>
            </tbody>
        </table>

        <h6>Biaya Operasional</h6>
        <table class="table table-bordered table-sm table-custom">
            <thead>
                <tr><th>Uraian 1</th><th>Uraian 2</th><th>Nominal</th></tr>
            </thead>
            <tbody>
                <?php for ($i = 0; $i < 7; $i++): ?>
                    <?php if (!empty($operasional_text1[$i]) || !empty($operasional_text2[$i]) || $operasional_nominal[$i] > 0): ?>
                    <tr>
                        <td><?= htmlspecialchars($operasional_text1[$i]) ?></td>
                        <td><?= htmlspecialchars($operasional_text2[$i]) ?></td>
                        <td><?= formatRupiah($operasional_nominal[$i]) ?></td>
                    </tr>
                    <?php endif; ?>
                <?php endfor; ?>
                <tr>
                    <td colspan="2"><strong>Total Operasional:</strong></td>
                    <td><strong><?= formatRupiah($operasional_total) ?></strong></td>
                </tr>
            </tbody>
        </table>
        <div class="row">
            <div class="col-md-6 detail-item"><strong>Laba Usaha:</strong> <?= formatRupiah($laba_usaha) ?></div>
        </div>
        <?php endif; ?>

        <h5 class="section-header mt-4">Pengeluaran</h5>
        <h6>Pengeluaran Rumah Tangga</h6>
        <div class="row">
            <div class="col-md-6 detail-item"><strong>Makan & Minum:</strong> <?= formatRupiah($makan_minum) ?></div>
            <div class="col-md-6 detail-item"><strong>Anak (Uang Saku dll):</strong> <?= formatRupiah($anak) ?></div>
            <div class="col-md-6 detail-item"><strong>Pendidikan Anak:</strong> <?= formatRupiah($pendidikan) ?></div>
            <div class="col-md-6 detail-item"><strong>Listrik:</strong> <?= formatRupiah($listrik) ?></div>
            <div class="col-md-6 detail-item"><strong>Air:</strong> <?= formatRupiah($air) ?></div>
            <div class="col-md-6 detail-item"><strong>Transportasi:</strong> <?= formatRupiah($transport) ?></div>
            <div class="col-md-6 detail-item"><strong>Pengeluaran Lain-lain RT:</strong> <?= formatRupiah($pengeluaran_lain_rt) ?></div>
            <div class="col-md-6 detail-item"><strong>Total Pengeluaran Rumah Tangga:</strong> <?= formatRupiah($pengeluaran_rumah_tangga_total) ?></div>
        </div>

        <h6>Pengeluaran Angsuran/Arisan/Iuran</h6>
        <div class="row">
            <div class="col-md-6 detail-item"><strong>Angsuran Bank/Lainnya:</strong> <?= formatRupiah($angsuran1) ?></div>
            <div class="col-md-6 detail-item"><strong>BPJS/Asuransi:</strong> <?= formatRupiah($bpjs) ?></div>
            <div class="col-md-6 detail-item"><strong>Arisan:</strong> <?= formatRupiah($arisan) ?></div>
            <div class="col-md-6 detail-item"><strong>Iuran-iuran:</strong> <?= formatRupiah($iuran) ?></div>
            <div class="col-md-6 detail-item"><strong>Angsuran Lain-lain:</strong> <?= formatRupiah($angsuran_lain) ?></div>
            <div class="col-md-6 detail-item"><strong>Total Angsuran/Arisan/Iuran:</strong> <?= formatRupiah($pengeluaran_angsuran_arisan_iuran_total) ?></div>
        </div>
        <div class="row">
            <div class="col-md-6 detail-item"><strong>Angsuran KSPPS MUI:</strong> <?= formatRupiah($pengeluaran_kspps) ?></div>
            <div class="col-md-6 detail-item"><strong>Total Keseluruhan Pengeluaran:</strong> <?= formatRupiah($total_pengeluaran) ?></div>
        </div>

        <h5 class="section-header mt-4">Analisa Keuangan</h5>
        <div class="row">
            <div class="col-md-6 detail-item"><strong>Total Pendapatan Keseluruhan:</strong> <?= formatRupiah($total_pendapatan_keseluruhan) ?></div>
            <div class="col-md-6 detail-item"><strong>Total Seluruh Pengeluaran (Diluar Angsuran KSPPS):</strong> <?= formatRupiah($total_pengeluaran_diluar_kspps) ?></div>
            <div class="col-md-6 detail-item"><strong>Sisa Dana Aktual:</strong> <?= formatRupiah($sisa_dana_aktual) ?></div>
            <div class="col-md-6 detail-item"><strong>Disposable Income (Final):</strong> <?= formatRupiah($disposable_income_final) ?></div>
            <div class="col-md-6 detail-item">
                <strong>Disposable Income Status:</strong>
                <span class="badge bg-<?= ($status_disposable_income == 'Diterima' ? 'success' : ($status_disposable_income == 'Dipertimbangkan' ? 'warning' : 'danger')) ?>">
                    <?= htmlspecialchars($status_disposable_income) ?>
                </span>
            </div>
        </div>

        <h5 class="section-header mt-4">Aset</h5>
        <div class="row">
            <div class="col-md-6 detail-item"><strong>Kas Tunai:</strong> <?= formatRupiah($kas_tunai) ?></div>
            <div class="col-md-6 detail-item"><strong>Kas Bank:</strong> <?= formatRupiah($kas_bank) ?></div>
            <div class="col-md-6 detail-item"><strong>Piutang:</strong> <?= formatRupiah($piutang) ?></div>
            <div class="col-md-6 detail-item"><strong>Persediaan:</strong> <?= formatRupiah($persediaan) ?></div>
            <div class="col-md-6 detail-item"><strong>Emas:</strong> <?= formatRupiah($emas) ?></div>
            <div class="col-md-6 detail-item"><strong>Surat Berharga:</strong> <?= formatRupiah($surat_berharga) ?></div>
            <div class="col-md-6 detail-item"><strong>Total Aset Lancar:</strong> <?= formatRupiah($total_aset_lancar) ?></div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6 detail-item"><strong>Mobil:</strong> <?= formatRupiah($mobil) ?></div>
            <div class="col-md-6 detail-item"><strong>Motor:</strong> <?= formatRupiah($motor) ?></div>
            <div class="col-md-6 detail-item"><strong>Rumah:</strong> <?= formatRupiah($rumah) ?></div>
            <div class="col-md-6 detail-item"><strong>Tanah:</strong> <?= formatRupiah($tanah) ?></div>
            <div class="col-md-6 detail-item"><strong>Gudang/Kios:</strong> <?= formatRupiah($gudang) ?></div>
            <div class="col-md-6 detail-item"><strong>Kantor/Tempat Usaha:</strong> <?= formatRupiah($kantor) ?></div>
            <div class="col-md-6 detail-item"><strong>Total Aset Tetap:</strong> <?= formatRupiah($total_aset_tetap) ?></div>
            <div class="col-md-6 detail-item"><strong>Total Keseluruhan Aset:</strong> <?= formatRupiah($total_aset) ?></div>
        </div>

        <h5 class="section-header mt-4">Kewajiban</h5>
        <h6>Kewajiban Lancar</h6>
        <div class="row">
            <div class="col-md-6 detail-item"><strong>DP Diterima:</strong> <?= formatRupiah($dp_diterima) ?></div>
            <div class="col-md-6 detail-item"><strong>Biaya yang Harus Dibayar:</strong> <?= formatRupiah($biaya_harus_bayar) ?></div>
            <div class="col-md-6 detail-item"><strong>Lain-lain:</strong> <?= formatRupiah($dll_kewajiban_lancar) ?></div>
            <div class="col-md-6 detail-item"><strong>Total Kewajiban Lancar:</strong> <?= formatRupiah($total_kewajiban_lancar) ?></div>
        </div>
        <h6>Kewajiban Tetap</h6>
        <div class="row">
            <div class="col-md-6 detail-item"><strong>Modal Pinjaman/Utang Bank:</strong> <?= formatRupiah($modal_pinjaman) ?></div>
            <div class="col-md-6 detail-item"><strong>Modal Sendiri:</strong> <?= formatRupiah($modal_bank) ?></div>
            <div class="col-md-6 detail-item"><strong>Total Kewajiban Tetap:</strong> <?= formatRupiah($total_kewajiban_tetap) ?></div>
            <div class="col-md-6 detail-item"><strong>Total Keseluruhan Kewajiban:</strong> <?= formatRupiah($total_keseluruhan_kewajiban) ?></div>
        </div>

        <h5 class="section-header mt-4">Rasio Keuangan</h5>
        <div class="row">
            <div class="col-md-6 detail-item"><strong>Net Worth (Kekayaan Bersih):</strong> <?= formatRupiah($net_worth) ?></div>
            <div class="col-md-6 detail-item"><strong>Modal:</strong> <?= formatRupiah($modal_total) ?></div>
            <div class="col-md-6 detail-item">
                <strong>Repayment Capacity (RPC):</strong> <?= number_format($rpc_value_raw, 2) ?>
                <span class="badge bg-<?= ($rpc_status == 'Diterima' || $rpc_status == 'Baik' ? 'success' : ($rpc_status == 'Dipertimbangkan' ? 'warning' : 'danger')) ?>">
                    <?= htmlspecialchars($rpc_status) ?>
                </span>
            </div>
            <div class="col-md-6 detail-item">
                <strong>Debt to Equity Ratio (DER):</strong> <?= number_format($der_value, 2) ?>
                <span class="badge bg-<?= ($der_status == 'Baik' ? 'success' : 'danger') ?>">
                    <?= htmlspecialchars($der_status) ?>
                </span>
            </div>
            <div class="col-md-6 detail-item">
                <strong>Profitability Ratio:</strong> <?= number_format($profitability_value_raw, 2) ?>%
                <span class="badge bg-<?= ($status_profitability == 'Baik' ? 'success' : 'danger') ?>">
                    <?= htmlspecialchars($status_profitability) ?>
                </span>
            </div>
        </div>

        <h5 class="section-header mt-4">Dokumen Pendukung</h5>
        <div class="row">
            <div class="col-md-12 detail-item">
                <?php if (!empty($dokumen_filename)): ?>
                    <strong>File Dokumen:</strong> <a href="../uploads/<?= htmlspecialchars($dokumen_filename) ?>" target="_blank"><?= htmlspecialchars($dokumen_filename) ?></a>
                <?php else: ?>
                    Tidak ada dokumen diunggah.
                <?php endif; ?>
            </div>
        </div>

        <h5 class="section-header mt-4">Ringkasan Grafik</h5>
        <div class="row">
            <div class="col-md-6">
                <div class="chart-container">
                    <canvas id="pendapatanChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <canvas id="pengeluaranChart"></canvas>
                </div>
            </div>
        </div>

        <a href="admin_panel.php" class="btn btn-secondary mt-4">Kembali ke Admin Panel</a>
    </div>

    <script>
        // Register Chart.js Datalabels plugin
        Chart.register(ChartDataLabels);

        // Data untuk grafik Pendapatan
        const pendapatanData = {
            labels: ['Gaji & Tunjangan Pegawai', 'Laba Usaha', 'Total Pendapatan Keseluruhan'],
            datasets: [{
                label: 'Nominal',
                data: [<?= $total_gaji_tunjangan_pegawai ?>, <?= $laba_usaha ?>, <?= $total_pendapatan_keseluruhan ?>],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        };

        // Data untuk grafik Pengeluaran
        const pengeluaranData = {
            labels: ['Pengeluaran Rumah Tangga', 'Angsuran/Arisan/Iuran', 'Angsuran KSPPS MUI', 'Total Pengeluaran'],
            datasets: [{
                label: 'Nominal',
                data: [<?= $pengeluaran_rumah_tangga_total ?>, <?= $pengeluaran_angsuran_arisan_iuran_total ?>, <?= $pengeluaran_kspps ?>, <?= $total_pengeluaran ?>],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        };

        // Opsi grafik umum
        const commonChartOptions = <?= json_encode($commonChartOptions) ?>;

        // Buat grafik Pendapatan
        const pendapatanCtx = document.getElementById('pendapatanChart').getContext('2d');
        new Chart(pendapatanCtx, {
            type: 'bar',
            data: pendapatanData,
            options: {
                ...commonChartOptions, // Gabungkan opsi umum
                plugins: {
                    ...commonChartOptions.plugins, // Gabungkan plugin umum
                    title: { // Tambahkan judul spesifik untuk grafik ini
                        display: true,
                        text: 'Ringkasan Pendapatan',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        color: '#333'
                    }
                }
            }
        });

        // Buat grafik Pengeluaran
        const pengeluaranCtx = document.getElementById('pengeluaranChart').getContext('2d');
        new Chart(pengeluaranCtx, {
            type: 'bar',
            data: pengeluaranData,
            options: {
                ...commonChartOptions, // Gabungkan opsi umum
                plugins: {
                    ...commonChartOptions.plugins, // Gabungkan plugin umum
                    title: { // Tambahkan judul spesifik untuk grafik ini
                        display: true,
                        text: 'Ringkasan Pengeluaran',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        color: '#333'
                    }
                }
            }
        });
    </script>
</body>
</html>