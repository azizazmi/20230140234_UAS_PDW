<?php
session_start();
require_once '../config.php';

// pastikan user asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

$pageTitle = "Tambah Mata Praktikum";
$activePage = "praktikum";

// proses tambah data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi']);

    if (empty($nama)) {
        $error = "Nama praktikum tidak boleh kosong.";
    } else {
        $stmt = $conn->prepare("INSERT INTO praktikum (nama, deskripsi) VALUES (?, ?)");
        $stmt->bind_param("ss", $nama, $deskripsi);
        if ($stmt->execute()) {
            header("Location: praktikum.php?status=added");
            exit();
        } else {
            $error = "Gagal menambahkan praktikum.";
        }
        $stmt->close();
    }
}

require_once 'templates/header.php';
?>

<div class="bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Tambah Mata Praktikum</h1>

    <?php if (!empty($error)) { echo "<div class='text-red-600 mb-4'>$error</div>"; } ?>

    <form method="post" class="space-y-4">
        <div>
            <label class="block mb-1">Nama Praktikum</label>
            <input type="text" name="nama" class="border rounded p-2 w-full" required>
        </div>
        <div>
            <label class="block mb-1">Deskripsi</label>
            <textarea name="deskripsi" class="border rounded p-2 w-full" rows="4"></textarea>
        </div>
        <div>
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Simpan</button>
            <a href="praktikum.php" class="ml-2 text-gray-600 hover:underline">Batal</a>
        </div>
    </form>
</div>

<?php
require_once 'templates/footer.php';
?>
