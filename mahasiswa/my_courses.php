<?php
session_start();
require_once '../config.php';

// pastikan mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

$pageTitle = "Praktikum Saya";
$activePage = "my_courses";

// ambil semua praktikum yang sudah didaftarkan mahasiswa ini
$sql = "SELECT p.*, pr.nama AS praktikum_nama, pr.deskripsi 
        FROM pendaftaran p
        JOIN praktikum pr ON p.praktikum_id = pr.id
        WHERE p.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

require_once 'templates/header_mahasiswa.php';
?>

<div class="bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Praktikum Saya</h1>

    <?php if ($result->num_rows > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="bg-blue-50 p-4 rounded shadow hover:shadow-lg transition">
                    <h2 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($row['praktikum_nama']); ?></h2>
                    <p class="text-gray-700 mb-4"><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                    <a href="detail_praktikum.php?id=<?php echo $row['praktikum_id']; ?>" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Lihat Detail</a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="text-gray-600">Kamu belum mendaftar praktikum apapun.</p>
    <?php endif; ?>
</div>

<?php
require_once 'templates/footer_mahasiswa.php';
?>
