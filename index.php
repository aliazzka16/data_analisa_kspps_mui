<?php
// index.php hanya akan menampilkan form
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Analisa Pembiayaan KSPPS MUI</title>
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
        /* Tambahan style untuk placeholder agar lebih jelas */
        .form-control::placeholder {
            color: #ccc;
            opacity: 1; /* Firefox */
        }
        /* Style untuk menyesuaikan kolom input di mobile */
        @media (max-width: 767.98px) {
            .form-section {
                padding: 1rem;
            }
            .input-group-text {
                width: 60px; /* Lebih kecil di mobile */
            }
            /* Sesuaikan lebar kolom untuk Omset/HPP/Operasional di mobile */
            .row.g-2 > [class*="col-md-"] {
                flex: 0 0 auto;
                width: 100%;
                margin-bottom: 0.5rem; /* Tambahkan sedikit spasi antar kolom */
            }
            /* Hapus margin bawah di kolom terakhir dalam baris */
            .row.g-2 > [class*="col-md-"]:last-child {
                margin-bottom: 0;
            }
            .row.g-2 > .col-md-2 {
                display: none !important; /* Sembunyikan 'x' di mobile jika terlalu sempit */
            }
        }
        /* Tambahan style untuk tombol lokasi */
        .input-group .btn-location {
            height: calc(2.25rem + 2px); /* Menyamakan tinggi dengan input form-control */
        }
    </style>
