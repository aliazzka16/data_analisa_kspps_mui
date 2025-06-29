<?php
// >>>>>>>>>> PENTING: INI HARUS PALING ATAS DI FILE ANDA <<<<<<<<<<
// Pastikan tidak ada spasi, karakter, atau baris kosong sebelum <?php
// Aktifkan output buffering untuk mencegah output yang tidak diinginkan merusak file Excel
ob_start();

require '../vendor/autoload.php'; // Pastikan path ini benar
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Matikan sementara error reporting untuk debugging.
// Nanti setelah selesai debugging, aktifkan kembali (hapus // dari baris di bawah):
// error_reporting(0);
// ini_set('display_errors', 0);
// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

$id = $_GET['id'] ?? -1;
$file_path = '../data/data.txt'; // Path ke data.txt relatif terhadap export_excel.php

$data_lines = [];
if (file_exists($file_path)) {
    $data_lines = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}

$row = [];
if (isset($data_lines[$id])) {
    $row = explode('|', $data_lines[$id]);
} else {
    // Redirect if ID is invalid or not found
    header("Location: admin_panel.php?error=id_invalid");
    exit;
}

// Fungsi pembantu untuk membersihkan dan mengubah ke float
function cleanAndFloat($value) {
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
$jenis_usaha = $row[5]; // Ini akan menjadi 'pegawai', 'usaha', atau 'usaha_dan_pegawai'
$jenis_pembiayaan = $row[6]; // Reguler/Musiman

// Data Pendapatan Pegawai (Indeks 7-9)
$gaji = cleanAndFloat($row[7]);
$total_tunjangan = cleanAndFloat($row[8]);
$biaya_pokok = cleanAndFloat($row[9]); // Ini tidak digunakan di detail.php untuk perhitungan pendapatan pegawai

// Data Pendapatan Usaha - Uraian dan Omset (Indeks 10-32)
$jenis_usaha_uraian = $row[10];
$omset_nominal = [];
for ($i = 0; $i < 7; $i++) {
    $omset_nominal[] = cleanAndFloat($row[13 + ($i * 3)]);
}
$omset_total = cleanAndFloat($row[32]);

// Data Pendapatan Usaha - HPP (Indeks 33-54)
$hpp_nominal = [];
for ($i = 0; $i < 7; $i++) {
    $hpp_nominal[] = cleanAndFloat($row[35 + ($i * 3)]);
}
$hpp_total = cleanAndFloat($row[54]);

// Data Pendapatan Usaha - Biaya Operasional (Indeks 55-76)
$operasional_nominal = [];
for ($i = 0; $i < 7; $i++) {
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

// Kewajiban Tetap (Indeks 105-107) - Note: index 105 is used for skip_analysis in simpan.php, check this carefully
$skip_analysis_raw = $row[105]; // This is actually skip_analysis field
$modal_pinjaman = cleanAndFloat($row[106]);
$modal_bank = cleanAndFloat($row[107]);
// $total_kewajiban_tetap is not directly stored but calculated in detail.php.
// Need to adjust subsequent indices due to skip_analysis being 105.
// Adjusting based on simpan.php:
// Index 105: skip_analysis (yes/no)
// Index 106: modal_pinjaman
// Index 107: modal_bank
// Index 108: total_keseluruhan_kewajiban
// Index 109: pengeluaran_kspps
// Index 110: dokumen_filename
// Index 111: timestamp

$total_keseluruhan_kewajiban = cleanAndFloat($row[108]);
$pengeluaran_kspps = cleanAndFloat($row[109]);
$dokumen_filename = $row[110];
$timestamp = $row[111];

// Determine if analysis should be skipped
$skip_analysis = ($skip_analysis_raw === 'yes');

// --- Perhitungan Analisa Keuangan (Synchronized with detail.php) ---

// Total Pendapatan Pegawai
$total_gaji_tunjangan_pegawai = $gaji + $total_tunjangan;

// Laba Usaha
$laba_usaha = $omset_total - $hpp_total - $operasional_total;

// Total Pengeluaran Rumah Tangga
$pengeluaran_rumah_tangga_total = $makan_minum + $anak + $pendidikan + $listrik + $air + $transport + $pengeluaran_lain_rt;

// Total Pengeluaran Angsuran/Arisan/Iuran
$pengeluaran_angsuran_arisan_iuran_total = $angsuran1 + $bpjs + $arisan + $iuran + $angsuran_lain;

// Total Seluruh Pengeluaran
$total_pengeluaran = $pengeluaran_rumah_tangga_total + $pengeluaran_angsuran_arisan_iuran_total + $pengeluaran_kspps;

// Disposable Income (Pendapatan Bersih Tersedia)
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
$total_aset = $total_aset_lancar + $total_aset_tetap;

// Total Kewajiban Tetap (Calculated in detail.php, not stored directly)
$total_kewajiban_tetap = $modal_pinjaman + $modal_bank;

// Net Worth (Kekayaan Bersih)
$net_worth = $total_aset - $total_keseluruhan_kewajiban;

// Modal
$modal = ($nominal_pengajuan > 0) ? ($nominal_pengajuan * 0.20) : 0; // Asumsi 20% dari nominal pengajuan

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
$status_disposable_income = hitungDisposableIncomeStatus($disposable_income_final);


// Fungsi untuk menghitung Status RPC (sesuai kriteria Aman/Cukup Aman/Bermasalah)
function hitungRpcStatus(float $sisaDanaAktual, float $pengeluaranKSPPS): string
{
    if ($pengeluaranKSPPS <= 0) {
        return "Tidak Ada Angsuran KSPPS"; // Hindari pembagian dengan nol
    }
    $rpc_ratio = $sisaDanaAktual / $pengeluaranKSPPS;
    if ($rpc_ratio >= 1.25) {
        return "Aman";
    } elseif ($rpc_ratio >= 1) {
        return "Cukup Aman";
    } else {
        return "Bermasalah";
    }
}
$status_rpc = hitungRpcStatus($sisa_dana_aktual, $pengeluaran_kspps);


// Fungsi untuk menghitung Status Debt to Income Ratio (DIR)
function hitungDirStatus(float $totalAngsuran, float $totalPendapatan): string
{
    if ($totalPendapatan <= 0) {
        return "Tidak Ada Pendapatan"; // Hindari pembagian dengan nol
    }
    $dir_ratio = ($totalAngsuran / $totalPendapatan) * 100; // Dalam persen
    if ($dir_ratio <= 30) {
        return "Baik";
    } else {
        return "Kurang Baik";
    }
}
$total_angsuran_all = $pengeluaran_angsuran_arisan_iuran_total + $pengeluaran_kspps;
$status_dir = hitungDirStatus($total_angsuran_all, $total_pendapatan_keseluruhan);


// Fungsi untuk menghitung Status Profitability Ratio (Laba Bersih / Omset)
function hitungProfitabilityStatus(float $labaBersihUsaha, float $omsetUsaha): string
{
    if ($omsetUsaha <= 0) {
        return "Tidak Relevan"; // Atau handle sesuai kebutuhan jika omset nol
    }
    $profit_ratio = ($labaBersihUsaha / $omsetUsaha) * 100; // Dalam persen
    if ($profit_ratio >= 20) { // Contoh: Profit di atas 20% dianggap baik
        return "Baik";
    } elseif ($profit_ratio >= 10) {
        return "Cukup";
    } else {
        return "Rendah";
    }
}
$status_profitability = hitungProfitabilityStatus($laba_usaha, $omset_total);

// Fungsi untuk menghitung Status Leverage Ratio (Total Kewajiban / Total Aset)
function hitungLeverageStatus(float $totalKewajiban, float $totalAset): string
{
    if ($totalAset <= 0) {
        return "Tidak Ada Aset"; // Hindari pembagian dengan nol
    }
    $leverage_ratio = ($totalKewajiban / $totalAset) * 100; // Dalam persen
    if ($leverage_ratio <= 50) { // Contoh: Leverage di bawah 50% dianggap baik
        return "Baik";
    } else {
        return "Tinggi";
    }
}
$status_leverage = hitungLeverageStatus($total_keseluruhan_kewajiban, $total_aset);


// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Analisa Pembiayaan');

// Define styles
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4F81BD']],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
];

$subHeaderStyle = [
    'font' => ['bold' => true, 'color' => ['argb' => 'FF000000']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFDCE6F1']],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];

$dataLabelStyle = [
    'font' => ['bold' => true],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];

$dataValueStyle = [
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];

$currencyStyle = [
    'numberFormat' => ['formatCode' => '#,##0'],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];

$statusGoodStyle = [
    'font' => ['color' => ['argb' => 'FF00B050'], 'bold' => true], // Green
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];

$statusConsiderStyle = [
    'font' => ['color' => ['argb' => 'FFFFC000'], 'bold' => true], // Orange
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];

$statusBadStyle = [
    'font' => ['color' => ['argb' => 'FFFF0000'], 'bold' => true], // Red
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];

$statusRpcAmanStyle = [
    'font' => ['color' => ['argb' => 'FF00B050'], 'bold' => true], // Green
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];
$statusRpcCukupAmanStyle = [
    'font' => ['color' => ['argb' => 'FFFFC000'], 'bold' => true], // Orange
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];
$statusRpcBermasalahStyle = [
    'font' => ['color' => ['argb' => 'FFFF0000'], 'bold' => true], // Red
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];

$statusDirBaikStyle = [
    'font' => ['color' => ['argb' => 'FF00B050'], 'bold' => true], // Green
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];
$statusDirKurangBaikStyle = [
    'font' => ['color' => ['argb' => 'FFFF0000'], 'bold' => true], // Red
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];

$statusProfitGoodStyle = [
    'font' => ['color' => ['argb' => 'FF00B050'], 'bold' => true], // Green
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];
$statusProfitCukupStyle = [
    'font' => ['color' => ['argb' => 'FFFFC000'], 'bold' => true], // Orange
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];
$statusProfitRendahStyle = [
    'font' => ['color' => ['argb' => 'FFFF0000'], 'bold' => true], // Red
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];

$statusLeverageGoodStyle = [
    'font' => ['color' => ['argb' => 'FF00B050'], 'bold' => true], // Green
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];
$statusLeverageTinggiStyle = [
    'font' => ['color' => ['argb' => 'FFFF0000'], 'bold' => true], // Red
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];


$rowIndex = 1;

// Section: Data Umum
$sheet->setCellValue('A' . $rowIndex, 'Data Umum')->getStyle('A' . $rowIndex . ':B' . $rowIndex)->applyFromArray($headerStyle);
$rowIndex++;
$sheet->setCellValue('A' . $rowIndex, 'Cabang:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $cabang)->getStyle('B' . $rowIndex)->applyFromArray($dataValueStyle);
$rowIndex++;
$sheet->setCellValue('A' . $rowIndex, 'Marketing:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $marketing)->getStyle('B' . $rowIndex)->applyFromArray($dataValueStyle);
$rowIndex++;
$sheet->setCellValue('A' . $rowIndex, 'Nama Anggota:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $anggota)->getStyle('B' . $rowIndex)->applyFromArray($dataValueStyle);
$rowIndex++;
$sheet->setCellValue('A' . $rowIndex, 'Alamat:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $alamat)->getStyle('B' . $rowIndex)->applyFromArray($dataValueStyle);
$rowIndex++;
$sheet->setCellValue('A' . $rowIndex, 'Nominal Pengajuan:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $nominal_pengajuan)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
$rowIndex++;
$sheet->setCellValue('A' . $rowIndex, 'Jenis Usaha:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $jenis_usaha)->getStyle('B' . $rowIndex)->applyFromArray($dataValueStyle);
$rowIndex++;
$sheet->setCellValue('A' . $rowIndex, 'Jenis Pembiayaan:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $jenis_pembiayaan)->getStyle('B' . $rowIndex)->applyFromArray($dataValueStyle);
$rowIndex++;
$sheet->setCellValue('A' . $rowIndex, 'Dokumen File:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $dokumen_filename)->getStyle('B' . $rowIndex)->applyFromArray($dataValueStyle);
$rowIndex++;
$sheet->setCellValue('A' . $rowIndex, 'Waktu Simpan:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $timestamp)->getStyle('B' . $rowIndex)->applyFromArray($dataValueStyle);
$rowIndex+=2;

// Section: Data Pendapatan
$sheet->setCellValue('A' . $rowIndex, 'Data Pendapatan')->getStyle('A' . $rowIndex . ':B' . $rowIndex)->applyFromArray($headerStyle);
$rowIndex++;

if ($jenis_usaha == 'pegawai' || $jenis_usaha == 'usaha_dan_pegawai') {
    $sheet->setCellValue('A' . $rowIndex, 'Pendapatan Pegawai')->getStyle('A' . $rowIndex . ':B' . $rowIndex)->applyFromArray($subHeaderStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Gaji Pokok:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $gaji)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Tunjangan:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $total_tunjangan)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Total Gaji & Tunjangan:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $total_gaji_tunjangan_pegawai)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex++;
}

if ($jenis_usaha == 'usaha' || $jenis_usaha == 'usaha_dan_pegawai') {
    $sheet->setCellValue('A' . $rowIndex, 'Pendapatan Usaha')->getStyle('A' . $rowIndex . ':B' . $rowIndex)->applyFromArray($subHeaderStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Jenis Usaha Uraian:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $jenis_usaha_uraian)->getStyle('B' . $rowIndex)->applyFromArray($dataValueStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Total Omset:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $omset_total)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Total HPP:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $hpp_total)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Total Biaya Operasional:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $operasional_total)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Laba Usaha:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $laba_usaha)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex++;
}
$rowIndex+=2;


// Section: Data Pengeluaran
$sheet->setCellValue('A' . $rowIndex, 'Data Pengeluaran')->getStyle('A' . $rowIndex . ':B' . $rowIndex)->applyFromArray($headerStyle);
$rowIndex++;

$sheet->setCellValue('A' . $rowIndex, 'Pengeluaran Rumah Tangga')->getStyle('A' . $rowIndex . ':B' . $rowIndex)->applyFromArray($subHeaderStyle);
$rowIndex++;
$sheet->setCellValue('A' . $rowIndex, 'Makan & Minum:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $makan_minum)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
$rowIndex++;
$sheet->setCellValue('A' . $rowIndex, 'Anak:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $anak)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
$rowIndex++;
$sheet->setCellValue('A' . $rowIndex, 'Pendidikan:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $pendidikan)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
$rowIndex++;
$sheet->setCellValue('A' . $rowIndex, 'Listrik:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $listrik)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
$rowIndex++;
$sheet->setCellValue('A' . $rowIndex, 'Air:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $air)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
$rowIndex++;
$sheet->setCellValue('A' . $rowIndex, 'Transportasi:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $transport)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
$rowIndex++;
$sheet->setCellValue('A' . $rowIndex, 'Pengeluaran Lain RT:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $pengeluaran_lain_rt)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
$rowIndex++;
$sheet->setCellValue('A' . $rowIndex, 'Total Pengeluaran Rumah Tangga:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $pengeluaran_rumah_tangga_total)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
$rowIndex++;

$sheet->setCellValue('A' . $rowIndex, 'Pengeluaran Angsuran/Arisan/Iuran')->getStyle('A' . $rowIndex . ':B' . $rowIndex)->applyFromArray($subHeaderStyle);
$rowIndex++;
$sheet->setCellValue('A' . $rowIndex, 'Angsuran Bank/Lainnya:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $angsuran1)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
$rowIndex++;
$sheet->setCellValue('A' . $rowIndex, 'BPJS:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $bpjs)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
$rowIndex++;
$sheet->setCellValue('A' . $rowIndex, 'Arisan:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $arisan)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
$rowIndex++;
$sheet->setCellValue('A' . $rowIndex, 'Iuran:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $iuran)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
$rowIndex++;
$sheet->setCellValue('A' . $rowIndex, 'Angsuran Lain:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $angsuran_lain)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
$rowIndex++;
$sheet->setCellValue('A' . $rowIndex, 'Total Angsuran/Arisan/Iuran:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $pengeluaran_angsuran_arisan_iuran_total)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
$rowIndex++;

$sheet->setCellValue('A' . $rowIndex, 'Angsuran KSPPS MUI:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $pengeluaran_kspps)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
$rowIndex++;

$sheet->setCellValue('A' . $rowIndex, 'TOTAL SELURUH PENGELUARAN:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $total_pengeluaran)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
$rowIndex+=2;


// Section: Analisa Aset (Conditional based on skip_analysis)
if (!$skip_analysis) {
    $sheet->setCellValue('A' . $rowIndex, 'Analisa Aset')->getStyle('A' . $rowIndex . ':B' . $rowIndex)->applyFromArray($headerStyle);
    $rowIndex++;

    $sheet->setCellValue('A' . $rowIndex, 'Aset Lancar')->getStyle('A' . $rowIndex . ':B' . $rowIndex)->applyFromArray($subHeaderStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Kas Tunai:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $kas_tunai)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Kas Bank:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $kas_bank)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Piutang:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $piutang)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Persediaan:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $persediaan)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Emas:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $emas)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Surat Berharga:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $surat_berharga)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Total Aset Lancar:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $total_aset_lancar)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex++;

    $sheet->setCellValue('A' . $rowIndex, 'Aset Tetap')->getStyle('A' . $rowIndex . ':B' . $rowIndex)->applyFromArray($subHeaderStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Mobil:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $mobil)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Motor:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $motor)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Rumah:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $rumah)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Tanah:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $tanah)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Gudang:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $gudang)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Kantor:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $kantor)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Total Aset Tetap:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $total_aset_tetap)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex++;

    $sheet->setCellValue('A' . $rowIndex, 'TOTAL KESELURUHAN ASET:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $total_aset)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex+=2;
}


// Section: Analisa Kewajiban (Conditional based on skip_analysis)
if (!$skip_analysis) {
    $sheet->setCellValue('A' . $rowIndex, 'Analisa Kewajiban')->getStyle('A' . $rowIndex . ':B' . $rowIndex)->applyFromArray($headerStyle);
    $rowIndex++;

    $sheet->setCellValue('A' . $rowIndex, 'Kewajiban Lancar')->getStyle('A' . $rowIndex . ':B' . $rowIndex)->applyFromArray($subHeaderStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'DP Diterima:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $dp_diterima)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Biaya Harus Dibayar:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $biaya_harus_bayar)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Dll Kewajiban Lancar:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $dll_kewajiban_lancar)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Total Kewajiban Lancar:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $total_kewajiban_lancar)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex++;

    $sheet->setCellValue('A' . $rowIndex, 'Kewajiban Tetap')->getStyle('A' . $rowIndex . ':B' . $rowIndex)->applyFromArray($subHeaderStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Modal Pinjaman:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $modal_pinjaman)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Modal Bank:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $modal_bank)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Total Kewajiban Tetap:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $total_kewajiban_tetap)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex++;

    $sheet->setCellValue('A' . $rowIndex, 'TOTAL KESELURUHAN KEWAJIBAN:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, $total_keseluruhan_kewajiban)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
    $rowIndex+=2;
}

// Section: Rekomendasi Pembiayaan
$sheet->setCellValue('A' . $rowIndex, 'Rekomendasi Pembiayaan')->getStyle('A' . $rowIndex . ':B' . $rowIndex)->applyFromArray($headerStyle);
$rowIndex++;

$sheet->setCellValue('A' . $rowIndex, 'Disposable Income:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $disposable_income_final)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
$rowIndex++;
$sheet->setCellValue('A' . $rowIndex, 'Status Disposable Income:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$disStatusStyle = ($status_disposable_income === 'Diterima') ? $statusGoodStyle : (($status_disposable_income === 'Dipertimbangkan') ? $statusConsiderStyle : $statusBadStyle);
$sheet->setCellValue('B' . $rowIndex, $status_disposable_income)->getStyle('B' . $rowIndex)->applyFromArray($disStatusStyle);
$rowIndex++;

$sheet->setCellValue('A' . $rowIndex, 'Sisa Dana Aktual:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $sisa_dana_aktual)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
$rowIndex++;
$sheet->setCellValue('A' . $rowIndex, 'Angsuran KSPPS MUI:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $pengeluaran_kspps)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle);
$rowIndex++;
$sheet->setCellValue('A' . $rowIndex, 'Status RPC:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$rpcStatusStyle = ($status_rpc === 'Aman') ? $statusRpcAmanStyle : (($status_rpc === 'Cukup Aman') ? $statusRpcCukupAmanStyle : $statusRpcBermasalahStyle);
$sheet->setCellValue('B' . $rowIndex, $status_rpc)->getStyle('B' . $rowIndex)->applyFromArray($rpcStatusStyle);
$rowIndex++;

$sheet->setCellValue('A' . $rowIndex, 'Debt to Income Ratio (DIR):')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, round($total_angsuran_all / $total_pendapatan_keseluruhan * 100, 2) . '%')->getStyle('B' . $rowIndex)->applyFromArray($dataValueStyle); // Display as percentage
$rowIndex++;
$sheet->setCellValue('A' . $rowIndex, 'Status DIR:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$dirStatusStyle = ($status_dir === 'Baik') ? $statusDirBaikStyle : $statusDirKurangBaikStyle;
$sheet->setCellValue('B' . $rowIndex, $status_dir)->getStyle('B' . $rowIndex)->applyFromArray($dirStatusStyle);
$rowIndex++;

if ($jenis_usaha == 'usaha' || $jenis_usaha == 'usaha_dan_pegawai') {
    $sheet->setCellValue('A' . $rowIndex, 'Profitability Ratio (Laba Bersih / Omset):')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, round($laba_usaha / $omset_total * 100, 2) . '%')->getStyle('B' . $rowIndex)->applyFromArray($dataValueStyle); // Display as percentage
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Status Profitability:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $profitStatusStyle = ($status_profitability === 'Baik') ? $statusProfitGoodStyle : (($status_profitability === 'Cukup') ? $statusProfitCukupStyle : $statusProfitRendahStyle);
    $sheet->setCellValue('B' . $rowIndex, $status_profitability)->getStyle('B' . $rowIndex)->applyFromArray($profitStatusStyle);
    $rowIndex++;
}

if (!$skip_analysis) {
    $sheet->setCellValue('A' . $rowIndex, 'Leverage Ratio (Total Kewajiban / Total Aset):')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $sheet->setCellValue('B' . $rowIndex, round($total_keseluruhan_kewajiban / $total_aset * 100, 2) . '%')->getStyle('B' . $rowIndex)->applyFromArray($dataValueStyle); // Display as percentage
    $rowIndex++;
    $sheet->setCellValue('A' . $rowIndex, 'Status Leverage:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
    $leverageStatusStyle = ($status_leverage === 'Baik') ? $statusLeverageGoodStyle : $statusLeverageTinggiStyle;
    $sheet->setCellValue('B' . $rowIndex, $status_leverage)->getStyle('B' . $rowIndex)->applyFromArray($leverageStatusStyle);
    $rowIndex++;
}

$sheet->setCellValue('A' . $rowIndex, 'Net Worth (Kekayaan Bersih):')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $net_worth)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle); $rowIndex++;
$sheet->setCellValue('A' . $rowIndex, 'Modal:')->getStyle('A' . $rowIndex)->applyFromArray($dataLabelStyle);
$sheet->setCellValue('B' . $rowIndex, $modal)->getStyle('B' . $rowIndex)->applyFromArray($currencyStyle); $rowIndex++;


// Auto size columns
foreach (range('A', 'C') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// ------ Bagian Krusial untuk Download File Excel ------
// Bersihkan buffer output yang mungkin berisi output dari script sebelumnya
// Ini sangat penting untuk memastikan tidak ada output selain file Excel
ob_end_clean();

// Set headers untuk download
$filename = 'Detail_Analisa_' . str_replace(' ', '_', $anggota) . '_' . date('Ymd_His') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
// Jika Anda menggunakan IE 9 atau lebih lama
header('Cache-Control: max-age=1');

// Jika Anda menggunakan IE di SSL
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Tanggal di masa lalu
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // Selalu dimodifikasi sekarang
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;