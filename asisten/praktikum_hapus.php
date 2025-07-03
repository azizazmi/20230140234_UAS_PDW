<?php
session_start();
require_once '../config.php';

// pastikan user asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

// pastikan ada id praktikum
if (!isset($_GET['id'])) {
    header("Location: praktikum.php");
    exit();
}

$id = intval($_GET['id']);

// hapus praktikum
$stmt = $conn->prepare("DELETE FROM praktikum WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    header("Location: praktikum.php?status=deleted");
    exit();
} else {
    echo "Gagal menghapus data.";
}
$stmt->close();
$conn->close();
?>