</head>
<body class="bg-light py-4">
    <div class="container">
        <h2 class="text-center mb-4">Form Analisa Pembiayaan KSPPS MUI</h2>
        <a href="admin/admin_panel.php" class="btn btn-secondary mb-3">Lihat Data Tersimpan</a>

        <form action="simpan.php" method="POST" enctype="multipart/form-data">
            <div class="form-section shadow-sm">
                <h5>Data Umum</h5>
                <div class="mb-3">
                    <label for="cabang" class="form-label">Cabang</label>
                    <input type="text" class="form-control" id="cabang" name="cabang" required>
                </div>
                <div class="mb-3">
                    <label for="marketing" class="form-label">Marketing</label>
                    <input type="text" class="form-control" id="marketing" name="marketing" required>
                </div>
                <div class="mb-3">
                    <label for="anggota" class="form-label">Nama Anggota</label>
                    <input type="text" class="form-control" id="anggota" name="anggota" required>
                </div>
                 <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Masukkan alamat atau klik tombol lokasi" required>
                        <button class="btn btn-outline-secondary btn-location" type="button" id="getLocationBtn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                                <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
                            </svg>
                            Kirim Lokasi
                        </button>
                        </div>
                    <small id="locationHelp" class="form-text text-muted">Akan mengisi alamat dengan koordinat lokasi saat ini.</small>
                </div>
                <div class="mb-3">
    <label class="form-label">Jenis Pembiayaan</label><br>
    <input type="radio" name="jenis_pembiayaan" value="di_bawah_20jt" id="pembiayaan_20jt"> Pembiayaan di bawah Rp 20 Juta<br>
    <input type="radio" name="jenis_pembiayaan" value="di_atas_20jt" id="pembiayaan_atas_20jt" checked> Pembiayaan di atas Rp 20 Juta
  </div>
                <div class="mb-3">
                    <label for="nominal_pengajuan" class="form-label">Nominal Pengajuan</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="nominal_pengajuan" name="nominal_pengajuan" value="0" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="jenis_pengajuan" class="form-label">Jenis Pengajuan</label>
                    <select class="form-select" id="jenis_pengajuan" name="jenis_pengajuan" required>
                        <option value="">Pilih Jenis Pengajuan</option>
                        <option value="Reguler">Reguler</option>
                        <option value="Musiman">Musiman</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="jenis_usaha" class="form-label">Jenis Usaha</label>
                    <select class="form-select" id="jenis_usaha" name="jenis_usaha" onchange="toggleJenis()" required>
                        <option value="">Pilih Jenis Usaha</option>
                        <option value="pegawai">Pegawai</option>
                        <option value="usaha">Usaha</option>
                        <option value="usaha_dan_pegawai">Usaha & Pegawai</option>
                    </select>
                </div>
            </div>

            <div class="form-section shadow-sm" id="pegawai_section" style="display: none;">
                <h5>Pendapatan Pegawai</h5>
                <div class="mb-3">
                    <label for="gaji" class="form-label">Gaji</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="gaji" name="gaji" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="total_tunjangan" class="form-label">Total Tunjangan</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="total_tunjangan" name="total_tunjangan" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="total_gaji_tunjangan" class="form-label">Total Gaji + Tunjangan</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="total_gaji_tunjangan" name="total_gaji_tunjangan" readonly value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="biaya_pokok" class="form-label">Biaya Pokok</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="biaya_pokok" name="biaya_pokok" value="0">
                    </div>
                </div>
            </div>

            <div class="form-section shadow-sm" id="usaha_section" style="display: none;">
                <h5>Pendapatan Usaha</h5>
                <div class="mb-3">
                    <label for="jenis_usaha_uraian" class="form-label">Uraian Jenis Usaha</label>
                    <input type="text" class="form-control" id="jenis_usaha_uraian" name="jenis_usaha_uraian" placeholder="Contoh: Toko Sembako, Bengkel, dll.">
                </div>
                
                <h6 class="mt-4">Omset per Bulan</h6>
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <input type="text" class="form-control omset-text" id="omset_text1_1" name="omset_text1_1" placeholder="Item Omset" value="">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control omset-text" id="omset_text2_1" name="omset_text2_1" placeholder="Uraian Omset" value="">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" class="form-control rupiah omset-nominal" id="omset_nominal_1" name="omset_nominal_1" placeholder="Nominal Omset" value="0">
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <input type="text" class="form-control omset-text" id="omset_text1_2" name="omset_text1_2" placeholder="Item Omset" value="">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control omset-text" id="omset_text2_2" name="omset_text2_2" placeholder="Uraian Omset" value="">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" class="form-control rupiah omset-nominal" id="omset_nominal_2" name="omset_nominal_2" placeholder="Nominal Omset" value="0">
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <input type="text" class="form-control omset-text" id="omset_text1_3" name="omset_text1_3" placeholder="Item Omset" value="">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control omset-text" id="omset_text2_3" name="omset_text2_3" placeholder="Uraian Omset" value="">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" class="form-control rupiah omset-nominal" id="omset_nominal_3" name="omset_nominal_3" placeholder="Nominal Omset" value="0">
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <input type="text" class="form-control omset-text" id="omset_text1_4" name="omset_text1_4" placeholder="Item Omset" value="">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control omset-text" id="omset_text2_4" name="omset_text2_4" placeholder="Uraian Omset" value="">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" class="form-control rupiah omset-nominal" id="omset_nominal_4" name="omset_nominal_4" placeholder="Nominal Omset" value="0">
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <input type="text" class="form-control omset-text" id="omset_text1_5" name="omset_text1_5" placeholder="Item Omset" value="">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control omset-text" id="omset_text2_5" name="omset_text2_5" placeholder="Uraian Omset" value="">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" class="form-control rupiah omset-nominal" id="omset_nominal_5" name="omset_nominal_5" placeholder="Nominal Omset" value="0">
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <input type="text" class="form-control omset-text" id="omset_text1_6" name="omset_text1_6" placeholder="Item Omset" value="">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control omset-text" id="omset_text2_6" name="omset_text2_6" placeholder="Uraian Omset" value="">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" class="form-control rupiah omset-nominal" id="omset_nominal_6" name="omset_nominal_6" placeholder="Nominal Omset" value="0">
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control omset-text" id="omset_text1_7" name="omset_text1_7" placeholder="Item Omset" value="">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control omset-text" id="omset_text2_7" name="omset_text2_7" placeholder="Uraian Omset" value="">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" class="form-control rupiah omset-nominal" id="omset_nominal_7" name="omset_nominal_7" placeholder="Nominal Omset" value="0">
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="omset_total" class="form-label">Total Omset</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="omset_total" name="omset_total" readonly value="0">
                    </div>
                </div>

                <h6 class="mt-4">HPP (Harga Pokok Penjualan)</h6>
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <input type="text" class="form-control hpp-text" id="hpp_text1_1" name="hpp_text1_1" placeholder="Item HPP" value="">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control hpp-text" id="hpp_text2_1" name="hpp_text2_1" placeholder="Uraian HPP" value="">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" class="form-control rupiah hpp-nominal" id="hpp_nominal_1" name="hpp_nominal_1" placeholder="Nominal HPP" value="0">
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <input type="text" class="form-control hpp-text" id="hpp_text1_2" name="hpp_text1_2" placeholder="Item HPP" value="">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control hpp-text" id="hpp_text2_2" name="hpp_text2_2" placeholder="Uraian HPP" value="">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" class="form-control rupiah hpp-nominal" id="hpp_nominal_2" name="hpp_nominal_2" placeholder="Nominal HPP" value="0">
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <input type="text" class="form-control hpp-text" id="hpp_text1_3" name="hpp_text1_3" placeholder="Item HPP" value="">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control hpp-text" id="hpp_text2_3" name="hpp_text2_3" placeholder="Uraian HPP" value="">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" class="form-control rupiah hpp-nominal" id="hpp_nominal_3" name="hpp_nominal_3" placeholder="Nominal HPP" value="0">
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <input type="text" class="form-control hpp-text" id="hpp_text1_4" name="hpp_text1_4" placeholder="Item HPP" value="">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control hpp-text" id="hpp_text2_4" name="hpp_text2_4" placeholder="Uraian HPP" value="">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" class="form-control rupiah hpp-nominal" id="hpp_nominal_4" name="hpp_nominal_4" placeholder="Nominal HPP" value="0">
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <input type="text" class="form-control hpp-text" id="hpp_text1_5" name="hpp_text1_5" placeholder="Item HPP" value="">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control hpp-text" id="hpp_text2_5" name="hpp_text2_5" placeholder="Uraian HPP" value="">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" class="form-control rupiah hpp-nominal" id="hpp_nominal_5" name="hpp_nominal_5" placeholder="Nominal HPP" value="0">
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <input type="text" class="form-control hpp-text" id="hpp_text1_6" name="hpp_text1_6" placeholder="Item HPP" value="">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control hpp-text" id="hpp_text2_6" name="hpp_text2_6" placeholder="Uraian HPP" value="">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" class="form-control rupiah hpp-nominal" id="hpp_nominal_6" name="hpp_nominal_6" placeholder="Nominal HPP" value="0">
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control hpp-text" id="hpp_text1_7" name="hpp_text1_7" placeholder="Item HPP" value="">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control hpp-text" id="hpp_text2_7" name="hpp_text2_7" placeholder="Uraian HPP" value="">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" class="form-control rupiah hpp-nominal" id="hpp_nominal_7" name="hpp_nominal_7" placeholder="Nominal HPP" value="0">
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="hpp_total" class="form-label">Total HPP</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="hpp_total" name="hpp_total" readonly value="0">
                    </div>
                </div>

                <h6 class="mt-4">Biaya Operasional</h6>
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <input type="text" class="form-control operasional-text" id="operasional_text1_1" name="operasional_text1_1" placeholder="Item Operasional" value="">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control operasional-text" id="operasional_text2_1" name="operasional_text2_1" placeholder="Uraian Operasional" value="">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" class="form-control rupiah operasional-nominal" id="operasional_nominal_1" name="operasional_nominal_1" placeholder="Nominal Operasional" value="0">
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <input type="text" class="form-control operasional-text" id="operasional_text1_2" name="operasional_text1_2" placeholder="Item Operasional" value="">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control operasional-text" id="operasional_text2_2" name="operasional_text2_2" placeholder="Uraian Operasional" value="">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" class="form-control rupiah operasional-nominal" id="operasional_nominal_2" name="operasional_nominal_2" placeholder="Nominal Operasional" value="0">
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <input type="text" class="form-control operasional-text" id="operasional_text1_3" name="operasional_text1_3" placeholder="Item Operasional" value="">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control operasional-text" id="operasional_text2_3" name="operasional_text2_3" placeholder="Uraian Operasional" value="">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" class="form-control rupiah operasional-nominal" id="operasional_nominal_3" name="operasional_nominal_3" placeholder="Nominal Operasional" value="0">
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <input type="text" class="form-control operasional-text" id="operasional_text1_4" name="operasional_text1_4" placeholder="Item Operasional" value="">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control operasional-text" id="operasional_text2_4" name="operasional_text2_4" placeholder="Uraian Operasional" value="">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" class="form-control rupiah operasional-nominal" id="operasional_nominal_4" name="operasional_nominal_4" placeholder="Nominal Operasional" value="0">
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <input type="text" class="form-control operasional-text" id="operasional_text1_5" name="operasional_text1_5" placeholder="Item Operasional" value="">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control operasional-text" id="operasional_text2_5" name="operasional_text2_5" placeholder="Uraian Operasional" value="">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" class="form-control rupiah operasional-nominal" id="operasional_nominal_5" name="operasional_nominal_5" placeholder="Nominal Operasional" value="0">
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <input type="text" class="form-control operasional-text" id="operasional_text1_6" name="operasional_text1_6" placeholder="Item Operasional" value="">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control operasional-text" id="operasional_text2_6" name="operasional_text2_6" placeholder="Uraian Operasional" value="">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" class="form-control rupiah operasional-nominal" id="operasional_nominal_6" name="operasional_nominal_6" placeholder="Nominal Operasional" value="0">
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control operasional-text" id="operasional_text1_7" name="operasional_text1_7" placeholder="Item Operasional" value="">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control operasional-text" id="operasional_text2_7" name="operasional_text2_7" placeholder="Uraian Operasional" value="">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" class="form-control rupiah operasional-nominal" id="operasional_nominal_7" name="operasional_nominal_7" placeholder="Nominal Operasional" value="0">
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="operasional_total" class="form-label">Total Operasional</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="operasional_total" name="operasional_total" readonly value="0">
                    </div>
                </div>
            </div>

            <div class="form-section shadow-sm">
                <h5>Pengeluaran Rumah Tangga</h5>
                <div class="mb-3">
                    <label for="makan_minum" class="form-label">Makan Minum</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="makan_minum" name="makan_minum" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="anak" class="form-label">Anak</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="anak" name="anak" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="pendidikan" class="form-label">Pendidikan</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="pendidikan" name="pendidikan" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="listrik" class="form-label">Listrik</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="listrik" name="listrik" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="air" class="form-label">Air</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="air" name="air" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="transport" class="form-label">Transport</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="transport" name="transport" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="pengeluaran_lain_rt" class="form-label">Lain-lain (Pengeluaran Rumah Tangga)</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="pengeluaran_lain_rt" name="pengeluaran_lain_rt" value="0">
                    </div>
                </div>
            </div>

            <div class="form-section shadow-sm">
                <h5>Pengeluaran Angsuran/Arisan/Iuran</h5>
                <div class="mb-3">
                    <label for="angsuran1" class="form-label">Angsuran 1</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="angsuran1" name="angsuran1" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="bpjs" class="form-label">BPJS</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="bpjs" name="bpjs" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="arisan" class="form-label">Arisan</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="arisan" name="arisan" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="iuran" class="form-label">Iuran</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="iuran" name="iuran" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="angsuran_lain" class="form-label">Lain-lain (Angsuran)</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="angsuran_lain" name="angsuran_lain" value="0">
                    </div>
                </div>
            </div>
