<?php
// process_form.php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize all expected input fields
    $cabang = $_POST['cabang'] ?? '';
    $marketing = $_POST['marketing'] ?? '';
    $nama_anggota = $_POST['nama_anggota'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $jenis_pembiayaan = $_POST['jenis_pembiayaan'] ?? '';
    $nominal_pengajuan = $_POST['nominal_pengajuan'] ?? '';
    $jenis_pengajuan = $_POST['jenis_pengajuan'] ?? ''; // <--- FIELD BARU

    // Initialize fields for conditional sections
    $jenis_usaha = '';
    $lama_usaha = '';
    $jumlah_karyawan = '';
    $nama_instansi = '';
    $posisi_pekerjaan = '';
    $lama_bekerja = '';
    $gaji_pokok = '';

    if ($jenis_pembiayaan == 'Usaha') {
        $jenis_usaha = $_POST['jenis_usaha'] ?? '';
        $lama_usaha = $_POST['lama_usaha'] ?? '';
        $jumlah_karyawan = $_POST['jumlah_karyawan'] ?? '';
    } elseif ($jenis_pembiayaan == 'Pegawai') {
        $nama_instansi = $_POST['nama_instansi'] ?? '';
        $posisi_pekerjaan = $_POST['posisi_pekerjaan'] ?? '';
        $lama_bekerja = $_POST['lama_bekerja'] ?? '';
        $gaji_pokok = $_POST['gaji_pokok'] ?? '';
    }

    // Handle skip_analysis checkbox
    $skip_analysis = isset($_POST['skip_analysis']) ? 'Ya' : 'Tidak';

    // Initialize analysis fields (will be populated only if skip_analysis is 'Tidak')
    $tanggungan_keluarga = '';
    $biaya_pendidikan = '';
    $biaya_listrik_air = '';
    $biaya_telp_internet = '';
    $biaya_transportasi = '';
    $biaya_makan_minum = '';
    $biaya_lainnya = '';
    $pendapatan_lain = '';
    $cicilan_bank_lain = '';
    $cicilan_lainnya = '';
    $aset_tanah_bangunan = '';
    $aset_kendaraan = '';
    $aset_elektronik = '';
    $aset_lainnya = '';
    $kewajiban_hutang_bank = '';
    $kewajiban_hutang_perorangan = '';
    $kewajiban_hutang_lainnya = '';

    if ($skip_analysis === 'Tidak') {
        $tanggungan_keluarga = $_POST['tanggungan_keluarga'] ?? '';
        $biaya_pendidikan = $_POST['biaya_pendidikan'] ?? '';
        $biaya_listrik_air = $_POST['biaya_listrik_air'] ?? '';
        $biaya_telp_internet = $_POST['biaya_telp_internet'] ?? '';
        $biaya_transportasi = $_POST['biaya_transportasi'] ?? '';
        $biaya_makan_minum = $_POST['biaya_makan_minum'] ?? '';
        $biaya_lainnya = $_POST['biaya_lainnya'] ?? '';
        $pendapatan_lain = $_POST['pendapatan_lain'] ?? '';
        $cicilan_bank_lain = $_POST['cicilan_bank_lain'] ?? '';
        $cicilan_lainnya = $_POST['cicilan_lainnya'] ?? '';
        $aset_tanah_bangunan = $_POST['aset_tanah_bangunan'] ?? '';
        $aset_kendaraan = $_POST['aset_kendaraan'] ?? '';
        $aset_elektronik = $_POST['aset_elektronik'] ?? '';
        $aset_lainnya = $_POST['aset_lainnya'] ?? '';
        $kewajiban_hutang_bank = $_POST['kewajiban_hutang_bank'] ?? '';
        $kewajiban_hutang_perorangan = $_POST['kewajiban_hutang_perorangan'] ?? '';
        $kewajiban_hutang_lainnya = $_POST['kewajiban_hutang_lainnya'] ?? '';
    }

    // Clean rupiah values by removing 'Rp. ' and '.' and ',' and converting to plain numbers
    // Pastikan ini diterapkan ke semua input nominal
    $nominal_pengajuan = preg_replace('/[^0-9]/', '', $nominal_pengajuan);
    $gaji_pokok = preg_replace('/[^0-9]/', '', $gaji_pokok);
    $tanggungan_keluarga = preg_replace('/[^0-9]/', '', $tanggungan_keluarga); // Ini perlu juga dibersihkan
    $biaya_pendidikan = preg_replace('/[^0-9]/', '', $biaya_pendidikan);
    $biaya_listrik_air = preg_replace('/[^0-9]/', '', $biaya_listrik_air);
    $biaya_telp_internet = preg_replace('/[^0-9]/', '', $biaya_telp_internet);
    $biaya_transportasi = preg_replace('/[^0-9]/', '', $biaya_transportasi);
    $biaya_makan_minum = preg_replace('/[^0-9]/', '', $biaya_makan_minum);
    $biaya_lainnya = preg_replace('/[^0-9]/', '', $biaya_lainnya);
    $pendapatan_lain = preg_replace('/[^0-9]/', '', $pendapatan_lain);
    $cicilan_bank_lain = preg_replace('/[^0-9]/', '', $cicilan_bank_lain);
    $cicilan_lainnya = preg_replace('/[^0-9]/', '', $cicilan_lainnya);
    $aset_tanah_bangunan = preg_replace('/[^0-9]/', '', $aset_tanah_bangunan);
    $aset_kendaraan = preg_replace('/[^0-9]/', '', $aset_kendaraan);
    $aset_elektronik = preg_replace('/[^0-9]/', '', $aset_elektronik);
    $aset_lainnya = preg_replace('/[^0-9]/', '', $aset_lainnya);
    $kewajiban_hutang_bank = preg_replace('/[^0-9]/', '', $kewajiban_hutang_bank);
    $kewajiban_hutang_perorangan = preg_replace('/[^0-9]/', '', $kewajiban_hutang_perorangan);
    $kewajiban_hutang_lainnya = preg_replace('/[^0-9]/', '', $kewajiban_hutang_lainnya);


    // Create data string, including all fields in a consistent order
    // Urutan ini harus cocok dengan ekspektasi admin_panel.php
    $data_to_save = [
        $cabang,
        $marketing,
        $nama_anggota,
        $alamat,
        $jenis_pembiayaan,
        $nominal_pengajuan, // Index 5
        $jenis_pengajuan,   // Index 6 (BARU)
        $skip_analysis,     // Index 7
        // Conditional Usaha fields (Index 8, 9, 10)
        $jenis_usaha,
        $lama_usaha,
        $jumlah_karyawan,
        // Conditional Pegawai fields (Index 11, 12, 13, 14)
        $nama_instansi,
        $posisi_pekerjaan,
        $lama_bekerja,
        $gaji_pokok,
        // Analysis fields (Index 15 dan seterusnya)
        $tanggungan_keluarga,
        $biaya_pendidikan,
        $biaya_listrik_air,
        $biaya_telp_internet,
        $biaya_transportasi,
        $biaya_makan_minum,
        $biaya_lainnya,
        $pendapatan_lain,
        $cicilan_bank_lain,
        $cicilan_lainnya,
        $aset_tanah_bangunan,
        $aset_kendaraan,
        $aset_elektronik,
        $aset_lainnya,
        $kewajiban_hutang_bank,
        $kewajiban_hutang_perorangan,
        $kewajiban_hutang_lainnya
    ];

    // Path to the data file
    $file_path = 'data/data.txt'; // Pastikan path ini benar relatif dari process_form.php

    // Append data to the file
    $result = file_put_contents($file_path, implode('|', $data_to_save) . PHP_EOL, FILE_APPEND | LOCK_EX);

    if ($result !== false) {
        // Redirect back to the form or a success page
        header('Location: index.php?status=success');
        exit();
    } else {
        // Handle error
        header('Location: index.php?status=error');
        exit();
    }
} else {
    // Not a POST request, redirect or show an error
    header('Location: index.php');
    exit();
}
?>