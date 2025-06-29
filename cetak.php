<?php
// admin/cetak.php
$id = $_GET['id'] ?? -1;
$file_path = '../data/data.txt';

if (!file_exists($file_path)) {
    echo "<div class='alert alert-danger'>File data.txt tidak ditemukan.</div>";
    exit;
}

$data_lines = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$row = isset($data_lines[$id]) ? explode('|', $data_lines[$id]) : [];

// Fungsi pembantu untuk membersihkan dan mengubah ke float
function cleanAndFloat($value)
{
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

// ---- Mapping Data Berdasarkan Indeks ----
// Data Umum (Indeks 0-6)
$cabang = $row[0] ?? '';
$marketing = $row[1] ?? '';
$nama_anggota = $row[2] ?? '';
$alamat = $row[3] ?? '';
$nominal_pengajuan = cleanAndFloat($row[4] ?? 0);
$jenis_usaha = $row[5] ?? ''; // Bisa Usaha/Pegawai tergantung input
$jenis_pembiayaan = $row[6] ?? ''; // Jenis pembiayaan (Produktif/Konsumtif)

// Data Usaha (Indeks 7-9) - Hanya relevan jika $jenis_usaha == 'Usaha'
$nama_usaha = $row[7] ?? '';
$lama_usaha = $row[8] ?? '';
$jumlah_karyawan = cleanAndFloat($row[9] ?? 0);

// Data Pegawai (Indeks 10-13) - Hanya relevan jika $jenis_usaha == 'Pegawai'
$nama_instansi = $row[10] ?? '';
$posisi_pekerjaan = $row[11] ?? '';
$lama_bekerja = $row[12] ?? '';
$gaji_pokok_pegawai = cleanAndFloat($row[13] ?? 0);

// Data Penghasilan (Indeks 14-25)
$penghasilan_usaha = cleanAndFloat($row[14] ?? 0);
$penghasilan_pasangan = cleanAndFloat($row[15] ?? 0);
$penghasilan_tambahan_lainnya = cleanAndFloat($row[16] ?? 0);
$penghasilan_rumah_tangga_lainnya = cleanAndFloat($row[17] ?? 0);
$total_tunjangan = cleanAndFloat($row[18] ?? 0);
$gaji_bersih_pegawai = cleanAndFloat($row[19] ?? 0); // Diisi dari index.php jika jenis_usaha pegawai
$pendapatan_lainnya = cleanAndFloat($row[20] ?? 0);
$pendapatan_pasangan_usaha = cleanAndFloat($row[21] ?? 0);
$pendapatan_pasangan_pegawai = cleanAndFloat($row[22] ?? 0);
$pendapatan_usaha_lainnya = cleanAndFloat($row[23] ?? 0);
$pendapatan_komisi = cleanAndFloat($row[24] ?? 0);
$pendapatan_sewa = cleanAndFloat($row[25] ?? 0);

// Data Pengeluaran (Indeks 26-44)
$biaya_pendidikan = cleanAndFloat($row[26] ?? 0);
$biaya_listrik_air = cleanAndFloat($row[27] ?? 0);
$biaya_transportasi = cleanAndFloat($row[28] ?? 0);
$biaya_telepon_internet = cleanAndFloat($row[29] ?? 0);
$biaya_makan_minum = cleanAndFloat($row[30] ?? 0);
$biaya_pajak_pbb_tahunan = cleanAndFloat($row[31] ?? 0); // Tahunan
$biaya_pajak_motor_tahunan = cleanAndFloat($row[32] ?? 0); // Tahunan
$biaya_pajak_mobil_tahunan = cleanAndFloat($row[33] ?? 0); // Tahunan
$biaya_lain_lain = cleanAndFloat($row[34] ?? 0);
$angsuran_bank_lain = cleanAndFloat($row[35] ?? 0);
$angsuran_koperasi_lain = cleanAndFloat($row[36] ?? 0);
$angsuran_lainnya = cleanAndFloat($row[37] ?? 0);
$angsuran_motor = cleanAndFloat($row[38] ?? 0);
$angsuran_mobil = cleanAndFloat($row[39] ?? 0);
$angsuran_rumah = cleanAndFloat($row[40] ?? 0);
$cicilan_bank_lain = cleanAndFloat($row[41] ?? 0); // Duplikat dari angsuran_bank_lain (dihapus di form, tapi tetap di sini untuk kompatibilitas)
$cicilan_lainnya = cleanAndFloat($row[42] ?? 0); // Duplikat dari angsuran_lainnya (dihapus di form, tapi tetap di sini untuk kompatibilitas)
$biaya_per_bulan = cleanAndFloat($row[43] ?? 0); // Tambahan untuk pengeluaran usaha
$biaya_sewa_tempat = cleanAndFloat($row[44] ?? 0); // Tambahan untuk pengeluaran usaha

// Data Aset (Indeks 45-51)
$aset_tanah_bangunan = cleanAndFloat($row[45] ?? 0);
$aset_kendaraan = cleanAndFloat($row[46] ?? 0);
$aset_elektronik = cleanAndFloat($row[47] ?? 0);
$aset_lainnya = cleanAndFloat($row[48] ?? 0);
$aset_piutang = cleanAndFloat($row[49] ?? 0); // Tambahan aset usaha
$aset_persediaan = cleanAndFloat($row[50] ?? 0); // Tambahan aset usaha
$aset_kas_bank = cleanAndFloat($row[51] ?? 0); // Tambahan aset usaha

// Data Kewajiban (Indeks 52-57)
$kewajiban_hutang_bank = cleanAndFloat($row[52] ?? 0);
$kewajiban_hutang_perorangan = cleanAndFloat($row[53] ?? 0);
$kewajiban_hutang_lainnya = cleanAndFloat($row[54] ?? 0);
$kewajiban_hutang_dagang = cleanAndFloat($row[55] ?? 0); // Tambahan kewajiban usaha
$kewajiban_hutang_gaji = cleanAndFloat($row[56] ?? 0); // Tambahan kewajiban usaha
$kewajiban_hutang_lain_usaha = cleanAndFloat($row[57] ?? 0); // Tambahan kewajiban usaha

// Data Dokumen (Indeks 58-110)
$dokumen_ktp = $row[58] ?? '';
$dokumen_kk = $row[59] ?? '';
$dokumen_buku_nikah = $row[60] ?? '';
$dokumen_sertifikat_tanah = $row[61] ?? '';
$dokumen_pbb = $row[62] ?? '';
$dokumen_imb = $row[63] ?? '';
$dokumen_akte_pendirian = $row[64] ?? '';
$dokumen_siup = $row[65] ?? '';
$dokumen_tdp = $row[66] ?? '';
$dokumen_situ = $row[67] ?? '';
$dokumen_skk = $row[68] ?? '';
$dokumen_domisili = $row[69] ?? '';
$dokumen_npwp = $row[70] ?? '';
$dokumen_sku = $row[71] ?? '';
$dokumen_rincian_penjualan = $row[72] ?? '';
$dokumen_nota_pembelian = $row[73] ?? '';
$dokumen_foto_usaha = $row[74] ?? '';
$dokumen_slip_gaji = $row[75] ?? '';
$dokumen_sk_pengangkatan = $row[76] ?? '';
$dokumen_rekening_koran = $row[77] ?? '';
$dokumen_spt_tahunan = $row[78] ?? '';
$dokumen_surat_keterangan_lainnya = $row[79] ?? '';
$dokumen_pbb_terbaru = $row[80] ?? '';
$dokumen_bpkb_kendaraan = $row[81] ?? '';
$dokumen_shm_shgb = $row[82] ?? '';
$dokumen_lainnya = $row[83] ?? '';
$dokumen_pas_foto = $row[84] ?? '';
$dokumen_surat_permohonan = $row[85] ?? '';
$dokumen_surat_keterangan_penghasilan = $row[86] ?? '';
$dokumen_mutasi_rekening = $row[87] ?? '';
$dokumen_nota_penjualan = $row[88] ?? '';
$dokumen_neraca_laba_rugi = $row[89] ?? '';
$dokumen_daftar_piutang = $row[90] ?? '';
$dokumen_daftar_hutang = $row[91] ?? '';
$dokumen_surat_keterangan_domisili_usaha = $row[92] ?? '';
$dokumen_akta_notaris_legalisir = $row[93] ?? '';
$dokumen_bukti_kepemilikan_aset = $row[94] ?? '';
$dokumen_surat_kuasa = $row[95] ?? '';
$dokumen_surat_pernyataan_ahli_waris = $row[96] ?? '';
$dokumen_izin_prinsip = $row[97] ?? '';
$dokumen_analisis_kelayakan_usaha = $row[98] ?? '';
$dokumen_proposal_usaha = $row[99] ?? '';
$dokumen_surat_persetujuan_suami_istri = $row[100] ?? '';
$dokumen_kartu_keluarga_pasangan = $row[101] ?? '';
$dokumen_pajak_bumi_bangunan = $row[102] ?? '';
$dokumen_surat_izin_mengemudi = $row[103] ?? '';
$dokumen_bukti_pembayaran_listrik_air_telepon = $row[104] ?? '';
$dokumen_bukti_pembayaran_pajak_kendaraan = $row[105] ?? '';
$dokumen_laporan_keuangan = $row[106] ?? '';
$dokumen_surat_rekomendasi = $row[107] ?? '';
$dokumen_surat_keterangan_kerja = $row[108] ?? '';
$dokumen_surat_izin_usaha = $row[109] ?? '';
$dokumen_selfie_ktp = $row[110] ?? ''; // New field at index 110

// Waktu Simpan (Indeks 111)
$waktu_simpan = $row[111] ?? '';

// Perhitungan Pendapatan Bersih
$total_pendapatan_usaha = $penghasilan_usaha + $pendapatan_usaha_lainnya;
$total_pendapatan_pegawai = $gaji_bersih_pegawai + $total_tunjangan;
$total_pendapatan_gabungan = $total_pendapatan_usaha + $total_pendapatan_pegawai + $penghasilan_pasangan + $penghasilan_tambahan_lainnya + $penghasilan_rumah_tangga_lainnya + $pendapatan_lainnya + $pendapatan_pasangan_usaha + $pendapatan_pasangan_pegawai + $pendapatan_komisi + $pendapatan_sewa;

// Perhitungan Pengeluaran Rutin per bulan
// Biaya pajak tahunan dibagi 12 bulan
$pajak_pbb_per_bulan = $biaya_pajak_pbb_tahunan / 12;
$pajak_motor_per_bulan = $biaya_pajak_motor_tahunan / 12;
$pajak_mobil_per_bulan = $biaya_pajak_mobil_tahunan / 12;

$pengeluaran_rumah_tangga_total = $biaya_pendidikan + $biaya_listrik_air + $biaya_transportasi + $biaya_telepon_internet + $biaya_makan_minum + $biaya_lain_lain + $pajak_pbb_per_bulan + $pajak_motor_per_bulan + $pajak_mobil_per_bulan;

// Total angsuran dan cicilan di luar KSPPS
$pengeluaran_angsuran_arisan_iuran_total = $angsuran_bank_lain + $angsuran_koperasi_lain + $angsuran_lainnya + $angsuran_motor + $angsuran_mobil + $angsuran_rumah;

// Tambahan pengeluaran usaha
$total_pengeluaran_usaha = $biaya_per_bulan + $biaya_sewa_tempat;

// Total Pengeluaran bulanan (belum termasuk angsuran KSPPS)
$total_pengeluaran_bulanan_non_kspps = $pengeluaran_rumah_tangga_total + $pengeluaran_angsuran_arisan_iuran_total + $total_pengeluaran_usaha;

// Angsuran KSPPS (nominal pengajuan per bulan) - diasumsikan ini adalah angsuran yang akan diambil
$pengeluaran_kspps = $nominal_pengajuan;

// Total Pengeluaran Keseluruhan (termasuk angsuran KSPPS)
$total_pengeluaran = $total_pengeluaran_bulanan_non_kspps + $pengeluaran_kspps;

// Sisa Pendapatan (Disposable Income)
$disposable_income_actual = $total_pendapatan_gabungan - $total_pengeluaran_bulanan_non_kspps;
$disposable_income_final = $disposable_income_actual - $pengeluaran_kspps;

// Disposable Income Rasio (DIR)
$disposable_income_rasio = ($total_pendapatan_gabungan > 0) ? ($disposable_income_final / $total_pendapatan_gabungan) * 100 : 0;

// Fungsi untuk menghitung Status Disposable Income (sesuai kriteria Diterima/Dipertimbangkan/Ditolak)
function hitungDisposableIncomeStatus(float $disposableIncomeFinal): string
{
    if ($disposableIncomeFinal >= 3000000) {
        return "Diterima";
    } elseif ($disposableIncomeFinal >= 1000000) {
        return "Dipertimbangkan";
    } else {
        return "Ditolak";
    }
}

// Fungsi untuk menghitung Status RPC (RPC < 70% Diterima, 70-80% Dipertimbangkan, > 80% Ditolak)
function hitungRpcStatus(float $totalPendapatan, float $totalPengeluaran): string
{
    if ($totalPendapatan <= 0) {
        return "Ditolak (Pendapatan Nol)";
    }
    $rpc = ($totalPengeluaran / $totalPendapatan) * 100;
    if ($rpc < 70) {
        return "Diterima";
    } elseif ($rpc >= 70 && $rpc <= 80) {
        return "Dipertimbangkan";
    } else {
        return "Ditolak";
    }
}

// Menentukan status berdasarkan Disposable Income
$status_disposable_income = hitungDisposableIncomeStatus($disposable_income_final);

// Menentukan status berdasarkan RPC
$status_rpc = hitungRpcStatus($total_pendapatan_gabungan, $total_pengeluaran);


// Fungsi untuk format rupiah
function formatRupiah($angka)
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

$formatted_nominal_pengajuan = formatRupiah($nominal_pengajuan);
$formatted_total_pendapatan_usaha = formatRupiah($total_pendapatan_usaha);
$formatted_total_pendapatan_pegawai = formatRupiah($total_pendapatan_pegawai);
$formatted_total_pendapatan_gabungan = formatRupiah($total_pendapatan_gabungan);
$formatted_pengeluaran_rumah_tangga_total = formatRupiah($pengeluaran_rumah_tangga_total);
$formatted_pengeluaran_angsuran_arisan_iuran_total = formatRupiah($pengeluaran_angsuran_arisan_iuran_total);
$formatted_total_pengeluaran_usaha = formatRupiah($total_pengeluaran_usaha);
$formatted_total_pengeluaran_bulanan_non_kspps = formatRupiah($total_pengeluaran_bulanan_non_kspps);
$formatted_pengeluaran_kspps = formatRupiah($pengeluaran_kspps);
$formatted_total_pengeluaran = formatRupiah($total_pengeluaran);
$formatted_disposable_income_actual = formatRupiah($disposable_income_actual);
$formatted_disposable_income_final = formatRupiah($disposable_income_final);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Analisa Pembiayaan KSPPS MUI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 20px;
        }
        .container-print {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        h2, h3 {
            color: #0056b3;
            margin-bottom: 10px;
            text-align: center;
        }
        .section-title {
            background-color: #f2f2f2;
            padding: 5px 10px;
            margin-top: 20px;
            margin-bottom: 10px;
            border-left: 5px solid #0056b3;
            font-weight: bold;
        }
        .data-item {
            display: flex;
            margin-bottom: 5px;
        }
        .data-label {
            flex: 1;
            font-weight: bold;
            padding-right: 10px;
        }
        .data-value {
            flex: 2;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-success { color: green; }
        .text-danger { color: red; }
        .text-warning { color: orange; }

        .status-accepted {
            color: green;
            font-weight: bold;
        }
        .status-considered {
            color: orange;
            font-weight: bold;
        }
        .status-rejected {
            color: red;
            font-weight: bold;
        }
        .document-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }
        .document-item {
            border: 1px solid #eee;
            padding: 10px;
            text-align: center;
            font-size: 0.9em;
            word-wrap: break-word;
        }
        .document-item img {
            max-width: 100%;
            height: auto;
            margin-bottom: 5px;
        }
        .document-item .file-icon {
            font-size: 3em;
            color: #dc3545;
            margin-bottom: 5px;
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .container-print {
                box-shadow: none;
                margin: 0;
                max-width: none;
            }
            .btn {
                display: none;
            }
            /* Menghilangkan tautan di bagian dokumen saat dicetak */
            .document-item a {
                display: none;
            }
            .document-item {
                border: none; /* Hilangkan border untuk cetak jika tidak diinginkan */
                padding: 0;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container-print">
        <h2 class="mb-4">Analisa Pembiayaan KSPPS MUI</h2>
        <p style="text-align: center;">Tanggal Cetak: <?= date('d-m-Y H:i:s') ?></p>

        <?php if (!empty($row)): ?>
            <div class="section-title">Data Umum</div>
            <div class="data-item"><span class="data-label">Cabang:</span> <span class="data-value"><?= htmlspecialchars($cabang) ?></span></div>
            <div class="data-item"><span class="data-label">Marketing:</span> <span class="data-value"><?= htmlspecialchars($marketing) ?></span></div>
            <div class="data-item"><span class="data-label">Nama Anggota:</span> <span class="data-value"><?= htmlspecialchars($nama_anggota) ?></span></div>
            <div class="data-item"><span class="data-label">Alamat:</span> <span class="data-value"><?= htmlspecialchars($alamat) ?></span></div>
            <div class="data-item"><span class="data-label">Nominal Pengajuan:</span> <span class="data-value"><?= $formatted_nominal_pengajuan ?></span></div>
            <div class="data-item"><span class="data-label">Jenis Pembiayaan:</span> <span class="data-value"><?= htmlspecialchars($jenis_pembiayaan) ?> (reguler atau musiman)</span></div>
            <div class="data-item"><span class="data-label">Jenis Pekerjaan:</span> <span class="data-value"><?= htmlspecialchars($jenis_usaha) ?></span></div>
            <?php if ($jenis_usaha == 'Usaha'): ?>
                <div class="data-item"><span class="data-label">Nama Usaha:</span> <span class="data-value"><?= htmlspecialchars($nama_usaha) ?></span></div>
                <div class="data-item"><span class="data-label">Lama Usaha:</span> <span class="data-value"><?= htmlspecialchars($lama_usaha) ?></span></div>
                <div class="data-item"><span class="data-label">Jumlah Karyawan:</span> <span class="data-value"><?= htmlspecialchars($jumlah_karyawan) ?></span></div>
            <?php elseif ($jenis_usaha == 'Pegawai'): ?>
                <div class="data-item"><span class="data-label">Nama Instansi:</span> <span class="data-value"><?= htmlspecialchars($nama_instansi) ?></span></div>
                <div class="data-item"><span class="data-label">Posisi Pekerjaan:</span> <span class="data-value"><?= htmlspecialchars($posisi_pekerjaan) ?></span></div>
                <div class="data-item"><span class="data-label">Lama Bekerja:</span> <span class="data-value"><?= htmlspecialchars($lama_bekerja) ?></span></div>
                <div class="data-item"><span class="data-label">Gaji Pokok:</span> <span class="data-value"><?= formatRupiah($gaji_pokok_pegawai) ?></span></div>
            <?php endif; ?>
            <div class="data-item"><span class="data-label">Waktu Simpan:</span> <span class="data-value"><?= htmlspecialchars($waktu_simpan) ?></span></div>


            <div class="section-title">Analisa Keuangan</div>
            <table>
                <tbody>
                    <tr>
                        <td><strong>Penghasilan Usaha:</strong></td>
                        <td><?= formatRupiah($penghasilan_usaha) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Penghasilan Pasangan:</strong></td>
                        <td><?= formatRupiah($penghasilan_pasangan) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Penghasilan Tambahan Lainnya:</strong></td>
                        <td><?= formatRupiah($penghasilan_tambahan_lainnya) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Penghasilan Rumah Tangga Lainnya:</strong></td>
                        <td><?= formatRupiah($penghasilan_rumah_tangga_lainnya) ?></td>
                    </tr>
                        <tr>
                        <td><strong>Total Tunjangan Pegawai:</strong></td>
                        <td><?= formatRupiah($total_tunjangan) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Gaji Bersih Pegawai:</strong></td>
                        <td><?= formatRupiah($gaji_bersih_pegawai) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Pendapatan Lainnya:</strong></td>
                        <td><?= formatRupiah($pendapatan_lainnya) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Pendapatan Pasangan Usaha:</strong></td>
                        <td><?= formatRupiah($pendapatan_pasangan_usaha) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Pendapatan Pasangan Pegawai:</strong></td>
                        <td><?= formatRupiah($pendapatan_pasangan_pegawai) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Pendapatan Usaha Lainnya:</strong></td>
                        <td><?= formatRupiah($pendapatan_usaha_lainnya) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Pendapatan Komisi:</strong></td>
                        <td><?= formatRupiah($pendapatan_komisi) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Pendapatan Sewa:</strong></td>
                        <td><?= formatRupiah($pendapatan_sewa) ?></td>
                    </tr>
                    <tr style="background-color: #e0f2f7;">
                        <td><strong>TOTAL PENDAPATAN:</strong></td>
                        <td><strong><?= $formatted_total_pendapatan_gabungan ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong>Biaya Pendidikan:</strong></td>
                        <td><?= formatRupiah($biaya_pendidikan) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Biaya Listrik & Air:</strong></td>
                        <td><?= formatRupiah($biaya_listrik_air) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Biaya Transportasi:</strong></td>
                        <td><?= formatRupiah($biaya_transportasi) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Biaya Telepon & Internet:</strong></td>
                        <td><?= formatRupiah($biaya_telepon_internet) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Biaya Makan & Minum:</strong></td>
                        <td><?= formatRupiah($biaya_makan_minum) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Biaya Pajak PBB (Tahunan):</strong></td>
                        <td><?= formatRupiah($biaya_pajak_pbb_tahunan) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Biaya Pajak Motor (Tahunan):</strong></td>
                        <td><?= formatRupiah($biaya_pajak_motor_tahunan) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Biaya Pajak Mobil (Tahunan):</strong></td>
                        <td><?= formatRupiah($biaya_pajak_mobil_tahunan) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Biaya Lain-lain:</strong></td>
                        <td><?= formatRupiah($biaya_lain_lain) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Angsuran Bank Lain:</strong></td>
                        <td><?= formatRupiah($angsuran_bank_lain) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Angsuran Koperasi Lain:</strong></td>
                        <td><?= formatRupiah($angsuran_koperasi_lain) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Angsuran Lainnya:</strong></td>
                        <td><?= formatRupiah($angsuran_lainnya) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Angsuran Motor:</strong></td>
                        <td><?= formatRupiah($angsuran_motor) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Angsuran Mobil:</strong></td>
                        <td><?= formatRupiah($angsuran_mobil) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Angsuran Rumah:</strong></td>
                        <td><?= formatRupiah($angsuran_rumah) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Biaya Per Bulan (Usaha):</strong></td>
                        <td><?= formatRupiah($biaya_per_bulan) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Biaya Sewa Tempat (Usaha):</strong></td>
                        <td><?= formatRupiah($biaya_sewa_tempat) ?></td>
                    </tr>
                    <tr style="background-color: #f7e0e0;">
                        <td><strong>Total Pengeluaran Rutin (Non-KSPPS):</strong></td>
                        <td><strong><?= $formatted_total_pengeluaran_bulanan_non_kspps ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong>Angsuran KSPPS (Simulasi):</strong></td>
                        <td><?= $formatted_pengeluaran_kspps ?></td>
                    </tr>
                    <tr style="background-color: #ffe0e0;">
                        <td><strong>TOTAL PENGELUARAN KESELURUHAN:</strong></td>
                        <td><strong><?= $formatted_total_pengeluaran ?></strong></td>
                    </tr>
                    <tr style="background-color: #e6ffe6;">
                        <td><strong>Disposable Income (Sebelum Angsuran KSPPS):</strong></td>
                        <td><strong><?= $formatted_disposable_income_actual ?></strong></td>
                    </tr>
                    <tr style="background-color: #c7e6c7;">
                        <td><strong>Disposable Income (Setelah Angsuran KSPPS):</strong></td>
                        <td><strong><?= $formatted_disposable_income_final ?></strong></td>
                    </tr>
                    <tr style="background-color: #fff9e6;">
                        <td><strong>Status Disposable Income:</strong></td>
                        <td class="<?= strtolower(str_replace(' ', '-', $status_disposable_income)) ?>"><strong><?= $status_disposable_income ?></strong></td>
                    </tr>
                    <tr style="background-color: #fff9e6;">
                        <td><strong>Status RPC:</strong></td>
                        <td class="<?= strtolower(str_replace(' ', '-', $status_rpc)) ?>"><strong><?= $status_rpc ?></strong></td>
                    </tr>
                </tbody>
            </table>

            <div class="section-title">Informasi Aset</div>
            <div class="data-item"><span class="data-label">Tanah & Bangunan:</span> <span class="data-value"><?= formatRupiah($aset_tanah_bangunan) ?></span></div>
            <div class="data-item"><span class="data-label">Kendaraan:</span> <span class="data-value"><?= formatRupiah($aset_kendaraan) ?></span></div>
            <div class="data-item"><span class="data-label">Elektronik:</span> <span class="data-value"><?= formatRupiah($aset_elektronik) ?></span></div>
            <div class="data-item"><span class="data-label">Aset Lainnya:</span> <span class="data-value"><?= formatRupiah($aset_lainnya) ?></span></div>
            <div class="data-item"><span class="data-label">Piutang Usaha:</span> <span class="data-value"><?= formatRupiah($aset_piutang) ?></span></div>
            <div class="data-item"><span class="data-label">Persediaan Barang:</span> <span class="data-value"><?= formatRupiah($aset_persediaan) ?></span></div>
            <div class="data-item"><span class="data-label">Kas & Bank:</span> <span class="data-value"><?= formatRupiah($aset_kas_bank) ?></span></div>

            <div class="section-title">Informasi Kewajiban</div>
            <div class="data-item"><span class="data-label">Hutang Bank:</span> <span class="data-value"><?= formatRupiah($kewajiban_hutang_bank) ?></span></div>
            <div class="data-item"><span class="data-label">Hutang Perorangan:</span> <span class="data-value"><?= formatRupiah($kewajiban_hutang_perorangan) ?></span></div>
            <div class="data-item"><span class="data-label">Hutang Lainnya:</span> <span class="data-value"><?= formatRupiah($kewajiban_hutang_lainnya) ?></span></div>
            <div class="data-item"><span class="data-label">Hutang Dagang:</span> <span class="data-value"><?= formatRupiah($kewajiban_hutang_dagang) ?></span></div>
            <div class="data-item"><span class="data-label">Hutang Gaji Karyawan:</span> <span class="data-value"><?= formatRupiah($kewajiban_hutang_gaji) ?></span></div>
            <div class="data-item"><span class="data-label">Hutang Lainnya (Usaha):</span> <span class="data-value"><?= formatRupiah($kewajiban_hutang_lain_usaha) ?></span></div>

            <div class="section-title">Dokumen Terlampir</div>
            <div class="document-grid">
                <?php
                $document_fields = [
                    'KTP' => $dokumen_ktp,
                    'Kartu Keluarga' => $dokumen_kk,
                    'Buku Nikah' => $dokumen_buku_nikah,
                    'Sertifikat Tanah' => $dokumen_sertifikat_tanah,
                    'PBB' => $dokumen_pbb,
                    'IMB' => $dokumen_imb,
                    'Akte Pendirian Usaha' => $dokumen_akte_pendirian,
                    'SIUP' => $dokumen_siup,
                    'TDP' => $dokumen_tdp,
                    'SITU' => $dokumen_situ,
                    'SKK' => $dokumen_skk,
                    'Domisili' => $dokumen_domisili,
                    'NPWP' => $dokumen_npwp,
                    'SKU' => $dokumen_sku,
                    'Rincian Penjualan' => $dokumen_rincian_penjualan,
                    'Nota Pembelian' => $dokumen_nota_pembelian,
                    'Foto Usaha' => $dokumen_foto_usaha,
                    'Slip Gaji' => $dokumen_slip_gaji,
                    'SK Pengangkatan' => $dokumen_sk_pengangkatan,
                    'Rekening Koran' => $dokumen_rekening_koran,
                    'SPT Tahunan' => $dokumen_spt_tahunan,
                    'Surat Keterangan Lainnya' => $dokumen_surat_keterangan_lainnya,
                    'PBB Terbaru' => $dokumen_pbb_terbaru,
                    'BPKB Kendaraan' => $dokumen_bpkb_kendaraan,
                    'SHM/SHGB' => $dokumen_shm_shgb,
                    'Dokumen Lainnya' => $dokumen_lainnya,
                    'Pas Foto' => $dokumen_pas_foto,
                    'Surat Permohonan' => $dokumen_surat_permohonan,
                    'Surat Keterangan Penghasilan' => $dokumen_surat_keterangan_penghasilan,
                    'Mutasi Rekening' => $dokumen_mutasi_rekening,
                    'Nota Penjualan' => $dokumen_nota_penjualan,
                    'Neraca Laba Rugi' => $dokumen_neraca_laba_rugi,
                    'Daftar Piutang' => $dokumen_daftar_piutang,
                    'Daftar Hutang' => $dokumen_daftar_hutang,
                    'Surat Keterangan Domisili Usaha' => $dokumen_surat_keterangan_domisili_usaha,
                    'Akta Notaris Legalisir' => $dokumen_akta_notaris_legalisir,
                    'Bukti Kepemilikan Aset' => $dokumen_bukti_kepemilikan_aset,
                    'Surat Kuasa' => $dokumen_surat_kuasa,
                    'Surat Pernyataan Ahli Waris' => $dokumen_surat_pernyataan_ahli_waris,
                    'Izin Prinsip' => $dokumen_izin_prinsip,
                    'Analisis Kelayakan Usaha' => $dokumen_analisis_kelayakan_usaha,
                    'Proposal Usaha' => $dokumen_proposal_usaha,
                    'Surat Persetujuan Suami/Istri' => $dokumen_surat_persetujuan_suami_istri,
                    'Kartu Keluarga Pasangan' => $dokumen_kartu_keluarga_pasangan,
                    'Pajak Bumi Bangunan' => $dokumen_pajak_bumi_bangunan,
                    'Surat Izin Mengemudi' => $dokumen_surat_izin_mengemudi,
                    'Bukti Pembayaran Listrik/Air/Telepon' => $dokumen_bukti_pembayaran_listrik_air_telepon,
                    'Bukti Pembayaran Pajak Kendaraan' => $dokumen_bukti_pembayaran_pajak_kendaraan,
                    'Laporan Keuangan' => $dokumen_laporan_keuangan,
                    'Surat Rekomendasi' => $dokumen_surat_rekomendasi,
                    'Surat Keterangan Kerja' => $dokumen_surat_keterangan_kerja,
                    'Surat Izin Usaha' => $dokumen_surat_izin_usaha,
                    'Selfie KTP' => $dokumen_selfie_ktp,
                ];

                foreach ($document_fields as $label => $filename):
                    if (!empty($filename) && $filename !== '0'):
                        $file_url = '../uploads/' . htmlspecialchars($filename);
                ?>
                    <div class="document-item">
                        <p class="fw-bold mb-1"><?= $label ?></p>
                        <?php
                        $file_extension = pathinfo($filename, PATHINFO_EXTENSION);
                        if (in_array(strtolower($file_extension), ['jpg', 'jpeg', 'png', 'gif'])):
                        ?>
                            <img src="<?= $file_url ?>" alt="<?= $label ?>">
                            <small class="d-block text-muted"><?= htmlspecialchars($filename) ?></small>
                        <?php elseif (strtolower($file_extension) == 'pdf'): ?>
                            <div class="file-icon">&#x1F4C4;</div> <small class="d-block text-muted"><?= htmlspecialchars($filename) ?></small>
                        <?php else: ?>
                            <div class="file-icon">&#x1F4C4;</div> <small class="d-block text-muted">File: <?= htmlspecialchars($filename) ?></small>
                        <?php endif; ?>
                    </div>
                <?php
                    endif;
                endforeach;
                ?>
            </div>

        <?php else: ?>
            <div class="alert alert-danger">Data tidak ditemukan atau ID tidak valid.</div>
        <?php endif ?>
    </div>
</body>
</html>