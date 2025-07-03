<?php
session_start();
require_once '../config.php';

// pastikan mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

// pastikan form terisi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $modul_id = intval($_POST['modul_id']);
    $praktikum_id = intval($_POST['praktikum_id']);
    $user_id = $_SESSION['user_id'];

    // cek id pendaftaran
    $cek = $conn->prepare("SELECT id FROM pendaftaran WHERE praktikum_id = ? AND user_id = ?");
    $cek->bind_param("ii", $praktikum_id, $user_id);
    $cek->execute();
    $cek->bind_result($pendaftaran_id);
    $cek->fetch();
    $cek->close();

    if (!$pendaftaran_id) {
        die("Kamu belum terdaftar di praktikum ini.");
    }

    // validasi file
    $fileName = $_FILES["file"]["name"];
    $fileTmp = $_FILES["file"]["tmp_name"];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $allowed = ["pdf", "docx"];
    if (!in_array($fileExt, $allowed)) {
        die("Hanya file PDF atau DOCX yang diizinkan.");
    }

    // rename file biar unik
    $newName = uniqid() . "_" . basename($fileName);
    $targetPath = "../uploads/" . $newName;

    if (move_uploaded_file($fileTmp, $targetPath)) {
        // simpan ke tabel laporan
        $stmt = $conn->prepare("INSERT INTO laporan (pendaftaran_id, modul_id, file_laporan, status) VALUES (?, ?, ?, 'pending')");
        $stmt->bind_param("iis", $pendaftaran_id, $modul_id, $newName);
        if ($stmt->execute()) {
            header("Location: detail_praktikum.php?id=$praktikum_id&status=uploaded");
            exit();
        } else {
            echo "Gagal menyimpan ke database.";
        }
        $stmt->close();
    } else {
        echo "Gagal upload file.";
    }
}

$conn->close();
?>
