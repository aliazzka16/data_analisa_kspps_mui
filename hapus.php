<?php
// admin/hapus.php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php'); // Redirect ke halaman login jika belum login
    exit;
}

$file = '../data/data.txt';
$id = $_GET['id'] ?? -1;

if ($id !== -1 && file_exists($file)) {
    $lines = @file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); // Gunakan @ untuk menyembunyikan warning

    if ($lines === false) {
        header("Location: admin_panel.php?status=error&message=file_read_error");
        exit;
    }

    // Check if the ID exists and is valid
    if (isset($lines[$id])) {
        // Get the path to the document file if it exists (index 50/110)
        $data_to_delete = explode('|', $lines[$id]);
        $dokumen_filename = $data_to_delete[110] ?? ''; // Pastikan indeks dokumen yang benar
        $upload_dir = '../uploads/'; // Path ke direktori uploads relatif dari hapus.php

        // Delete the document file if it exists and is within the allowed directory
        if (!empty($dokumen_filename)) {
            // Gunakan basename untuk mencegah path traversal (misal: ../../file.txt)
            $dokumen_filename_clean = basename($dokumen_filename);
            $full_path_to_delete = $upload_dir . $dokumen_filename_clean;

            // Verifikasi bahwa file yang akan dihapus berada di dalam direktori upload yang diizinkan
            // realpath() akan menyelesaikan semua .. dan symbolic links
            if (file_exists($full_path_to_delete) && realpath($full_path_to_delete) !== false && strpos(realpath($full_path_to_delete), realpath($upload_dir)) === 0) {
                if (!unlink($full_path_to_delete)) {
                    error_log("Failed to delete document file: " . $full_path_to_delete);
                    // Anda bisa menambahkan pesan error ke pengguna jika penghapusan dokumen gagal
                    // header("Location: admin_panel.php?status=error&message=document_delete_failed");
                    // exit;
                }
            } else {
                // Log jika ada upaya menghapus file yang tidak ada atau di luar direktori yang diizinkan
                error_log("Attempted to delete non-existent or out-of-bounds file: " . $full_path_to_delete);
            }
        }

        // Remove the line from the array
        unset($lines[$id]);

        // Re-index the array if necessary (not strictly needed for file_put_contents but good practice)
        $lines = array_values($lines);

        // Write the updated data back to the file
        if (file_put_contents($file, implode(PHP_EOL, $lines)) !== false) {
            header("Location: admin_panel.php?status=deleted");
            exit;
        } else {
            error_log("Failed to write data.txt after deletion for ID: " . $id);
            header("Location: admin_panel.php?status=error&message=file_write_error");
            exit;
        }
    }
}

// Redirect back to admin panel with an error if ID was invalid or file not found initially
header("Location: admin_panel.php?status=error&message=invalid_id_or_file_not_found");
exit;