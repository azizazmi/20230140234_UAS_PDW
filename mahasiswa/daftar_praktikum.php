<?php
session_start();
require_once '../config.php';

// Pastikan user mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

// Pastikan ada parameter id
if (!isset($_GET['id'])) {
    header("Location: courses.php");
    exit();
}

$praktikum_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// cek apakah sudah pernah daftar
$cek = $conn->prepare("SELECT id FROM pendaftaran WHERE praktikum_id = ? AND user_id = ?");
$cek->bind_param("ii", $praktikum_id, $user_id);
$cek->execute();
$cek->store_result();

if ($cek->num_rows > 0) {
    // sudah daftar
    header("Location: my_courses.php?status=already");
    exit();
}
$cek->close();

// insert ke tabel pendaftaran
$stmt = $conn->prepare("INSERT INTO pendaftaran (praktikum_id, user_id) VALUES (?, ?)");
$stmt->bind_param("ii", $praktikum_id, $user_id);

if ($stmt->execute()) {
    header("Location: my_courses.php?status=success");
    exit();
} else {
    echo "Terjadi kesalahan. Silakan coba lagi.";
}

$stmt->close();
$conn->close();
?>
