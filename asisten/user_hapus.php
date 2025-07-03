<?php
session_start();
require_once '../config.php';

// pastikan asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

// cek id user
if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$id = intval($_GET['id']);

// mencegah asisten menghapus dirinya sendiri
if ($id == $_SESSION['user_id']) {
    die("Anda tidak dapat menghapus akun Anda sendiri.");
}

// hapus user
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    header("Location: users.php?status=deleted");
    exit();
} else {
    echo "Gagal menghapus user.";
}
$stmt->close();
$conn->close();
?>
