<?php
session_start();
require_once '../config.php';

// pastikan asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

// pastikan ada id modul
if (!isset($_GET['id'])) {
    header("Location: modul.php");
    exit();
}

$id = intval($_GET['id']);

// hapus data modul
$stmt = $conn->prepare("DELETE FROM modul WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    header("Location: modul.php?status=deleted");
    exit();
} else {
    echo "Gagal menghapus data.";
}
$stmt->close();
$conn->close();
?>
