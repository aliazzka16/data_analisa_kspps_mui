<?php
// admin/admin_panel.php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location:login.php'); // Redirect ke halaman login jika belum login
    exit;
}

$file_path = '../data/data.txt'; // Path ke file data.txt

// Pastikan file data.txt ada
if (!file_exists($file_path)) {
    // Jika tidak ada, buat file kosong
    file_put_contents($file_path, "");
}

// Baca data dari file tanpa di-reverse
$data_lines_original = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel KSPPS MUI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }
        .table-responsive {
            margin-top: 20px;
        }
        .btn-group-sm > .btn, .btn-sm {
            padding: .25rem .5rem;
            font-size: .75rem;
            line-height: 1.5;
            border-radius: .2rem;
        }
        /* Mengurangi padding cell agar lebih padat di layar kecil */
        .table-sm th, .table-sm td {
            padding: 0.3rem;
        }
        /* Memastikan kolom aksi tidak terlalu lebar */
        .table-bordered th:last-child, .table-bordered td:last-child {
            width: 200px; /* Sesuaikan lebar sesuai kebutuhan */
            min-width: 150px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3 class="mb-4">Data Analisa Pembiayaan Anggota</h3>
        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control" onkeyup="filterTable()" placeholder="Cari berdasarkan Cabang, Marketing, Nama Anggota, Alamat, atau Jenis Pengajuan...">
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm" id="dataTable">
                <thead class="table-primary">
                    <tr>
                        <th>No.</th>
                        <th>Cabang</th>
                        <th>Marketing</th>
                        <th>Nama Anggota</th>
                        <th>Alamat</th>
                        <th>Jenis Pengajuan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; // Mulai nomor dari 1
                    foreach ($data_lines_original as $original_index => $line):
                        $data = explode('|', $line);
                        // Perhatikan indeks data dari data.txt:
                        // data[0]: Cabang
                        // data[1]: Marketing
                        // data[2]: Nama Anggota
                        // data[3]: Alamat
                        // data[4]: Nominal Pengajuan (tidak ditampilkan di sini)
                        // data[5]: Jenis Usaha (tidak ditampilkan di sini)
                        // data[6]: Jenis Pembiayaan (Reguler/Musiman)
                        $index = $original_index; // Gunakan index asli untuk link detail/edit
                    ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($data[0] ?? '') ?></td>
                            <td><?= htmlspecialchars($data[1] ?? '') ?></td>
                            <td><?= htmlspecialchars($data[2] ?? '') ?></td>
                            <td><?= htmlspecialchars($data[3] ?? '') ?></td>
                            <td><?= htmlspecialchars($data[6] ?? '') ?></td> <td>
                                <div class="btn-group" role="group" aria-label="Aksi">
                                    <a href="detail.php?id=<?= $index ?>&type=reguler" class="btn btn-info btn-sm">Detail Reguler</a>
                                    <a href="detail.php?id=<?= $index ?>&type=musiman" class="btn btn-info btn-sm">Detail Musiman</a>
                                    <a href="edit.php?id=<?= $index ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i> Edit</a>
                                    <a href="hapus.php?id=<?= $index ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');"><i class="bi bi-trash"></i> Hapus</a>
                                    <a href="export_excel.php?id=<?= $index ?>" class="btn btn-success btn-sm"><i class="bi bi-file-earmark-excel"></i> Excel</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filterTable() {
            const input = document.getElementById("searchInput").value.toLowerCase();
            const rows = document.querySelectorAll("#dataTable tbody tr");
            rows.forEach(row => {
                // Kolom HTML yang ditampilkan (indeks row.cells):
                // [0] No.
                // [1] Cabang
                // [2] Marketing
                // [3] Nama Anggota
                // [4] Alamat
                // [5] Jenis Pengajuan
                // [6] Aksi

                const cabang = row.cells[1].textContent.toLowerCase();
                const marketing = row.cells[2].textContent.toLowerCase();
                const anggota = row.cells[3].textContent.toLowerCase();
                const alamat = row.cells[4].textContent.toLowerCase();
                const jenisPengajuan = row.cells[5].textContent.toLowerCase(); // Tetap di index 5

                if (cabang.includes(input) || marketing.includes(input) || anggota.includes(input) || alamat.includes(input) || jenisPengajuan.includes(input)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }
    </script>
</body>
</html>