<div id="bagian_aset_kewajiban">
    <h5>Aset Lancar</h5>
            <div class="form-section shadow-sm" id="aset_section"> <h5>Aset Lancar</h5>
                <div class="mb-3">
                    <label for="kas_tunai" class="form-label">Kas Tunai</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="kas_tunai" name="kas_tunai" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="kas_bank" class="form-label">Kas Bank</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="kas_bank" name="kas_bank" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="piutang" class="form-label">Piutang</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="piutang" name="piutang" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="persediaan" class="form-label">Persediaan</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="persediaan" name="persediaan" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="emas" class="form-label">Emas</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="emas" name="emas" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="surat_berharga" class="form-label">Surat Berharga</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="surat_berharga" name="surat_berharga" value="0">
                    </div>
                </div>
            </div>

            <div class="form-section shadow-sm" id="aset_tetap_section">
                <h5>Aset Tetap</h5>
                <div class="mb-3">
                    <label for="mobil" class="form-label">Mobil</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="mobil" name="mobil" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="motor" class="form-label">Motor</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="motor" name="motor" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="rumah" class="form-label">Rumah</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="rumah" name="rumah" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="tanah" class="form-label">Tanah</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="tanah" name="tanah" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="gudang" class="form-label">Gudang</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="gudang" name="gudang" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="kantor" class="form-label">Kantor</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="kantor" name="kantor" value="0">
                    </div>
                </div>
            </div>

            <div class="form-section shadow-sm" id="kewajiban_section"> <h5>Kewajiban</h5>
                <h6>Kewajiban Lancar</h6>
                <div class="mb-3">
                    <label for="dp_diterima" class="form-label">DP Diterima</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="dp_diterima" name="dp_diterima" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="biaya_harus_bayar" class="form-label">Biaya yang Harus Dibayar</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="biaya_harus_bayar" name="biaya_harus_bayar" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="dll_kewajiban_lancar" class="form-label">Lain-lain (Kewajiban Lancar)</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="dll_kewajiban_lancar" name="dll_kewajiban_lancar" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="total_kewajiban_lancar" class="form-label">Total Kewajiban Lancar</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="total_kewajiban_lancar" name="total_kewajiban_lancar" readonly value="0">
                    </div>
                </div>

                <h6 class="mt-4">Kewajiban Tetap</h6>
                <div class="mb-3">
                    <label for="modal_pinjaman" class="form-label">Modal Pinjaman</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="modal_pinjaman" name="modal_pinjaman" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="modal_bank" class="form-label">Modal Bank</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="modal_bank" name="modal_bank" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="total_kewajiban_tetap" class="form-label">Total Kewajiban Tetap</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="total_kewajiban_tetap" name="total_kewajiban_tetap" readonly value="0">
                    </div>
                </div>

                <h6 class="mt-4">Total Keseluruhan Kewajiban</h6>
                <div class="mb-3">
                    <label for="total_keseluruhan_kewajiban" class="form-label">Total Keseluruhan Kewajiban (Lancar + Tetap)</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="total_keseluruhan_kewajiban" name="total_keseluruhan_kewajiban" readonly value="0">
                    </div>
                </div>
            </div>
            </div>

            <div class="form-section shadow-sm">
                <h5>Angsuran KSPPS MUI</h5>
                <div class="mb-3">
                    <label for="pengeluaran_kspps" class="form-label">Angsuran KSPPS MUI</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah" id="pengeluaran_kspps" name="pengeluaran_kspps" value="0">
                    </div>
                </div>
            </div>

            <div class="form-section shadow-sm">
                <h5>Dokumen Pendukung</h5>
                <div class="mb-3">
                    <label for="dokumen" class="form-label">Upload Dokumen (PDF, JPG, PNG)</label>
                    <input class="form-control" type="file" id="dokumen" name="dokumen" accept=".pdf, .jpg, .jpeg, .png">
                    <small class="form-text text-muted">Ukuran maksimal 5MB.</small>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100">Simpan Data</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
  const pembiayaan20jt = document.getElementById("pembiayaan_20jt");
  const pembiayaanAtas20jt = document.getElementById("pembiayaan_atas_20jt");
  const targetDiv = document.getElementById("bagian_aset_kewajiban");

  function toggleBagian() {
    if (pembiayaan20jt.checked) {
      targetDiv.style.display = "none";
    } else {
      targetDiv.style.display = "block";
    }
  }

  pembiayaan20jt.addEventListener("change", toggleBagian);
  pembiayaanAtas20jt.addEventListener("change", toggleBagian);

  toggleBagian(); // Saat halaman pertama dimuat
});
        // Fungsi untuk memformat input menjadi format Rupiah
        function formatRupiah(angka, prefix) {
            let number_string = String(angka).replace(/[^,\d]/g, '').toString();
            number_string = number_string.replace(/^0+(?!$)/, ''); // Hapus nol di depan kecuali jika satu-satunya angka adalah nol
            if (number_string === '') {
                return '';
            }
            let split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
        }

        // Fungsi untuk membersihkan format Rupiah menjadi angka murni
        function cleanRupiah(rupiah) {
            // Menghapus 'Rp. ' dan semua titik, lalu mengganti koma dengan titik untuk float
            return parseInt(rupiah.replace(/[^0-9]/g, '')) || 0;
        }

        // Otomatis format input rupiah saat diketik
        const rupiahInputs = document.querySelectorAll('.rupiah');
        rupiahInputs.forEach(input => {
            input.addEventListener('keyup', function(e) {
                this.value = formatRupiah(this.value, 'Rp. ');
            });
            input.addEventListener('blur', function() {
                if (this.value === '' || cleanRupiah(this.value) === 0) {
                    this.value = formatRupiah('0', 'Rp. ');
                }
            });
            // Pastikan nilai awal di-format saat halaman dimuat
            if (input.value !== '') {
                input.value = formatRupiah(cleanRupiah(input.value), 'Rp. ');
            }
        });

        // Hitung Otomatis untuk Pendapatan Pegawai
        const gajiInput = document.getElementById('gaji');
        const tunjanganInput = document.getElementById('total_tunjangan');
        const totalGajiTunjanganInput = document.getElementById('total_gaji_tunjangan');

        function hitungTotalPendapatanPegawai() {
            const gaji = cleanRupiah(gajiInput.value);
            const tunjangan = cleanRupiah(tunjanganInput.value);
            const total = gaji + tunjangan;
            totalGajiTunjanganInput.value = formatRupiah(total, 'Rp. ');
        }
        gajiInput.addEventListener('keyup', hitungTotalPendapatanPegawai);
        tunjanganInput.addEventListener('keyup', hitungTotalPendapatanPegawai);
        gajiInput.addEventListener('blur', hitungTotalPendapatanPegawai);
        tunjanganInput.addEventListener('blur', hitungTotalPendapatanPegawai);


        // Hitung Otomatis untuk Omset, HPP, Operasional Usaha
        const omsetNominalInputs = document.querySelectorAll('.omset-nominal');
        const omsetTotal = document.getElementById('omset_total');

        const hppNominalInputs = document.querySelectorAll('.hpp-nominal');
        const hppTotal = document.getElementById('hpp_total');

        const operasionalNominalInputs = document.querySelectorAll('.operasional-nominal');
        const operasionalTotal = document.getElementById('operasional_total');

        function hitungTotal(inputs, totalElement) {
            let total = 0;
            inputs.forEach(input => {
                total += cleanRupiah(input.value);
            });
            totalElement.value = formatRupiah(total, 'Rp. ');
        }

        omsetNominalInputs.forEach(input => {
            input.addEventListener('keyup', () => hitungTotal(omsetNominalInputs, omsetTotal));
            input.addEventListener('blur', () => hitungTotal(omsetNominalInputs, omsetTotal));
        });

        hppNominalInputs.forEach(input => {
            input.addEventListener('keyup', () => hitungTotal(hppNominalInputs, hppTotal));
            input.addEventListener('blur', () => hitungTotal(hppNominalInputs, hppTotal));
        });

        operasionalNominalInputs.forEach(input => {
            input.addEventListener('keyup', () => hitungTotal(operasionalNominalInputs, operasionalTotal));
            input.addEventListener('blur', () => hitungTotal(operasionalNominalInputs, operasionalTotal));
        });


        // Hitung Otomatis untuk Kewajiban
        const dpDiterimaInput = document.getElementById('dp_diterima');
        const biayaHarusBayarInput = document.getElementById('biaya_harus_bayar');
        const dllKewajibanLancarInput = document.getElementById('dll_kewajiban_lancar');
        const totalKewajibanLancarInput = document.getElementById('total_kewajiban_lancar');

        const modalPinjamanInput = document.getElementById('modal_pinjaman');
        const modalBankInput = document.getElementById('modal_bank');
        const totalKewajibanTetapInput = document.getElementById('total_kewajiban_tetap');
        
        const totalKeseluruhanKewajibanInput = document.getElementById('total_keseluruhan_kewajiban');

        function hitungTotalKewajibanLancar() {
            const dpDiterima = cleanRupiah(dpDiterimaInput.value);
            const biayaHarusBayar = cleanRupiah(biayaHarusBayarInput.value);
            const dllKewajibanLancar = cleanRupiah(dllKewajibanLancarInput.value);
            const total = dpDiterima + biayaHarusBayar + dllKewajibanLancar;
            totalKewajibanLancarInput.value = formatRupiah(total, 'Rp. ');
            hitungTotalKeseluruhanKewajiban();
        }

        function hitungTotalKewajibanTetap() {
            const modalPinjaman = cleanRupiah(modalPinjamanInput.value);
            const modalBank = cleanRupiah(modalBankInput.value);
            const total = modalPinjaman + modalBank;
            totalKewajibanTetapInput.value = formatRupiah(total, 'Rp. ');
            hitungTotalKeseluruhanKewajiban();
        }

        function hitungTotalKeseluruhanKewajiban() {
            const totalLancar = cleanRupiah(totalKewajibanLancarInput.value);
            const totalTetap = cleanRupiah(totalKewajibanTetapInput.value);
            totalKeseluruhanKewajibanInput.value = formatRupiah(totalLancar + totalTetap, 'Rp. ');
        }

        dpDiterimaInput.addEventListener('keyup', hitungTotalKewajibanLancar);
        biayaHarusBayarInput.addEventListener('keyup', hitungTotalKewajibanLancar);
        dllKewajibanLancarInput.addEventListener('keyup', hitungTotalKewajibanLancar);
        dpDiterimaInput.addEventListener('blur', hitungTotalKewajibanLancar);
        biayaHarusBayarInput.addEventListener('blur', hitungTotalKewajibanLancar);
        dllKewajibanLancarInput.addEventListener('blur', hitungTotalKewajibanLancar);

        modalPinjamanInput.addEventListener('keyup', hitungTotalKewajibanTetap);
        modalBankInput.addEventListener('keyup', hitungTotalKewajibanTetap);
        modalPinjamanInput.addEventListener('blur', hitungTotalKewajibanTetap);
        modalBankInput.addEventListener('blur', hitungTotalKewajibanTetap);


        // NEW: Variabel dan fungsi untuk mengelola tampilan section aset dan kewajiban berdasarkan checkbox
        const skipAnalysisCheckbox = document.getElementById('skip_analysis');
        const kewajibanSection = document.getElementById('kewajiban_section');
        const asetSection = document.getElementById('aset_section'); // Menargetkan semua aset, lancar dan tetap

        function resetSectionInputs(sectionElement) {
            sectionElement.querySelectorAll('.rupiah').forEach(input => {
                input.value = formatRupiah('0', 'Rp. ');
            });
            sectionElement.querySelectorAll('input:not(.rupiah)').forEach(input => {
                input.value = '';
            });
        }

        function toggleAnalysisSections() {
            const isSkipped = skipAnalysisCheckbox.checked;

            if (isSkipped) {
                kewajibanSection.style.display = 'none';
                asetSection.style.display = 'none';
                // Reset values in these hidden sections to 0 for submission
                resetSectionInputs(kewajibanSection);
                resetSectionInputs(asetSection);
            } else {
                kewajibanSection.style.display = 'block';
                asetSection.style.display = 'block';
                // If not skipped, re-initialize calculations for these sections
                hitungTotalKewajibanLancar();
                hitungTotalKewajibanTetap();
                hitungTotalKeseluruhanKewajiban();
            }
        }
                
        // Fungsi untuk menampilkan/menyembunyikan bagian jenis usaha
        function toggleJenis() {
            const val = document.getElementById("jenis_usaha").value;
            const pegawaiSection = document.getElementById("pegawai_section");
            const usahaSection = document.getElementById("usaha_section");

            pegawaiSection.style.display = "none";
            usahaSection.style.display = "none";

            // Reset nilai input di bagian yang disembunyikan
            pegawaiSection.querySelectorAll('input').forEach(input => {
                if (input.type === 'text' && input.classList.contains('rupiah')) {
                    input.value = formatRupiah('0', 'Rp. ');
                } else if (input.type === 'text') {
                    input.value = '';
                }
            });
            usahaSection.querySelectorAll('input').forEach(input => {
                if (input.type === 'text' && input.classList.contains('rupiah')) {
                    input.value = formatRupiah('0', 'Rp. ');
                } else if (input.type === 'text') {
                    input.value = '';
                }
            });
            
            if (val === "pegawai") {
                pegawaiSection.style.display = "block";
            } else if (val === "usaha") {
                usahaSection.style.display = "block";
            } else if (val === "usaha_dan_pegawai") {
                pegawaiSection.style.display = "block";
                usahaSection.style.display = "block";
            }

            // Panggil kembali semua hitungan setelah section ditampilkan/disembunyikan
            hitungTotalPendapatanPegawai();
            hitungTotal(omsetNominalInputs, omsetTotal); // Perbarui ini
            hitungTotal(hppNominalInputs, hppTotal);     // Perbarui ini
            hitungTotal(operasionalNominalInputs, operasionalTotal); // Perbarui ini

            toggleAnalysisSections(); // Pastikan section aset/kewajiban disesuaikan juga
        }

        // Fungsi untuk mendapatkan lokasi pengguna
        document.getElementById('getLocationBtn').addEventListener('click', function()
        {
            const alamatInput = document.getElementById('alamat');
            const locationHelp = document.getElementById('locationHelp');
            locationHelp.textContent = 'Mencari lokasi...'; // Pesan status
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;
                // URL Google Maps dengan koordinat
                const googleMapsLink = `http://maps.google.com/?q=${lat},${lon}`; // Perbaiki format link Google Maps
                alamatInput.value = `Lat: ${lat}, Long: ${lon} (Lihat di Maps: ${googleMapsLink})`;
                locationHelp.innerHTML = `<span class="text-success">Lokasi berhasil didapatkan.</span> <a href="${googleMapsLink}" target="_blank">Lihat di Google Maps</a>`;
            },
            function(error) {
                // Menangani error jika ada
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        locationHelp.textContent = "Pengguna menolak permintaan Geolocation.";
                        break;
                    case error.POSITION_UNAVAILABLE:
                        locationHelp.textContent = "Informasi lokasi tidak tersedia.";
                        break;
                    case error.TIMEOUT:
                        locationHelp.textContent = "Waktu permintaan lokasi habis.";
                        break;
                    case error.UNKNOWN_ERROR:
                        locationHelp.textContent = "Terjadi kesalahan yang tidak diketahui.";
                        break;
                }
                locationHelp.classList.remove('text-success');
                locationHelp.classList.add('text-danger');
                alamatInput.value = ''; // Kosongkan input jika ada error
            },
            {
                enableHighAccuracy: true, // Mencoba mendapatkan lokasi seakurat mungkin
                timeout: 10000, // Batas waktu 10 detik
                maximumAge: 0 // Tidak menggunakan lokasi cache
            }
        );
    } else {
        locationHelp.textContent = "Geolocation tidak didukung oleh browser ini.";
        locationHelp.classList.remove('text-success');
        locationHelp.classList.add('text-danger');
    }
});

        // Panggil fungsi toggleJenis() dan toggleAnalysisSections() saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Format awal semua input rupiah
            rupiahInputs.forEach(input => {
                if (input.value !== '') {
                    input.value = formatRupiah(cleanRupiah(input.value), 'Rp. ');
                } else {
                    input.value = formatRupiah('0', 'Rp. ');
                }
            });
            toggleJenisSections(); // Ini akan mengatur tampilan awal section usaha/pegawai
            toggleAnalysisSections(); // Ini akan mengatur tampilan awal section aset/kewajiban
            
            // Tambahkan event listener untuk checkbox "skip_analysis"
            const skipAnalysisCheckbox = document.getElementById('skip_analysis'); // Deklarasi ini penting!
            skipAnalysisCheckbox.addEventListener('change', toggleAnalysisSections);
        });
    </script>
</body>
</html>