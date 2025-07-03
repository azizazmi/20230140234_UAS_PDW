<?php
session_start();
require_once '../config.php';

// pastikan asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

$pageTitle = "Manajemen Modul";
$activePage = "modul";

// ambil semua modul
$sql = "SELECT modul.*, praktikum.nama AS praktikum_nama FROM modul
        JOIN praktikum ON modul.praktikum_id = praktikum.id
        ORDER BY praktikum.id, modul.id";
$result = $conn->query($sql);

require_once 'templates/header.php';
?>

<div class="bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Manajemen Modul</h1>
    <a href="modul_tambah.php" class="bg-green-500 text-white px-4 py-2 rounded mb-4 inline-block hover:bg-green-600">+ Tambah Modul</a>

    <table class="w-full border mt-4">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2 border">Praktikum</th>
                <th class="p-2 border">Judul Modul</th>
                <th class="p-2 border">File Materi</th>
                <th class="p-2 border">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td class='p-2 border'>".htmlspecialchars($row['praktikum_nama'])."</td>
                        <td class='p-2 border'>".htmlspecialchars($row['judul'])."</td>
                        <td class='p-2 border'><a href='../uploads/".htmlspecialchars($row['file_materi'])."' class='text-blue-600 underline'>Download</a></td>
                        <td class='p-2 border'>
                            <a href='modul_edit.php?id=".$row['id']."' class='bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600'>Edit</a>
                            <a href='modul_hapus.php?id=".$row['id']."' class='bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600' onclick='return confirm(\"Yakin hapus?\")'>Hapus</a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='4' class='text-center p-4'>Belum ada modul</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php
require_once 'templates/footer.php';
?>
