<?php
// admin/auth/functions.php

session_start(); // Mulai sesi di setiap file yang membutuhkan otentikasi

define('USER_FILE', __DIR__ . '/../../data/users/users.txt'); // Path ke file users.txt

// Fungsi untuk membuat hash password
function hashPassword(string $password): string
{
    return password_hash($password, PASSWORD_BCRYPT);
}

// Fungsi untuk memverifikasi password
function verifyPassword(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}

// Fungsi untuk login pengguna
function loginUser(string $username, string $password): bool
{
    $users = file(USER_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($users as $user_line) {
        list($stored_username, $stored_password_hash, $role) = explode('|', $user_line);
        if ($username === $stored_username && verifyPassword($password, $stored_password_hash)) {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $stored_username;
            $_SESSION['role'] = $role; // Simpan role pengguna di sesi
            return true;
        }
    }
    return false;
}

// Fungsi untuk memeriksa apakah pengguna sudah login
function isLoggedIn(): bool
{
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Fungsi untuk memeriksa role pengguna
function getUserRole(): string
{
    return $_SESSION['role'] ?? '';
}

// Fungsi untuk require login
function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: ' . getBaseUrl() . '/admin/auth/login.php');
        exit;
    }
}

// Fungsi untuk require role tertentu
function requireRole(string $required_role)
{
    if (!isLoggedIn() || getUserRole() !== $required_role) {
        header('Location: ' . getBaseUrl() . '/admin/auth/login.php?error=unauthorized');
        exit;
    }
}

// Fungsi untuk menambahkan pengguna baru (hanya oleh admin)
function addUser(string $username, string $password, string $role = 'user'): bool
{
    // Pastikan username tidak kosong dan password tidak kosong
    if (empty($username) || empty($password)) {
        return false;
    }

    $users = file(USER_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($users as $user_line) {
        list($stored_username, ,) = explode('|', $user_line);
        if ($username === $stored_username) {
            return false; // Username already exists
        }
    }

    $hashed_password = hashPassword($password);
    $new_user_line = "$username|$hashed_password|$role";
    file_put_contents(USER_FILE, $new_user_line . PHP_EOL, FILE_APPEND);
    return true;
}

// Fungsi untuk mendapatkan semua pengguna
function getAllUsers(): array
{
    if (!file_exists(USER_FILE)) {
        return [];
    }
    $users = file(USER_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $user_list = [];
    foreach ($users as $user_line) {
        list($username, , $role) = explode('|', $user_line); // Tidak mengambil hash password
        $user_list[] = ['username' => $username, 'role' => $role];
    }
    return $user_list;
}

// Fungsi untuk menghapus pengguna
function deleteUser(string $username_to_delete): bool
{
    if (empty($username_to_delete)) {
        return false;
    }

    $users = file(USER_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $new_users_lines = [];
    $user_found = false;
    foreach ($users as $user_line) {
        list($username, ,) = explode('|', $user_line);
        if ($username === $username_to_delete) {
            $user_found = true;
        } else {
            $new_users_lines[] = $user_line;
        }
    }

    if ($user_found) {
        file_put_contents(USER_FILE, implode(PHP_EOL, $new_users_lines) . PHP_EOL); // Tambahkan PHP_EOL di akhir
        return true;
    }
    return false;
}

// Fungsi untuk mendapatkan base URL agar redirect tidak error
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    // Cari posisi "/admin" dalam URI dan ambil bagian depannya
    $base_path = strstr($uri, '/admin', true);
    return "$protocol://$host$base_path";
}