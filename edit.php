<?php
// admin/edit.php
$id = $_GET['id'] ?? -1;
$file = '../data/data.txt';
$lines = file($file, FILE_IGNORE_NEW_LINES);
$data = isset($lines[$id]) ? explode('|', $lines[$id]) : [];

// Fungsi untuk memformat nilai dari file ke tampilan input Rupiah (dengan titik ribuan, tanpa Rp.)
function formatRupiahForInput($value) {
    if (is_numeric($value)) {
        return number_format((float)$value, 0, ',', '.');
    }
    return '0';
}

// Fungsi pembantu untuk membersihkan dan mengubah ke float (dari input form)
function cleanAndFloatFromInput($value) {
    $cleaned = str_replace(['Rp. ', '.', ','], '', $value);
    return floatval($cleaned);
}

// Fungsi pembantu untuk membersihkan string teks
function cleanString($value) {
    return htmlspecialchars(strip_tags(trim($value)));
}

// Memastikan jumlah kolom yang diharapkan adalah 112
$expected_fields = 112;
if (count($data) < $expected_fields) {
    $missing_fields = $expected_fields - count($data);
    for ($i = 0; $i < $missing_fields; $i++) {
        $data[] = ''; // Tambahkan string kosong untuk field yang tidak ada
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated_data = [];

    // --- STRUKTUR DATA LENGKAP (Total 112 field - Indeks 0 sampai 111) ---
    // Pastikan urutan dan jumlah field ini konsisten dengan simpan.php dan detail.php

    // Data Umum (Indeks 0-6)
    $updated_data[] = cleanString($_POST['cabang'] ?? ''); // 0
    $updated_data[] = cleanString($_POST['marketing'] ?? ''); // 1
    $updated_data[] = cleanString($_POST['anggota'] ?? ''); // 2
    $updated_data[] = cleanString($_POST['alamat'] ?? ''); // 3
    $updated_data[] = cleanAndFloatFromInput($_POST['nominal_pengajuan'] ?? 0); // 4
    $updated_data[] = cleanString($_POST['jenis_usaha'] ?? ''); // 5
    $updated_data[] = cleanString($_POST['jenis_pembiayaan'] ?? ''); // 6

    // Data Pendapatan Pegawai (Indeks 7-9)
    $updated_data[] = cleanAndFloatFromInput($_POST['gaji'] ?? 0); // 7
    $updated_data[] = cleanAndFloatFromInput($_POST['total_tunjangan'] ?? 0); // 8
    $updated_data[] = cleanAndFloatFromInput($_POST['biaya_pokok'] ?? 0); // 9

    // Data Pendapatan Usaha - Uraian dan Omset (Indeks 10-32)
    $updated_data[] = cleanString($_POST['jenis_usaha_uraian'] ?? ''); // 10
    for ($i = 1; $i <= 7; $i++) {
        $updated_data[] = cleanString($_POST['uraian_omset_' . $i] ?? ''); // 11, 14, 17, 20, 23, 26, 29
        $updated_data[] = cleanString($_POST['satuan_omset_' . $i] ?? ''); // 12, 15, 18, 21, 24, 27, 30
        $updated_data[] = cleanAndFloatFromInput($_POST['nominal_omset_' . $i] ?? 0); // 13, 16, 19, 22, 25, 28, 31
    }
    $updated_data[] = cleanAndFloatFromInput($_POST['omset_total'] ?? 0); // 32

    // Data Pendapatan Usaha - HPP (Indeks 33-54)
    for ($i = 1; $i <= 7; $i++) {
        $updated_data[] = cleanString($_POST['uraian_hpp_' . $i] ?? ''); // 33, 36, 39, 42, 45, 48, 51
        $updated_data[] = cleanString($_POST['satuan_hpp_' . $i] ?? ''); // 34, 37, 40, 43, 46, 49, 52
        $updated_data[] = cleanAndFloatFromInput($_POST['nominal_hpp_' . $i] ?? 0); // 35, 38, 41, 44, 47, 50, 53
    }
    $updated_data[] = cleanAndFloatFromInput($_POST['hpp_total'] ?? 0); // 54

    // Data Pendapatan Usaha - Biaya Operasional (Indeks 55-76)
    for ($i = 1; $i <= 7; $i++) {
        $updated_data[] = cleanString($_POST['uraian_operasional_' . $i] ?? ''); // 55, 58, 61, 64, 67, 70, 73
        $updated_data[] = cleanString($_POST['satuan_operasional_' . $i] ?? ''); // 56, 59, 62, 65, 68, 71, 74
        $updated_data[] = cleanAndFloatFromInput($_POST['nominal_operasional_' . $i] ?? 0); // 57, 60, 63, 66, 69, 72, 75
    }
    $updated_data[] = cleanAndFloatFromInput($_POST['operasional_total'] ?? 0); // 76

    // Pengeluaran Rumah Tangga (Indeks 77-83)
    $updated_data[] = cleanAndFloatFromInput($_POST['makan_minum'] ?? 0); // 77
    $updated_data[] = cleanAndFloatFromInput($_POST['anak'] ?? 0); // 78
    $updated_data[] = cleanAndFloatFromInput($_POST['pendidikan'] ?? 0); // 79
    $updated_data[] = cleanAndFloatFromInput($_POST['listrik'] ?? 0); // 80
    $updated_data[] = cleanAndFloatFromInput($_POST['air'] ?? 0); // 81
    $updated_data[] = cleanAndFloatFromInput($_POST['transport'] ?? 0); // 82
    $updated_data[] = cleanAndFloatFromInput($_POST['pengeluaran_lain_rt'] ?? 0); // 83

    // Pengeluaran Angsuran/Arisan/Iuran (Indeks 84-88)
    $updated_data[] = cleanAndFloatFromInput($_POST['angsuran1'] ?? 0); // 84
    $updated_data[] = cleanAndFloatFromInput($_POST['bpjs'] ?? 0); // 85
    $updated_data[] = cleanAndFloatFromInput($_POST['arisan'] ?? 0); // 86
    $updated_data[] = cleanAndFloatFromInput($_POST['iuran'] ?? 0); // 87
    $updated_data[] = cleanAndFloatFromInput($_POST['angsuran_lain'] ?? 0); // 88

    // Aset Lancar (Indeks 89-94)
    $updated_data[] = cleanAndFloatFromInput($_POST['kas_tunai'] ?? 0); // 89
    $updated_data[] = cleanAndFloatFromInput($_POST['kas_bank'] ?? 0); // 90
    $updated_data[] = cleanAndFloatFromInput($_POST['piutang'] ?? 0); // 91
    $updated_data[] = cleanAndFloatFromInput($_POST['persediaan'] ?? 0); // 92
    $updated_data[] = cleanAndFloatFromInput($_POST['emas'] ?? 0); // 93
    $updated_data[] = cleanAndFloatFromInput($_POST['surat_berharga'] ?? 0); // 94

    // Aset Tetap (Indeks 95-100)
    $updated_data[] = cleanAndFloatFromInput($_POST['mobil'] ?? 0); // 95
    $updated_data[] = cleanAndFloatFromInput($_POST['motor'] ?? 0); // 96
    $updated_data[] = cleanAndFloatFromInput($_POST['rumah'] ?? 0); // 97
    $updated_data[] = cleanAndFloatFromInput($_POST['tanah'] ?? 0); // 98
    $updated_data[] = cleanAndFloatFromInput($_POST['gudang'] ?? 0); // 99
    $updated_data[] = cleanAndFloatFromInput($_POST['kantor'] ?? 0); // 100

    // Kewajiban Lancar (Indeks 101-104)
    $updated_data[] = cleanAndFloatFromInput($_POST['dp_diterima'] ?? 0); // 101
    $updated_data[] = cleanAndFloatFromInput($_POST['biaya_harus_bayar'] ?? 0); // 102
    $updated_data[] = cleanAndFloatFromInput($_POST['dll_kewajiban_lancar'] ?? 0); // 103
    $updated_data[] = cleanAndFloatFromInput($_POST['total_kewajiban_lancar'] ?? 0); // 104

    // Checkbox skip_analysis (Indeks 105)
    $updated_data[] = isset($_POST['skip_analysis']) ? 'yes' : 'no'; // 105

    // Kewajiban Tetap (Indeks 106-107) - Sesuaikan indeks setelah skip_analysis
    $updated_data[] = cleanAndFloatFromInput($_POST['modal_pinjaman'] ?? 0); // 106
    $updated_data[] = cleanAndFloatFromInput($_POST['modal_bank'] ?? 0); // 107

    // Total Keseluruhan Kewajiban (Indeks 108)
    $updated_data[] = cleanAndFloatFromInput($_POST['total_keseluruhan_kewajiban'] ?? 0); // 108

    // Pengeluaran KSPPS MUI (Indeks 109)
    $updated_data[] = cleanAndFloatFromInput($_POST['pengeluaran_kspps'] ?? 0); // 109

    // Dokumen filename (Indeks 110) - ambil dari data lama jika tidak ada upload baru
    $dokumen_filename = $data[110] ?? ''; // Default ke nilai yang sudah ada
    // Tangani unggahan file jika ada
    if (isset($_FILES['dokumen']) && $_FILES['dokumen']['error'] === UPLOAD_ERR_OK) {
        $file_info = $_FILES['dokumen'];
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = basename($file_info['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

        if (in_array($fileExt, $allowedExts) && $file_info['size'] <= 5000000) { // Max 5MB
            $uniqueFileName = uniqid('doc_', true) . '.' . $fileExt;
            $uploadFilePath = $uploadDir . $uniqueFileName;
            if (move_uploaded_file($file_info['tmp_name'], $uploadFilePath)) {
                $dokumen_filename = $uniqueFileName;
            } else {
                error_log("Failed to move uploaded file in edit.php: " . $file_info['tmp_name'] . " to " . $uploadFilePath);
            }
        } else {
            error_log("Invalid file type or size in edit.php: " . $fileName . " (Size: " . $file_info['size'] . ", Ext: " . $fileExt . ")");
        }
    }
    $updated_data[] = $dokumen_filename; // 110

    // Waktu Simpan (Indeks 111) - Pertahankan waktu simpan asli jika tidak ada perubahan, atau update jika diperlukan
    // Untuk tujuan edit, kita bisa mempertahankan timestamp asli atau mengupdatenya ke waktu edit
    // Mari kita update ke waktu edit
    $updated_data[] = date('Y-m-d H:i:s'); // 111

    // Verifikasi total jumlah field
    if (count($updated_data) !== $expected_fields) {
        error_log("Updated data array count mismatch! Expected " . $expected_fields . ", got " . count($updated_data));
        header("Location: admin_panel.php?status=error&message=update_data_mismatch");
        exit;
    }

    $lines[$id] = implode('|', $updated_data);
    file_put_contents($file, implode("\n", $lines));

    header('Location: admin_panel.php?status=success_edit');
    exit;
}

// Data yang akan dimuat ke dalam form untuk diedit
// Data Umum (Indeks 0-6)
$cabang = $data[0] ?? '';
$marketing = $data[1] ?? '';
$anggota = $data[2] ?? '';
$alamat = $data[3] ?? '';
$nominal_pengajuan = formatRupiahForInput($data[4] ?? 0);
$jenis_usaha = $data[5] ?? '';
$jenis_pembiayaan = $data[6] ?? '';

// Data Pendapatan Pegawai (Indeks 7-9)
$gaji = formatRupiahForInput($data[7] ?? 0);
$total_tunjangan = formatRupiahForInput($data[8] ?? 0);
$biaya_pokok = formatRupiahForInput($data[9] ?? 0);

// Data Pendapatan Usaha - Uraian dan Omset (Indeks 10-32)
$jenis_usaha_uraian = $data[10] ?? '';
$uraian_omset = [];
$satuan_omset = [];
$nominal_omset = [];
for ($i = 0; $i < 7; $i++) {
    $base_index = 11 + ($i * 3);
    $uraian_omset[] = $data[$base_index] ?? '';
    $satuan_omset[] = $data[$base_index + 1] ?? '';
    $nominal_omset[] = formatRupiahForInput($data[$base_index + 2] ?? 0);
}
$omset_total = formatRupiahForInput($data[32] ?? 0);

// Data Pendapatan Usaha - HPP (Indeks 33-54)
$uraian_hpp = [];
$satuan_hpp = [];
$nominal_hpp = [];
for ($i = 0; $i < 7; $i++) {
    $base_index = 33 + ($i * 3);
    $uraian_hpp[] = $data[$base_index] ?? '';
    $satuan_hpp[] = $data[$base_index + 1] ?? '';
    $nominal_hpp[] = formatRupiahForInput($data[$base_index + 2] ?? 0);
}
$hpp_total = formatRupiahForInput($data[54] ?? 0);

// Data Pendapatan Usaha - Biaya Operasional (Indeks 55-76)
$uraian_operasional = [];
$satuan_operasional = [];
$nominal_operasional = [];
for ($i = 0; $i < 7; $i++) {
    $base_index = 55 + ($i * 3);
    $uraian_operasional[] = $data[$base_index] ?? '';
    $satuan_operasional[] = $data[$base_index + 1] ?? '';
    $nominal_operasional[] = formatRupiahForInput($data[$base_index + 2] ?? 0);
}
$operasional_total = formatRupiahForInput($data[76] ?? 0);

// Pengeluaran Rumah Tangga (Indeks 77-83)
$makan_minum = formatRupiahForInput($data[77] ?? 0);
$anak = formatRupiahForInput($data[78] ?? 0);
$pendidikan = formatRupiahForInput($data[79] ?? 0);
$listrik = formatRupiahForInput($data[80] ?? 0);
$air = formatRupiahForInput($data[81] ?? 0);
$transport = formatRupiahForInput($data[82] ?? 0);
$pengeluaran_lain_rt = formatRupiahForInput($data[83] ?? 0);

// Pengeluaran Angsuran/Arisan/Iuran (Indeks 84-88)
$angsuran1 = formatRupiahForInput($data[84] ?? 0);
$bpjs = formatRupiahForInput($data[85] ?? 0);
$arisan = formatRupiahForInput($data[86] ?? 0);
$iuran = formatRupiahForInput($data[87] ?? 0);
$angsuran_lain = formatRupiahForInput($data[88] ?? 0);

// Aset Lancar (Indeks 89-94)
$kas_tunai = formatRupiahForInput($data[89] ?? 0);
$kas_bank = formatRupiahForInput($data[90] ?? 0);
$piutang = formatRupiahForInput($data[91] ?? 0);
$persediaan = formatRupiahForInput($data[92] ?? 0);
$emas = formatRupiahForInput($data[93] ?? 0);
$surat_berharga = formatRupiahForInput($data[94] ?? 0);

// Aset Tetap (Indeks 95-100)
$mobil = formatRupiahForInput($data[95] ?? 0);
$motor = formatRupiahForInput($data[96] ?? 0);
$rumah = formatRupiahForInput($data[97] ?? 0);
$tanah = formatRupiahForInput($data[98] ?? 0);
$gudang = formatRupiahForInput($data[99] ?? 0);
$kantor = formatRupiahForInput($data[100] ?? 0);

// Kewajiban Lancar (Indeks 101-104)
$dp_diterima = formatRupiahForInput($data[101] ?? 0);
$biaya_harus_bayar = formatRupiahForInput($data[102] ?? 0);
$dll_kewajiban_lancar = formatRupiahForInput($data[103] ?? 0);
$total_kewajiban_lancar = formatRupiahForInput($data[104] ?? 0);

// Checkbox skip_analysis (Indeks 105)
$skip_analysis = ($data[105] ?? 'no') === 'yes';

// Kewajiban Tetap (Indeks 106-107)
$modal_pinjaman = formatRupiahForInput($data[106] ?? 0);
$modal_bank = formatRupiahForInput($data[107] ?? 0);

// Total Keseluruhan Kewajiban (Indeks 108)
$total_keseluruhan_kewajiban = formatRupiahForInput($data[108] ?? 0);

// Pengeluaran KSPPS MUI (Indeks 109)
$pengeluaran_kspps = formatRupiahForInput($data[109] ?? 0);

// Dokumen filename (Indeks 110)
$dokumen_filename = $data[110] ?? '';

// Waktu Simpan (Indeks 111)
$timestamp = $data[111] ?? '';

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Analisa Pembiayaan KSPPS MUI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            background-color: #fff;
        }
        .form-section h5 {
            margin-bottom: 1rem;
            color: #0056b3;
        }
        .input-group-text {
            width: 80px; /* Lebar tetap untuk label input-group */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Edit Data Analisa Pembiayaan</h4>
            </div>
            <div class="card-body">
                <form action="edit.php?id=<?= htmlspecialchars($id) ?>" method="POST" enctype="multipart/form-data">

                    <div class="form-section">
                        <h5>Data Umum</h5>
                        <div class="mb-3">
                            <label for="cabang" class="form-label">Cabang</label>
                            <input type="text" class="form-control" id="cabang" name="cabang" value="<?= htmlspecialchars($cabang) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="marketing" class="form-label">Marketing</label>
                            <input type="text" class="form-control" id="marketing" name="marketing" value="<?= htmlspecialchars($marketing) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="anggota" class="form-label">Nama Anggota</label>
                            <input type="text" class="form-control" id="anggota" name="anggota" value="<?= htmlspecialchars($anggota) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?= htmlspecialchars($alamat) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="nominal_pengajuan" class="form-label">Nominal Pengajuan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah" id="nominal_pengajuan" name="nominal_pengajuan" value="<?= htmlspecialchars($nominal_pengajuan) ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="jenis_usaha" class="form-label">Jenis Usaha</label>
                            <select class="form-select" id="jenis_usaha" name="jenis_usaha" required>
                                <option value="pegawai" <?= ($jenis_usaha == 'pegawai') ? 'selected' : '' ?>>Pegawai</option>
                                <option value="usaha" <?= ($jenis_usaha == 'usaha') ? 'selected' : '' ?>>Usaha</option>
                                <option value="usaha_dan_pegawai" <?= ($jenis_usaha == 'usaha_dan_pegawai') ? 'selected' : '' ?>>Usaha dan Pegawai</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jenis_pembiayaan" class="form-label">Jenis Pembiayaan</label>
                            <select class="form-select" id="jenis_pembiayaan" name="jenis_pembiayaan" required>
                                <option value="Reguler" <?= ($jenis_pembiayaan == 'Reguler') ? 'selected' : '' ?>>Reguler</option>
                                <option value="Musiman" <?= ($jenis_pembiayaan == 'Musiman') ? 'selected' : '' ?>>Musiman</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-section" id="pegawaiSection">
                        <h5>Pendapatan Pegawai</h5>
                        <div class="mb-3">
                            <label for="gaji" class="form-label">Gaji Pokok</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah" id="gaji" name="gaji" value="<?= htmlspecialchars($gaji) ?>" onkeyup="hitungTotalPendapatanPegawai()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="total_tunjangan" class="form-label">Total Tunjangan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah" id="total_tunjangan" name="total_tunjangan" value="<?= htmlspecialchars($total_tunjangan) ?>" onkeyup="hitungTotalPendapatanPegawai()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="biaya_pokok" class="form-label">Biaya Pokok (Opsional)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah" id="biaya_pokok" name="biaya_pokok" value="<?= htmlspecialchars($biaya_pokok) ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="total_gaji_tunjangan_pegawai_display" class="form-label">Total Gaji & Tunjangan Pegawai</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control" id="total_gaji_tunjangan_pegawai_display" value="0" readonly>
                                <input type="hidden" id="total_gaji_tunjangan_pegawai_hidden" name="total_gaji_tunjangan_pegawai">
                            </div>
                        </div>
                    </div>

                    <div class="form-section" id="usahaSection">
                        <h5>Pendapatan Usaha</h5>
                        <div class="mb-3">
                            <label for="jenis_usaha_uraian" class="form-label">Uraian Jenis Usaha</label>
                            <input type="text" class="form-control" id="jenis_usaha_uraian" name="jenis_usaha_uraian" value="<?= htmlspecialchars($jenis_usaha_uraian) ?>">
                        </div>
                        <h6>Omset Per Bulan</h6>
                        <?php for ($i = 0; $i < 7; $i++): ?>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="uraian_omset_<?= $i+1 ?>" placeholder="Uraian Omset <?= $i+1 ?>" value="<?= htmlspecialchars($uraian_omset[$i]) ?>">
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="satuan_omset_<?= $i+1 ?>" placeholder="Satuan (contoh: kg, liter)" value="<?= htmlspecialchars($satuan_omset[$i]) ?>">
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" class="form-control rupiah omset-input" name="nominal_omset_<?= $i+1 ?>" value="<?= htmlspecialchars($nominal_omset[$i]) ?>" onkeyup="hitungOmset()">
                                </div>
                            </div>
                        </div>
                        <?php endfor; ?>
                        <div class="mb-3">
                            <label for="omset_total_display" class="form-label">Total Omset</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control" id="omset_total_display" value="0" readonly>
                                <input type="hidden" id="omset_total_hidden" name="omset_total">
                            </div>
                        </div>

                        <h6>HPP (Harga Pokok Penjualan) Per Bulan</h6>
                        <?php for ($i = 0; $i < 7; $i++): ?>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="uraian_hpp_<?= $i+1 ?>" placeholder="Uraian HPP <?= $i+1 ?>" value="<?= htmlspecialchars($uraian_hpp[$i]) ?>">
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="satuan_hpp_<?= $i+1 ?>" placeholder="Satuan (contoh: kg, liter)" value="<?= htmlspecialchars($satuan_hpp[$i]) ?>">
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" class="form-control rupiah hpp-input" name="nominal_hpp_<?= $i+1 ?>" value="<?= htmlspecialchars($nominal_hpp[$i]) ?>" onkeyup="hitungHPP()">
                                </div>
                            </div>
                        </div>
                        <?php endfor; ?>
                        <div class="mb-3">
                            <label for="hpp_total_display" class="form-label">Total HPP</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control" id="hpp_total_display" value="0" readonly>
                                <input type="hidden" id="hpp_total_hidden" name="hpp_total">
                            </div>
                        </div>

                        <h6>Biaya Operasional Per Bulan</h6>
                        <?php for ($i = 0; $i < 7; $i++): ?>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="uraian_operasional_<?= $i+1 ?>" placeholder="Uraian Operasional <?= $i+1 ?>" value="<?= htmlspecialchars($uraian_operasional[$i]) ?>">
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="satuan_operasional_<?= $i+1 ?>" placeholder="Satuan (contoh: unit, bulan)" value="<?= htmlspecialchars($satuan_operasional[$i]) ?>">
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="text" class="form-control rupiah operasional-input" name="nominal_operasional_<?= $i+1 ?>" value="<?= htmlspecialchars($nominal_operasional[$i]) ?>" onkeyup="hitungOperasional()">
                                </div>
                            </div>
                        </div>
                        <?php endfor; ?>
                        <div class="mb-3">
                            <label for="operasional_total_display" class="form-label">Total Biaya Operasional</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control" id="operasional_total_display" value="0" readonly>
                                <input type="hidden" id="operasional_total_hidden" name="operasional_total">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h5>Data Pengeluaran</h5>
                        <h6>Pengeluaran Rumah Tangga</h6>
                        <div class="mb-3">
                            <label for="makan_minum" class="form-label">Makan & Minum</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah pengeluaran-rt-input" id="makan_minum" name="makan_minum" value="<?= htmlspecialchars($makan_minum) ?>" onkeyup="hitungTotalPengeluaranRT()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="anak" class="form-label">Anak</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah pengeluaran-rt-input" id="anak" name="anak" value="<?= htmlspecialchars($anak) ?>" onkeyup="hitungTotalPengeluaranRT()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="pendidikan" class="form-label">Pendidikan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah pengeluaran-rt-input" id="pendidikan" name="pendidikan" value="<?= htmlspecialchars($pendidikan) ?>" onkeyup="hitungTotalPengeluaranRT()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="listrik" class="form-label">Listrik</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah pengeluaran-rt-input" id="listrik" name="listrik" value="<?= htmlspecialchars($listrik) ?>" onkeyup="hitungTotalPengeluaranRT()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="air" class="form-label">Air</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah pengeluaran-rt-input" id="air" name="air" value="<?= htmlspecialchars($air) ?>" onkeyup="hitungTotalPengeluaranRT()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="transport" class="form-label">Transportasi</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah pengeluaran-rt-input" id="transport" name="transport" value="<?= htmlspecialchars($transport) ?>" onkeyup="hitungTotalPengeluaranRT()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="pengeluaran_lain_rt" class="form-label">Pengeluaran Lain-lain Rumah Tangga</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah pengeluaran-rt-input" id="pengeluaran_lain_rt" name="pengeluaran_lain_rt" value="<?= htmlspecialchars($pengeluaran_lain_rt) ?>" onkeyup="hitungTotalPengeluaranRT()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="pengeluaran_rumah_tangga_total_display" class="form-label">Total Pengeluaran Rumah Tangga</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control" id="pengeluaran_rumah_tangga_total_display" value="0" readonly>
                                <input type="hidden" id="pengeluaran_rumah_tangga_total_hidden" name="pengeluaran_rumah_tangga_total">
                            </div>
                        </div>

                        <h6>Pengeluaran Angsuran/Arisan/Iuran</h6>
                        <div class="mb-3">
                            <label for="angsuran1" class="form-label">Angsuran Bank/Lainnya</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah angsuran-input" id="angsuran1" name="angsuran1" value="<?= htmlspecialchars($angsuran1) ?>" onkeyup="hitungTotalAngsuran()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="bpjs" class="form-label">BPJS</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah angsuran-input" id="bpjs" name="bpjs" value="<?= htmlspecialchars($bpjs) ?>" onkeyup="hitungTotalAngsuran()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="arisan" class="form-label">Arisan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah angsuran-input" id="arisan" name="arisan" value="<?= htmlspecialchars($arisan) ?>" onkeyup="hitungTotalAngsuran()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="iuran" class="form-label">Iuran</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah angsuran-input" id="iuran" name="iuran" value="<?= htmlspecialchars($iuran) ?>" onkeyup="hitungTotalAngsuran()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="angsuran_lain" class="form-label">Angsuran Lain-lain</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah angsuran-input" id="angsuran_lain" name="angsuran_lain" value="<?= htmlspecialchars($angsuran_lain) ?>" onkeyup="hitungTotalAngsuran()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="pengeluaran_angsuran_arisan_iuran_total_display" class="form-label">Total Angsuran/Arisan/Iuran</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control" id="pengeluaran_angsuran_arisan_iuran_total_display" value="0" readonly>
                                <input type="hidden" id="pengeluaran_angsuran_arisan_iuran_total_hidden" name="pengeluaran_angsuran_arisan_iuran_total">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="pengeluaran_kspps" class="form-label">Angsuran KSPPS MUI</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah" id="pengeluaran_kspps" name="pengeluaran_kspps" value="<?= htmlspecialchars($pengeluaran_kspps) ?>" onkeyup="hitungTotalPengeluaran()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="total_pengeluaran_display" class="form-label">Total Seluruh Pengeluaran</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control" id="total_pengeluaran_display" value="0" readonly>
                                <input type="hidden" id="total_pengeluaran_hidden" name="total_pengeluaran">
                            </div>
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" value="yes" id="skip_analysis" name="skip_analysis" <?= $skip_analysis ? 'checked' : '' ?>>
                        <label class="form-check-label" for="skip_analysis">
                            Lewati Analisa Aset dan Kewajiban
                        </label>
                    </div>

                    <div class="form-section" id="assetSection">
                        <h5>Analisa Aset</h5>
                        <h6>Aset Lancar</h6>
                        <div class="mb-3">
                            <label for="kas_tunai" class="form-label">Kas Tunai</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah asset-lancar-input" id="kas_tunai" name="kas_tunai" value="<?= htmlspecialchars($kas_tunai) ?>" onkeyup="hitungTotalAsetLancar()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="kas_bank" class="form-label">Kas Bank</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah asset-lancar-input" id="kas_bank" name="kas_bank" value="<?= htmlspecialchars($kas_bank) ?>" onkeyup="hitungTotalAsetLancar()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="piutang" class="form-label">Piutang</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah asset-lancar-input" id="piutang" name="piutang" value="<?= htmlspecialchars($piutang) ?>" onkeyup="hitungTotalAsetLancar()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="persediaan" class="form-label">Persediaan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah asset-lancar-input" id="persediaan" name="persediaan" value="<?= htmlspecialchars($persediaan) ?>" onkeyup="hitungTotalAsetLancar()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="emas" class="form-label">Emas</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah asset-lancar-input" id="emas" name="emas" value="<?= htmlspecialchars($emas) ?>" onkeyup="hitungTotalAsetLancar()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="surat_berharga" class="form-label">Surat Berharga</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah asset-lancar-input" id="surat_berharga" name="surat_berharga" value="<?= htmlspecialchars($surat_berharga) ?>" onkeyup="hitungTotalAsetLancar()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="total_aset_lancar_display" class="form-label">Total Aset Lancar</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control" id="total_aset_lancar_display" value="0" readonly>
                                <input type="hidden" id="total_aset_lancar_hidden" name="total_aset_lancar">
                            </div>
                        </div>

                        <h6>Aset Tetap</h6>
                        <div class="mb-3">
                            <label for="mobil" class="form-label">Mobil</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah asset-tetap-input" id="mobil" name="mobil" value="<?= htmlspecialchars($mobil) ?>" onkeyup="hitungTotalAsetTetap()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="motor" class="form-label">Motor</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah asset-tetap-input" id="motor" name="motor" value="<?= htmlspecialchars($motor) ?>" onkeyup="hitungTotalAsetTetap()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="rumah" class="form-label">Rumah</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah asset-tetap-input" id="rumah" name="rumah" value="<?= htmlspecialchars($rumah) ?>" onkeyup="hitungTotalAsetTetap()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="tanah" class="form-label">Tanah</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah asset-tetap-input" id="tanah" name="tanah" value="<?= htmlspecialchars($tanah) ?>" onkeyup="hitungTotalAsetTetap()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="gudang" class="form-label">Gudang</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah asset-tetap-input" id="gudang" name="gudang" value="<?= htmlspecialchars($gudang) ?>" onkeyup="hitungTotalAsetTetap()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="kantor" class="form-label">Kantor</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah asset-tetap-input" id="kantor" name="kantor" value="<?= htmlspecialchars($kantor) ?>" onkeyup="hitungTotalAsetTetap()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="total_aset_tetap_display" class="form-label">Total Aset Tetap</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control" id="total_aset_tetap_display" value="0" readonly>
                                <input type="hidden" id="total_aset_tetap_hidden" name="total_aset_tetap">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="total_aset_display" class="form-label">Total Keseluruhan Aset</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control" id="total_aset_display" value="0" readonly>
                                <input type="hidden" id="total_aset_hidden" name="total_aset">
                            </div>
                        </div>
                    </div>

                    <div class="form-section" id="liabilitySection">
                        <h5>Analisa Kewajiban</h5>
                        <h6>Kewajiban Lancar</h6>
                        <div class="mb-3">
                            <label for="dp_diterima" class="form-label">DP Diterima</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah kewajiban-lancar-input" id="dp_diterima" name="dp_diterima" value="<?= htmlspecialchars($dp_diterima) ?>" onkeyup="hitungTotalKewajibanLancar()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="biaya_harus_bayar" class="form-label">Biaya Harus Dibayar</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah kewajiban-lancar-input" id="biaya_harus_bayar" name="biaya_harus_bayar" value="<?= htmlspecialchars($biaya_harus_bayar) ?>" onkeyup="hitungTotalKewajibanLancar()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="dll_kewajiban_lancar" class="form-label">Dll Kewajiban Lancar</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah kewajiban-lancar-input" id="dll_kewajiban_lancar" name="dll_kewajiban_lancar" value="<?= htmlspecialchars($dll_kewajiban_lancar) ?>" onkeyup="hitungTotalKewajibanLancar()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="total_kewajiban_lancar_display" class="form-label">Total Kewajiban Lancar</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control" id="total_kewajiban_lancar_display" value="0" readonly>
                                <input type="hidden" id="total_kewajiban_lancar_hidden" name="total_kewajiban_lancar">
                            </div>
                        </div>

                        <h6>Kewajiban Tetap</h6>
                        <div class="mb-3">
                            <label for="modal_pinjaman" class="form-label">Modal Pinjaman</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah kewajiban-tetap-input" id="modal_pinjaman" name="modal_pinjaman" value="<?= htmlspecialchars($modal_pinjaman) ?>" onkeyup="hitungTotalKewajibanTetap()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="modal_bank" class="form-label">Modal Bank</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control rupiah kewajiban-tetap-input" id="modal_bank" name="modal_bank" value="<?= htmlspecialchars($modal_bank) ?>" onkeyup="hitungTotalKewajibanTetap()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="total_kewajiban_tetap_display" class="form-label">Total Kewajiban Tetap</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control" id="total_kewajiban_tetap_display" value="0" readonly>
                                <input type="hidden" id="total_kewajiban_tetap_hidden" name="total_kewajiban_tetap">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="total_keseluruhan_kewajiban_display" class="form-label">Total Keseluruhan Kewajiban</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control" id="total_keseluruhan_kewajiban_display" value="0" readonly>
                                <input type="hidden" id="total_keseluruhan_kewajiban_hidden" name="total_keseluruhan_kewajiban">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h5>Dokumen Terkait</h5>
                        <div class="mb-3">
                            <label for="dokumen" class="form-label">Unggah Dokumen (Opsional, max 5MB, PDF/DOC/DOCX/JPG/JPEG/PNG)</label>
                            <input class="form-control" type="file" id="dokumen" name="dokumen" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <?php if (!empty($dokumen_filename)): ?>
                                <small class="form-text text-muted">File saat ini: <a href="../uploads/<?= htmlspecialchars($dokumen_filename) ?>" target="_blank"><?= htmlspecialchars($dokumen_filename) ?></a></small><br>
                                <small class="form-text text-danger">Unggah file baru akan menggantikan file ini.</small>
                            <?php else: ?>
                                <small class="form-text text-muted">Belum ada dokumen diunggah.</small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="admin_panel.php" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fungsi untuk memformat input menjadi format Rupiah
        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
        }

        // Fungsi untuk membersihkan format Rupiah menjadi angka float
        function cleanRupiah(rupiah) {
            // Hilangkan "Rp. ", titik sebagai pemisah ribuan, dan ganti koma dengan titik untuk desimal
            return parseFloat(rupiah.replace(/[^0-9,]/g, '').replace(',', '.'));
        }

        const rupiahInputs = document.querySelectorAll('.rupiah');
        rupiahInputs.forEach(input => {
            // Pastikan input memiliki nilai awal yang diformat jika ada
            if (input.value !== '') {
                input.value = formatRupiah(input.value, 'Rp. ');
            } else {
                input.value = formatRupiah('0', 'Rp. ');
            }

            input.addEventListener('keyup', function(e) {
                // Biarkan jika tombol yang ditekan adalah panah kiri/kanan, home, end, delete, backspace
                if (['ArrowLeft', 'ArrowRight', 'Home', 'End', 'Delete', 'Backspace'].includes(e.key)) {
                    return;
                }
                // Dapatkan posisi kursor sebelum perubahan
                let cursorPosition = this.selectionStart;
                const originalValue = this.value;

                let cleaned = cleanRupiah(originalValue).toString();
                // Jika input tidak valid (misal hanya Rp. atau hanya titik/koma)
                if (isNaN(cleaned) || cleaned === '') {
                    this.value = 'Rp. ';
                    return;
                }

                let formatted = formatRupiah(cleaned, 'Rp. ');
                this.value = formatted;

                // Hitung perbedaan panjang dan sesuaikan posisi kursor
                let newCursorPosition = cursorPosition + (formatted.length - originalValue.length);
                // Batasi posisi kursor agar tidak melebihi panjang string
                this.setSelectionRange(newCursorPosition, newCursorPosition);
            });

            input.addEventListener('blur', function() {
                // Pada saat kehilangan fokus, jika input kosong atau hanya "Rp. ", set ke "Rp. 0"
                if (this.value.trim() === '' || this.value.trim() === 'Rp.') {
                    this.value = formatRupiah('0', 'Rp. ');
                }
            });
        });


        // Perhitungan Total Pendapatan Pegawai
        function hitungTotalPendapatanPegawai() {
            const gaji = cleanRupiah(document.getElementById('gaji').value || '0');
            const tunjangan = cleanRupiah(document.getElementById('total_tunjangan').value || '0');
            const total = gaji + tunjangan;
            document.getElementById('total_gaji_tunjangan_pegawai_display').value = formatRupiah(total.toString(), 'Rp. ');
            document.getElementById('total_gaji_tunjangan_pegawai_hidden').value = total;
            hitungTotalPendapatanKeseluruhan();
        }

        // Perhitungan Omset
        function hitungOmset() {
            let totalOmset = 0;
            document.querySelectorAll('.omset-input').forEach(input => {
                totalOmset += cleanRupiah(input.value || '0');
            });
            document.getElementById('omset_total_display').value = formatRupiah(totalOmset.toString(), 'Rp. ');
            document.getElementById('omset_total_hidden').value = totalOmset;
            hitungTotalPendapatanKeseluruhan();
        }

        // Perhitungan HPP
        function hitungHPP() {
            let totalHPP = 0;
            document.querySelectorAll('.hpp-input').forEach(input => {
                totalHPP += cleanRupiah(input.value || '0');
            });
            document.getElementById('hpp_total_display').value = formatRupiah(totalHPP.toString(), 'Rp. ');
            document.getElementById('hpp_total_hidden').value = totalHPP;
            hitungLabaUsaha();
        }

        // Perhitungan Operasional
        function hitungOperasional() {
            let totalOperasional = 0;
            document.querySelectorAll('.operasional-input').forEach(input => {
                totalOperasional += cleanRupiah(input.value || '0');
            });
            document.getElementById('operasional_total_display').value = formatRupiah(totalOperasional.toString(), 'Rp. ');
            document.getElementById('operasional_total_hidden').value = totalOperasional;
            hitungLabaUsaha();
        }

        // Perhitungan Laba Usaha
        function hitungLabaUsaha() {
            const omset = cleanRupiah(document.getElementById('omset_total_hidden').value || '0');
            const hpp = cleanRupiah(document.getElementById('hpp_total_hidden').value || '0');
            const operasional = cleanRupiah(document.getElementById('operasional_total_hidden').value || '0');
            const laba = omset - hpp - operasional;
            // Tidak ada display untuk laba usaha di sini, hanya untuk perhitungan internal
            hitungTotalPendapatanKeseluruhan();
        }


        // Perhitungan Total Pengeluaran Rumah Tangga
        function hitungTotalPengeluaranRT() {
            let totalRT = 0;
            document.querySelectorAll('.pengeluaran-rt-input').forEach(input => {
                totalRT += cleanRupiah(input.value || '0');
            });
            document.getElementById('pengeluaran_rumah_tangga_total_display').value = formatRupiah(totalRT.toString(), 'Rp. ');
            document.getElementById('pengeluaran_rumah_tangga_total_hidden').value = totalRT;
            hitungTotalPengeluaran();
        }

        // Perhitungan Total Angsuran/Arisan/Iuran
        function hitungTotalAngsuran() {
            let totalAngsuran = 0;
            document.querySelectorAll('.angsuran-input').forEach(input => {
                totalAngsuran += cleanRupiah(input.value || '0');
            });
            document.getElementById('pengeluaran_angsuran_arisan_iuran_total_display').value = formatRupiah(totalAngsuran.toString(), 'Rp. ');
            document.getElementById('pengeluaran_angsuran_arisan_iuran_total_hidden').value = totalAngsuran;
            hitungTotalPengeluaran();
        }

        // Perhitungan Total Seluruh Pengeluaran
        function hitungTotalPengeluaran() {
            const totalRT = cleanRupiah(document.getElementById('pengeluaran_rumah_tangga_total_hidden').value || '0');
            const totalAngsuranIuran = cleanRupiah(document.getElementById('pengeluaran_angsuran_arisan_iuran_total_hidden').value || '0');
            const pengeluaranKSPPS = cleanRupiah(document.getElementById('pengeluaran_kspps').value || '0');
            const total = totalRT + totalAngsuranIuran + pengeluaranKSPPS;
            document.getElementById('total_pengeluaran_display').value = formatRupiah(total.toString(), 'Rp. ');
            document.getElementById('total_pengeluaran_hidden').value = total;
        }

        // Perhitungan Total Aset Lancar
        function hitungTotalAsetLancar() {
            let total = 0;
            document.querySelectorAll('.asset-lancar-input').forEach(input => {
                total += cleanRupiah(input.value || '0');
            });
            document.getElementById('total_aset_lancar_display').value = formatRupiah(total.toString(), 'Rp. ');
            document.getElementById('total_aset_lancar_hidden').value = total;
            hitungTotalAset();
        }

        // Perhitungan Total Aset Tetap
        function hitungTotalAsetTetap() {
            let total = 0;
            document.querySelectorAll('.asset-tetap-input').forEach(input => {
                total += cleanRupiah(input.value || '0');
            });
            document.getElementById('total_aset_tetap_display').value = formatRupiah(total.toString(), 'Rp. ');
            document.getElementById('total_aset_tetap_hidden').value = total;
            hitungTotalAset();
        }

        // Perhitungan Total Keseluruhan Aset
        function hitungTotalAset() {
            const totalLancar = cleanRupiah(document.getElementById('total_aset_lancar_hidden').value || '0');
            const totalTetap = cleanRupiah(document.getElementById('total_aset_tetap_hidden').value || '0');
            const total = totalLancar + totalTetap;
            document.getElementById('total_aset_display').value = formatRupiah(total.toString(), 'Rp. ');
            document.getElementById('total_aset_hidden').value = total;
        }

        // Perhitungan Total Kewajiban Lancar
        function hitungTotalKewajibanLancar() {
            let total = 0;
            document.querySelectorAll('.kewajiban-lancar-input').forEach(input => {
                total += cleanRupiah(input.value || '0');
            });
            document.getElementById('total_kewajiban_lancar_display').value = formatRupiah(total.toString(), 'Rp. ');
            document.getElementById('total_kewajiban_lancar_hidden').value = total;
            hitungTotalKeseluruhanKewajiban();
        }

        // Perhitungan Total Kewajiban Tetap
        function hitungTotalKewajibanTetap() {
            let total = 0;
            document.querySelectorAll('.kewajiban-tetap-input').forEach(input => {
                total += cleanRupiah(input.value || '0');
            });
            document.getElementById('total_kewajiban_tetap_display').value = formatRupiah(total.toString(), 'Rp. ');
            document.getElementById('total_kewajiban_tetap_hidden').value = total;
            hitungTotalKeseluruhanKewajiban();
        }

        // Perhitungan Total Keseluruhan Kewajiban
        function hitungTotalKeseluruhanKewajiban() {
            const totalLancar = cleanRupiah(document.getElementById('total_kewajiban_lancar_hidden').value || '0');
            const totalTetap = cleanRupiah(document.getElementById('total_kewajiban_tetap_hidden').value || '0');
            const total = totalLancar + totalTetap;
            document.getElementById('total_keseluruhan_kewajiban_display').value = formatRupiah(total.toString(), 'Rp. ');
            document.getElementById('total_keseluruhan_kewajiban_hidden').value = total;
        }

        // Fungsi untuk mengontrol visibilitas section pendapatan
        function toggleJenisSections() {
            const jenisUsaha = document.getElementById('jenis_usaha').value;
            const pegawaiSection = document.getElementById('pegawaiSection');
            const usahaSection = document.getElementById('usahaSection');

            pegawaiSection.style.display = 'none';
            usahaSection.style.display = 'none';

            if (jenisUsaha === 'pegawai') {
                pegawaiSection.style.display = 'block';
            } else if (jenisUsaha === 'usaha') {
                usahaSection.style.display = 'block';
            } else if (jenisUsaha === 'usaha_dan_pegawai') {
                pegawaiSection.style.display = 'block';
                usahaSection.style.display = 'block';
            }
            // Panggil kembali semua hitungan setelah section ditampilkan/disembunyikan
            hitungTotalPendapatanPegawai();
            hitungOmset();
            hitungHPP();
            hitungOperasional();
            hitungTotalPengeluaranRT();
            hitungTotalAngsuran();
            hitungTotalPengeluaran();
            hitungTotalAsetLancar();
            hitungTotalAsetTetap();
            hitungTotalKewajibanLancar();
            hitungTotalKewajibanTetap();
            hitungTotalKeseluruhanKewajiban();
        }

        // Fungsi untuk mengontrol visibilitas section analisa (aset & kewajiban)
        function toggleAnalysisSections() {
            const skipAnalysisCheckbox = document.getElementById('skip_analysis');
            const assetSection = document.getElementById('assetSection');
            const liabilitySection = document.getElementById('liabilitySection');

            if (skipAnalysisCheckbox.checked) {
                assetSection.style.display = 'none';
                liabilitySection.style.display = 'none';
            } else {
                assetSection.style.display = 'block';
                liabilitySection.style.display = 'block';
            }
        }

        // Panggil fungsi toggleJenisSections() dan toggleAnalysisSections() saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Format awal semua input rupiah
            rupiahInputs.forEach(input => {
                // Untuk edit, nilai mungkin sudah ada dari PHP, jadi kita bersihkan dulu
                // baru format kembali
                if (input.value !== '') {
                    input.value = formatRupiah(cleanRupiah(input.value).toString(), 'Rp. ');
                } else {
                    input.value = formatRupiah('0', 'Rp. '); // Pastikan input kosong menjadi 'Rp. 0'
                }
            });

            // Panggil toggleJenisSections() setelah semua input rupiah diformat
            toggleJenisSections(); // Ini akan mengatur tampilan awal section dan memicu perhitungan

            // Tambahkan event listener untuk jenis_usaha agar section pendapatan sesuai
            document.getElementById('jenis_usaha').addEventListener('change', toggleJenisSections);

            // Panggil toggleAnalysisSections() setelah semua input rupiah diformat
            toggleAnalysisSections();

            // Tambahkan event listener untuk checkbox "skip_analysis"
            const skipAnalysisCheckbox = document.getElementById('skip_analysis');
            if (skipAnalysisCheckbox) { // Pastikan elemen ada
                skipAnalysisCheckbox.addEventListener('change', toggleAnalysisSections);
            }
        });

        // Hitungan total pendapatan keseluruhan (diletakkan di akhir karena bergantung pada fungsi lain)
        function hitungTotalPendapatanKeseluruhan() {
            const jenisUsaha = document.getElementById('jenis_usaha').value;
            let totalPendapatanKeseluruhan = 0;

            if (jenisUsaha === 'pegawai' || jenisUsaha === 'usaha_dan_pegawai') {
                totalPendapatanKeseluruhan += cleanRupiah(document.getElementById('total_gaji_tunjangan_pegawai_hidden').value || '0');
            }
            if (jenisUsaha === 'usaha' || jenisUsaha === 'usaha_dan_pegawai') {
                // Laba Usaha
                const omset = cleanRupiah(document.getElementById('omset_total_hidden').value || '0');
                const hpp = cleanRupiah(document.getElementById('hpp_total_hidden').value || '0');
                const operasional = cleanRupiah(document.getElementById('operasional_total_hidden').value || '0');
                const laba_usaha = omset - hpp - operasional;
                totalPendapatanKeseluruhan += laba_usaha;
            }
            // Ini tidak ditampilkan langsung di form edit, tapi penting untuk perhitungan di simpan.php dan detail.php
            // Anda bisa menambahkan input hidden jika ingin menyimpan nilai ini di form
        }

        // Panggil semua fungsi perhitungan saat DOMContentLoaded untuk memastikan nilai-nilai terhitung
        // meskipun tidak ada perubahan input. Ini penting untuk mode edit.
        document.addEventListener('DOMContentLoaded', function() {
            // Pastikan format rupiah sudah diterapkan sebelum perhitungan
            rupiahInputs.forEach(input => {
                if (input.value !== '') {
                    input.value = formatRupiah(cleanRupiah(input.value).toString(), 'Rp. ');
                } else {
                    input.value = formatRupiah('0', 'Rp. ');
                }
            });

            // Panggil fungsi toggle untuk mengatur tampilan awal section
            toggleJenisSections();
            toggleAnalysisSections();

            // Panggil semua fungsi perhitungan agar nilai terupdate saat halaman dimuat
            hitungTotalPendapatanPegawai();
            hitungOmset();
            hitungHPP();
            hitungOperasional();
            hitungTotalPengeluaranRT();
            hitungTotalAngsuran();
            hitungTotalPengeluaran();
            hitungTotalAsetLancar();
            hitungTotalAsetTetap();
            hitungTotalKewajibanLancar();
            hitungTotalKewajibanTetap();
            hitungTotalKeseluruhanKewajiban();
            hitungTotalPendapatanKeseluruhan(); // Pastikan ini terpanggil juga
        });
    </script>
</body>
</html>