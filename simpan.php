<?php
// simpan.php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [];

    // Fungsi pembantu untuk membersihkan dan mengubah ke float
    function cleanAndFloat($value) {
        // Hapus 'Rp. ', semua titik (.), dan ganti koma (,) dengan kosong, lalu konversi ke float
        $cleaned = str_replace(['Rp. ', '.', ','], '', $value);
        return floatval($cleaned);
    }
    
    // Fungsi pembantu untuk membersihkan string teks
    function cleanString($value) {
        return htmlspecialchars(strip_tags(trim($value)));
    }

    // --- STRUKTUR DATA LENGKAP (Total 112 field - Indeks 0 sampai 111) ---
    // Pastikan urutan dan jumlah field ini konsisten di semua file yang membaca data.txt

    // Data Umum (Indeks 0-6)
    $data[] = cleanString($_POST['cabang'] ?? ''); // 0
    $data[] = cleanString($_POST['marketing'] ?? ''); // 1
    $data[] = cleanString($_POST['anggota'] ?? ''); // 2
    $data[] = cleanString($_POST['alamat'] ?? ''); // 3
    $data[] = cleanAndFloat($_POST['nominal_pengajuan'] ?? 0); // 4
    $data[] = cleanString($_POST['jenis_usaha'] ?? ''); // 5
    $data[] = cleanString($_POST['jenis_pembiayaan'] ?? ''); // 6 <-- NEW FIELD: Jenis Pembiayaan (Reguler/Musiman)

    // Data Pendapatan Pegawai (Indeks 7-9)
    $data[] = cleanAndFloat($_POST['gaji'] ?? 0); // 7
    $data[] = cleanAndFloat($_POST['total_tunjangan'] ?? 0); // 8
    // Catatan: total_gaji_tunjangan tidak disimpan karena ini adalah field kalkulasi readonly
    $data[] = cleanAndFloat($_POST['biaya_pokok'] ?? 0); // 9

    // Data Pendapatan Usaha - Uraian dan Omset (Indeks 10-32)
    $data[] = cleanString($_POST['jenis_usaha_uraian'] ?? ''); // 10
    // Omset: 7 baris x (text1, text2, nominal)
    for ($i = 1; $i <= 7; $i++) {
        $data[] = cleanString($_POST["omset_text1_{$i}"] ?? ''); // 11, 14, 17, ...
        $data[] = cleanString($_POST["omset_text2_{$i}"] ?? ''); // 12, 15, 18, ...
        $data[] = cleanAndFloat($_POST["omset_nominal_{$i}"] ?? 0); // 13, 16, 19, ...
    }
    $data[] = cleanAndFloat($_POST['omset_total'] ?? 0); // 32 (diambil dari total yang dihitung di client-side)

    // Data Pendapatan Usaha - HPP (Indeks 33-54)
    // HPP: 7 baris x (text1, text2, nominal)
    for ($i = 1; $i <= 7; $i++) {
        $data[] = cleanString($_POST["hpp_text1_{$i}"] ?? ''); // 33, 36, 39, ...
        $data[] = cleanString($_POST["hpp_text2_{$i}"] ?? ''); // 34, 37, 40, ...
        $data[] = cleanAndFloat($_POST["hpp_nominal_{$i}"] ?? 0); // 35, 38, 41, ...
    }
    $data[] = cleanAndFloat($_POST['hpp_total'] ?? 0); // 54 (diambil dari total yang dihitung di client-side)

    // Data Pendapatan Usaha - Biaya Operasional (Indeks 55-76)
    // Operasional: 7 baris x (text1, text2, nominal)
    for ($i = 1; $i <= 7; $i++) {
        $data[] = cleanString($_POST["operasional_text1_{$i}"] ?? ''); // 55, 58, 61, ...
        $data[] = cleanString($_POST["operasional_text2_{$i}"] ?? ''); // 56, 59, 62, ...
        $data[] = cleanAndFloat($_POST["operasional_nominal_{$i}"] ?? 0); // 57, 60, 63, ...
    }
    $data[] = cleanAndFloat($_POST['operasional_total'] ?? 0); // 76 (diambil dari total yang dihitung di client-side)

    // Pengeluaran Rumah Tangga (Indeks 77-83)
    $data[] = cleanAndFloat($_POST['makan_minum'] ?? 0); // 77
    $data[] = cleanAndFloat($_POST['anak'] ?? 0); // 78
    $data[] = cleanAndFloat($_POST['pendidikan'] ?? 0); // 79
    $data[] = cleanAndFloat($_POST['listrik'] ?? 0); // 80
    $data[] = cleanAndFloat($_POST['air'] ?? 0); // 81
    $data[] = cleanAndFloat($_POST['transport'] ?? 0); // 82
    $data[] = cleanAndFloat($_POST['pengeluaran_lain_rt'] ?? 0); // 83

    // Pengeluaran Angsuran/Arisan/Iuran (Indeks 84-88)
    $data[] = cleanAndFloat($_POST['angsuran1'] ?? 0); // 84
    $data[] = cleanAndFloat($_POST['bpjs'] ?? 0); // 85
    $data[] = cleanAndFloat($_POST['arisan'] ?? 0); // 86
    $data[] = cleanAndFloat($_POST['iuran'] ?? 0); // 87
    $data[] = cleanAndFloat($_POST['angsuran_lain'] ?? 0); // 88

    // Aset Lancar (Indeks 89-94)
    $data[] = cleanAndFloat($_POST['kas_tunai'] ?? 0); // 89
    $data[] = cleanAndFloat($_POST['kas_bank'] ?? 0); // 90
    $data[] = cleanAndFloat($_POST['piutang'] ?? 0); // 91
    $data[] = cleanAndFloat($_POST['persediaan'] ?? 0); // 92
    $data[] = cleanAndFloat($_POST['emas'] ?? 0); // 93
    $data[] = cleanAndFloat($_POST['surat_berharga'] ?? 0); // 94

    // Aset Tetap (Indeks 95-100)
    $data[] = cleanAndFloat($_POST['mobil'] ?? 0); // 95
    $data[] = cleanAndFloat($_POST['motor'] ?? 0); // 96
    $data[] = cleanAndFloat($_POST['rumah'] ?? 0); // 97
    $data[] = cleanAndFloat($_POST['tanah'] ?? 0); // 98
    $data[] = cleanAndFloat($_POST['gudang'] ?? 0); // 99
    $data[] = cleanAndFloat($_POST['kantor'] ?? 0); // 100

    // Kewajiban Lancar (Indeks 101-104)
    $data[] = cleanAndFloat($_POST['dp_diterima'] ?? 0); // 101
    $data[] = cleanAndFloat($_POST['biaya_harus_bayar'] ?? 0); // 102
    $data[] = cleanAndFloat($_POST['dll_kewajiban_lancar'] ?? 0); // 103
    $data[] = cleanAndFloat($_POST['total_kewajiban_lancar'] ?? 0); // 104 (diambil dari total yang dihitung di client-side)

    // Kewajiban Tetap (Indeks 105-107)
    $data[] = cleanAndFloat($_POST['modal_pinjaman'] ?? 0); // 105
    $data[] = cleanAndFloat($_POST['modal_bank'] ?? 0); // 106
    $data[] = cleanAndFloat($_POST['total_kewajiban_tetap'] ?? 0); // 107 (diambil dari total yang dihitung di client-side)

    // Total Keseluruhan Kewajiban (Indeks 108)
    $data[] = cleanAndFloat($_POST['total_keseluruhan_kewajiban'] ?? 0); // 108 (diambil dari total yang dihitung di client-side)

    // Angsuran KSPPS MUI (Indeks 109)
    $data[] = cleanAndFloat($_POST['pengeluaran_kspps'] ?? 0); // 109

    // Dokumen (Indeks 110)
    $dokumen_filename = '';
    if (isset($_FILES['dokumen']) && $_FILES['dokumen']['error'] == UPLOAD_ERR_OK) {
        $file_info = $_FILES['dokumen'];
        $fileName = basename($file_info['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExt = ['pdf', 'jpg', 'jpeg', 'png'];
        $maxFileSize = 5 * 1024 * 1024; // 5 MB

        $uploadDir = '../uploads/'; // Pastikan direktori ini ada dan writable
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (in_array($fileExt, $allowedExt) && $file_info['size'] <= $maxFileSize) {
            $uniqueFileName = uniqid('doc_', true) . '.' . $fileExt;
            $uploadFilePath = $uploadDir . $uniqueFileName;
            if (move_uploaded_file($file_info['tmp_name'], $uploadFilePath)) {
                $dokumen_filename = $uniqueFileName;
            } else {
                error_log("Failed to move uploaded file: " . $file_info['tmp_name'] . " to " . $uploadFilePath);
            }
        } else {
            error_log("Invalid file type or size: " . $fileName . " (Size: " . $file_info['size'] . ", Ext: " . $fileExt . ")");
        }
    }
    $data[] = $dokumen_filename; // 110

    // Waktu Simpan (Indeks 111)
    $data[] = date('Y-m-d H:i:s'); // 111

    // Verifikasi total jumlah field
    if (count($data) !== 112) { // Total field seharusnya 112 (indeks 0 hingga 111)
        error_log("Data array count mismatch! Expected 112, got " . count($data));
        header("Location: index.php?status=error&message=data_mismatch");
        exit;
    }

    $file = 'data/data.txt'; // Path ke file data
    $current_data = '';
    if (file_exists($file)) {
        $current_data = file_get_contents($file);
    }
    
    // Tambahkan data baru ke baris baru
    $current_data .= implode('|', $data) . "\n";

    if (file_put_contents($file, $current_data)) {
        header("Location: index.php?status=success");
        exit;
    } else {
        error_log("Failed to write data to file: " . $file);
        header("Location: index.php?status=error&message=file_write_failed");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?>