<?php
session_start();
require_once '../config.php';

// pastikan asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

$pageTitle = "Laporan Masuk";
$activePage = "laporan";

// ambil semua laporan
$sql = "SELECT laporan.*, 
            modul.judul AS modul_judul, 
            praktikum.nama AS praktikum_nama,
            users.nama AS mahasiswa_nama
        FROM laporan
        JOIN modul ON laporan.modul_id = modul.id
        JOIN praktikum ON modul.praktikum_id = praktikum.id
        JOIN pendaftaran ON laporan.pendaftaran_id = pendaftaran.id
        JOIN users ON pendaftaran.user_id = users.id
        ORDER BY laporan.created_at DESC";
$result = $conn->query($sql);

require_once 'templates/header.php';
?>

<div class="bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Laporan Mahasiswa</h1>

    <table class="w-full border mt-4 text-sm">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2 border">Mahasiswa</th>
                <th class="p-2 border">Praktikum</th>
                <th class="p-2 border">Modul</th>
                <th class="p-2 border">File</th>
                <th class="p-2 border">Status</th>
                <th class="p-2 border">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td class='p-2 border'>".htmlspecialchars($row['mahasiswa_nama'])."</td>
                        <td class='p-2 border'>".htmlspecialchars($row['praktikum_nama'])."</td>
                        <td class='p-2 border'>".htmlspecialchars($row['modul_judul'])."</td>
                        <td class='p-2 border'>
                            <a href='../uploads/".htmlspecialchars($row['file_laporan'])."' class='text-blue-600 underline'>Download</a>
                        </td>
                        <td class='p-2 border'>".htmlspecialchars($row['status'])."</td>
                        <td class='p-2 border'>
                            <a href='laporan_nilai.php?id=".$row['id']."' class='bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600'>Nilai</a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='text-center p-4'>Belum ada laporan masuk.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php
require_once 'templates/footer.php';
?>
