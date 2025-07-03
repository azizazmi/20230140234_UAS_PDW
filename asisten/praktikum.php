<?php
session_start();
require_once '../config.php';

// pastikan user asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

$pageTitle = "Kelola Mata Praktikum";
$activePage = "praktikum";

// ambil data praktikum
$result = $conn->query("SELECT * FROM praktikum ORDER BY id DESC");

require_once 'templates/header.php';
?>

<div class="bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Kelola Mata Praktikum</h1>
    <a href="praktikum_tambah.php" class="bg-green-500 text-white px-4 py-2 rounded mb-4 inline-block hover:bg-green-600">+ Tambah Praktikum</a>

    <table class="w-full border mt-4">
        <thead>
            <tr class="bg-gray-200 text-left">
                <th class="p-2 border">Nama Praktikum</th>
                <th class="p-2 border">Deskripsi</th>
                <th class="p-2 border">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td class='p-2 border'>".htmlspecialchars($row['nama'])."</td>
                        <td class='p-2 border'>".htmlspecialchars($row['deskripsi'])."</td>
                        <td class='p-2 border'>
                            <a href='praktikum_edit.php?id=".$row['id']."' class='bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600'>Edit</a>
                            <a href='praktikum_hapus.php?id=".$row['id']."' onclick='return confirm(\"Yakin hapus?\")' class='bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600'>Hapus</a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='3' class='p-4 text-center'>Belum ada data praktikum.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php
require_once 'templates/footer.php';
?>
