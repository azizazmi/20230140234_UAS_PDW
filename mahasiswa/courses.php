<?php
// mulai session
session_start();

// koneksi ke DB
require_once '../config.php';

// ambil semua praktikum
$sql = "SELECT * FROM praktikum";
$result = $conn->query($sql);

// set page aktif
$pageTitle = "Daftar Praktikum";
$activePage = "courses";

// panggil header
require_once 'templates/header_mahasiswa.php';
?>

<div class="bg-white p-6 rounded-xl shadow-lg">
    <h1 class="text-2xl font-bold mb-4">Daftar Mata Praktikum</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
        ?>
        <div class="border rounded-lg p-4 shadow hover:shadow-md transition">
            <h2 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($row['nama']); ?></h2>
            <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($row['deskripsi']); ?></p>

            <?php
            // cek apakah user sudah daftar praktikum ini
            $praktikum_id = $row['id'];
            $user_id = $_SESSION['user_id'];
            $cek = $conn->prepare("SELECT id FROM pendaftaran WHERE praktikum_id = ? AND user_id = ?");
            $cek->bind_param("ii", $praktikum_id, $user_id);
            $cek->execute();
            $cek->store_result();

            if ($cek->num_rows > 0) {
                echo '<button class="bg-green-500 text-white px-4 py-2 rounded" disabled>Sudah Terdaftar</button>';
            } else {
                echo '<a href="daftar_praktikum.php?id='.$praktikum_id.'" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Daftar</a>';
            }

            $cek->close();
            ?>
        </div>
        <?php
            }
        } else {
            echo "<p class='text-gray-500'>Belum ada praktikum tersedia.</p>";
        }
        ?>

    </div>
</div>

<?php
require_once 'templates/footer_mahasiswa.php';
?>
